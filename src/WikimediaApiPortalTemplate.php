<?php

namespace MediaWiki\Skin\WikimediaApiPortal;

use ExtensionRegistry;
use Linker;
use MediaWiki\MediaWikiServices;
use Message;
use OOUI\ButtonGroupWidget;
use OOUI\ButtonWidget;
use RawMessage;
use SpecialPage;
use Title;

class WikimediaApiPortalTemplate extends \BaseTemplate {
	// Personal url keys that will be allowed in the user menu
	private const PERSONAL_LINKS_ALLOWED_LIST = [ 'logout', 'uls' ];

	// Allowed page actions with config overrides
	private const PAGE_TOOLS_ALLOWED_LIST = [
		'views' => [
			 'edit' => [
				 'visible' => [ 'view' ],
				 'icon' => 'edit',
				 'group' => 'primary',
			 ],
		 ],
		'actions' => [
			'move' => [
				'visible' => [ 'view' ],
				'group' => 'secondary',

			],
			 'delete' => [
				'visible' => [ 'view' ],
				'flags' => 'destructive',
				'group' => 'secondary',
			 ],
		],
	 ];

	public function execute() {
		$this->setPersonalUrls();
		$this->setSubpageDisplayTitle();

		$componentParser = new TemplateParser( __DIR__ . '/../components' );
		$componentParser->enableRecursivePartials( true );

		echo $componentParser->processTemplate( 'default', [
			'html-headelement' => $this->get( 'headelement' ),
			'html-trail' => $this->getTrail(),
			'html-title' => $this->get( 'title' ),
			'html-subtitle' => $this->get( 'subtitle' ),
			'html-undelete' => $this->get( 'undelete' ) ?: null,
			'html-siteNotice' => $this->data['sitenotice'],
			'html-bodyContent' => $this->get( 'bodytext' ),
			'html-afterContent' => $this->get( 'dataAfterContent' ),
			'html-catlinks' => $this->get( 'catlinks' ),
			'html-disclaimer' => $this->getMsg( 'wikimediaapiportal-skin-disclaimer' )
				->inContentLanguage()->parseAsBlock(),
			'wmlinks' => $this->getWmLinks(),
			'jumptocontent' => $this->getMsg( 'wikimediaapiportal-jumpto-content' )->text(),

			'args-logo' => $this->getLogoArgs(),
			'args-navmenu' => $this->getNavmenuArgs(),
			'args-pagenav' => $this->getPageNavArgs(),
			'args-usermenu' => $this->getUserMenuArgs(),
			'args-pageTools' => $this->getPageToolsArgs(),
			'args-pageToolsMobile' => $this->getPageToolsMobileArgs(),
			'args-notification' => $this->getNotificationArgs(),
			'args-searchBar' => $this->getSearchBarArgs(),
			'args-footerInfo' => $this->getFooterInfoArgs(),
			'args-footerLinks' => $this->getFooterLinksArgs(),
		] );
	}

	/**
	 * Get correct return type
	 *
	 * @return Skin
	 */
	public function getSkin() {
		return $this->data['skin'];
	}

	/**
	 * @see components/Logo.mustache
	 * @return array
	 */
	private function getLogoArgs() : array {
		// Ignore wgLogo and 'logopath' from SkinTemplate
		$stylePath = $this->getSkin()->getConfig()->get( 'StylePath' );
		$logoPath = $stylePath . '/WikimediaApiPortal/resources/images/icon/wikimedia-black.svg';

		return [
			'logopath' => $logoPath,
			'sitename' => $this->get( 'sitename', '' ),
			'sitename-main' => Message::newFromKey( 'wikimediaapiportal-skin-site-name-main' )->text(),
			'sitename-sub' => Message::newFromKey( 'wikimediaapiportal-skin-site-name-sub' )->text(),
			'mainpage-href' => $this->get( 'nav_urls' )['mainpage']['href'] ?? '#',
			'logo-tooltip' => Linker::titleAttrib( 'p-logo' ),
		];
	}

	/**
	 * @see components/NavbarHorizontal.mustache
	 * @return array
	 */
	private function getNavmenuArgs() : array {
		$sidebar = $this->getSidebar( [
			'search' => false,
			'toolbox' => false,
			'languages' => false,
		] );

		$items = [];
		foreach ( $sidebar as $menuKey => $menu ) {
			/** @var array $menu See BaseTemplate::getSidebar */

			// Dropdown or single link
			if ( is_array( $menu['content'] ) && count( $menu['content'] ) > 1 ) {
				$subitems = [];
				$hasActive = false;
				foreach ( $menu['content'] as $key => $item ) {
					/** @var array $item See Skin::addToSidebarPlain */
					$hasActive = $hasActive || $this->getSkin()->isActiveLink( $item['href'] );
					// @phan-suppress-next-line SecurityCheck-DoubleEscaped
					$subitems[] = $this->getSkin()->makeListItem( $key, $item, [
						'tag' => 'div',
						'class' => 'nav-item',
						'link-class' => 'nav-link',
					] );
				}

				$items[] = [
					'isDropdown' => true,
					'hasActive' => $hasActive,
					'menuKey' => $menuKey,
					'id' => $menu['id'],
					'header' => $menu['header'],
					'items' => $subitems,
				];
			} else {
				$isActive = $this->getSkin()->isActiveLink( $menu['content'][0]['href'] );
				$items[] = [
					'isLink' => true,
					'isActive' => $isActive,
					'header' => $menu['header'],
					'href' => $menu['content'][0]['href'],
				];
			}
		}

		$mainPage = Title::newMainPage();
		return [
			'mainpage-href' => $mainPage->getLinkURL(),
			'mainpage-text' => $mainPage->getText(),
			'items' => $items,
		];
	}

	/**
	 * @see components/PageNav.mustache
	 * @return array|false
	 */
	private function getPageNavArgs() {
		$predefined = $this->getPredefinedNavStructure();
		if ( $predefined ) {
			return [ 'items' => $predefined ];
		}

		$title = $this->getSkin()->getTitle();
		if ( $title->isTalkPage() ) {
			$subjectNS = MediaWikiServices::getInstance()->getNamespaceInfo()
				->getSubject( $title->getNamespace() );
			$title = Title::makeTitleSafe( $subjectNS, $title->getDBkey() );
		}

		if ( !( $title && ( $title->isSubpage() || $title->hasSubpages() ) ) ) {
			return false;
		}

		$root = Title::makeTitleSafe( $title->getNamespace(), $title->getRootText() );
		if ( !$root ) {
			return false;
		}

		return [
			'items' => $this->getPageNav( $root ),
		];
	}

	/**
	 * Helper for self::getPageNavArgs
	 *
	 * @param Title $parent
	 * @return array|false
	 */
	private function getPageNav( Title $parent ) {
		$nav = [];
		$subpages = $parent->getSubpages();
		if ( !count( $subpages ) ) {
			return $nav;
		}
		$pageProps = MediaWikiServices::getInstance()->getPageProps();
		$defaultsort = $pageProps->getProperties( $subpages, 'defaultsort' );
		foreach ( $subpages as $page ) {
			if ( $page->getBaseText() !== $parent->getText() ) {
				// Not direct sub
				continue;
			}

			if ( $defaultsort && array_key_exists( $page->getArticleID(), $defaultsort ) ) {
				$key = $defaultsort[$page->getArticleID()];
			} else {
				$key = $page->getSubpageText();
			}
			$nav[$key] = [
				'isActive' => $this->getSkin()->isActiveTitle( $page ),
				'href' => $page->getLocalURL(),
				'text' => $page->getSubpageText(),
				'subpages' => $this->getPageNav( $page ) ?: false,
			];
		}
		ksort( $nav );
		return $nav;
	}

	/**
	 * Get page navigation hierarchy from predefined list
	 *
	 * @return array|null
	 */
	private function getPredefinedNavStructure() {
		$predefined = [];
		$this->getSkin()->addToSidebarPlain(
			$predefined,
			$this->getSkin()->msg(
				'wikimediaapiportal-skin-page-nav-predefines'
			)->inContentLanguage()->plain()
		);

		foreach ( $predefined as $root => $subpages ) {
			$currentURL = $this->getSkin()->getTitle()->getLocalURL();
			$matches = array_filter( $subpages, function ( $item ) use ( $currentURL ) {
				return $item['href'] === $currentURL;
			} );
			foreach ( $subpages as &$subpage ) {
				$subpage['isActive'] = $this->getSkin()->isActiveLink( $subpage['href'] );
				$subpage['subpages'] = false;
			}

			if ( !empty( $matches ) ) {
				return [ [
					'text' => $root,
					'href' => '#',
					'isActive' => true,
					'subpages' => $subpages
				] ];
			}
		}

		return null;
	}

	/**
	 * @see components/UserMenu.mustache
	 * @return array
	 */
	private function getUserMenuArgs() : array {
		$items = [];
		$hasActive = false;
		foreach ( $this->get( 'personal_urls' ) as $key => $item ) {
			/** @var array $item See Skin::addToSidebarPlain */
			$items[] = $this->getSkin()->makeListItem( $key, $item,
				[ 'tag' => 'div', 'link-class' => 'dropdown-item' ]
			);
		}

		$user = $this->getSkin()->getUser();
		return [
			'isAnon' => $user->isAnon(),
			'login-href' => SpecialPage::getTitleFor( 'Userlogin' )->getLocalURL( [
				'returnto' => $this->getSkin()->getTitle()
			] ),
			'login-label' => Message::newFromKey( 'wikimediaapiportal-skin-login-link-label' )->text(),
			'tools-label' => new RawMessage( $user->getName() ),
			'tools-items' => $items,
		];
	}

	/**
	 * @see components/PageTools.mustache
	 * @return array|false
	 */
	private function getPageToolsArgs() {
		if ( $this->getSkin()->getTitle()->isSpecialPage() ) {
			return false;
		}

		$requestedAction = $this->getSkin()->getRequestedAction();
		if ( $requestedAction === 'delete' ) {
			return false;
		}

		$primary = $this->getContentNavButtons( 'primary' );
		$secondary = $this->getContentNavButtons( 'secondary' );

		return [
			'html-discussionSwitch' => $this->getDiscussionSwitch(),
			'html-lastEdit' => $this->getLastEdit(),
			'html-primaryButtons' => new ButtonGroupWidget( [ 'items' => $primary ] ),
			'secondaryButtons' => $secondary,
		];
	}

	/**
	 * @see components/PageToolsMobile.mustache
	 * @return array|false
	 */
	private function getPageToolsMobileArgs() {
		if ( $this->getSkin()->getTitle()->isSpecialPage() ) {
			return false;
		}

		$buttons = $this->getContentNavButtons( 'all', [ 'framed' => true ] );

		return [
			'html-discussionSwitch' => $this->getDiscussionSwitch(),
			'html-lastEdit' => $this->getLastEdit(),
			'html-buttons' => new ButtonGroupWidget( [ 'items' => $buttons ] ),
		];
	}

	/**
	 * Helper for PageTools.
	 *
	 * @return ButtonWidget|string
	 */
	private function getDiscussionSwitch() {
		// See SkinTemplate::buildContentNavigationUrls
		$requestedAction = $this->getSkin()->getRequestedAction();
		$actions = $this->get( 'content_navigation', null );
		if ( !$this->getSkin()->getTitle()->isTalkPage() && $requestedAction === 'view' ) {
			if ( isset( $actions['namespaces']['talk'] ) ) {
				return $this->getButtonForContentAction( $actions['namespaces']['talk'], [
					'icon' => 'speechBubbles',
				] );
			}
		} else {
			if ( isset( $actions['namespaces']['main'] ) ) {
				return $this->getButtonForContentAction( $actions['namespaces']['main'], [
					'icon' => 'arrowPrevious',
					'label' => Message::newFromKey(
						'wikimediaapiportal-skin-return-to-page-label'
					)->text()
				] );
			}
		}
		return '';
	}

	/**
	 * Helper for PageTools.
	 *
	 * @return ButtonWidget|string
	 */
	private function getLastEdit() {
		$requestedAction = $this->getSkin()->getRequestedAction();
		if ( $requestedAction !== 'view' ) {
			return '';
		}
		if ( $this->getSkin()->getTitle()->isTalkPage() ) {
			return '';
		}
		// See SkinTemplate::buildContentNavigationUrls
		$actions = $this->get( 'content_navigation', null );
		if ( !isset( $actions['views']['history'] ) ) {
			return '';
		}
		$title = $this->getSkin()->getTitle();
		if ( !$title || !$title->exists() ) {
			return '';
		}
		$lastTouched = $title->getTouched();
		if ( $lastTouched === null ) {
			return '';
		}

		$formatted = $this->getSkin()->getLanguage()->userDate(
			$lastTouched,
			$this->getSkin()->getUser()
		);

		return $this->getButtonForContentAction( $actions['views']['history'], [
			'icon' => 'history',
			'label' => Message::newFromKey(
				'wikimediaapiportal-skin-updated-ts-label'
			)->params( $formatted )->text()
		] );
	}

	/**
	 * Get OOUI widgets for available content actions. Helper for PageTools.
	 *
	 * @param string $group One of 'primary', 'secondary', or 'all'
	 * @param array $oouiOptions
	 * @return ButtonWidget[]
	 */
	private function getContentNavButtons( $group, $oouiOptions = [] ) {
		if ( $this->getSkin()->getTitle()->isTalkPage() ) {
			return [];
		}
		$permissions = MediaWikiServices::getInstance()->getPermissionManager();
		$user = $this->getSkin()->getUser();
		if ( !$permissions->userHasRight( $user, 'edit-docs' ) ) {
			return [];
		}
		$requestedAction = $this->getSkin()->getRequestedAction();
		$actions = $this->get( 'content_navigation', null );
		if ( !$actions ) {
			return [];
		}
		$buttons = [];
		foreach ( $actions as $sectionKey => $section ) {
			foreach ( $section as $actionKey => $action ) {
				if ( !isset( self::PAGE_TOOLS_ALLOWED_LIST[$sectionKey][$actionKey] ) ) {
					continue;
				}

				$allowedListData = self::PAGE_TOOLS_ALLOWED_LIST[$sectionKey][$actionKey];
				if ( $group !== 'all' && $group !== $allowedListData['group'] ) {
					continue;
				}
				if ( !in_array( $requestedAction, $allowedListData['visible'] ) ) {
					continue;
				}

				$buttons[] = $this->getButtonForContentAction( $action, array_merge(
					$allowedListData,
					$oouiOptions
				) );
			}
		}

		return $buttons;
	}

	/**
	 * Helper for PageTools.
	 *
	 * @see SkinTemplate::buildContentNavigationUrls
	 * @param array $action Data from 'content_navigation' entry
	 * @param array $oouiOptions Override
	 * @return ButtonWidget
	 */
	private function getButtonForContentAction( $action, $oouiOptions = [] ) {
		return new ButtonWidget( $oouiOptions + [
			'id' => $action['id'],
			'href' => $action['href'],
			'label' => $action['text'],
			'framed' => false,
		] );
	}

	/**
	 * @see components/NotificationAlert.mustache
	 * @return array|false
	 */
	private function getNotificationArgs() {
		// Support: Echo extension

		if ( !$this->getSkin()->getUser()->isLoggedIn() ) {
			return false;
		}

		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			return false;
		}

		$data = $this->get( 'notification-alert' );
		if ( !$data ) {
			return false;
		}

		/** @var Message $actionMsg */
		$actionMsg = $data['text'];

		return [
			'hasCount' => $data['data']['counter-num'] > 0,
			'count' => $data['data']['counter-text'],
			'href' => $data['href'],
			'label' => $actionMsg->text(),
		];
	}

	/**
	 * @see components/SearchBar.mustache
	 * @return array
	 */
	private function getSearchBarArgs() {
		$clearButton = new ButtonWidget( [
			'name' => 'clear',
			'title' =>
				$this->getMsg(
					'wikimediaapiportal-skin-button-clear-search-label'
				)->text(),
			'classes' => [ 'wm-search-clear-button' ],
			'framed' => false,
			'icon' => 'close',
		] );

		return [
			'searchTitle' => $this->get( 'searchtitle' ),
			'searchButtonLabel' => $this->getMsg( 'searchbutton' )->text(),
			'searchButtonTooltip' => Linker::titleAttrib( 'search-fulltext' ),
			'searchPlaceholder' => $this->getMsg( 'searchsuggest-search' )->text(),
			'searchTooltip' => Linker::titleAttrib( 'search' ),
			'url-wgScript' => $this->get( 'wgScript' ),
			'html-clearButton' => $clearButton,
		];
	}

	/**
	 * @see components/FooterWikimediaInfo.mustache
	 * @return array
	 */
	private function getFooterInfoArgs() : array {
		return [
			'info' => $this->getMsg( 'wikimediaapiportal-skin-wikimedia-info' )
				->inContentLanguage()->plain(),
		];
	}

	/**
	 * @see components/FooterLinks.mustache
	 * @return array
	 */
	private function getFooterLinksArgs() : array {
		$groups = [];
		$this->getSkin()->addToSidebarPlain(
			$groups,
			$this->getSkin()->msg( 'FooterLinks' )->inContentLanguage()->plain()
		);

		$links = [];
		$first = true;
		foreach ( $groups as $header => $items ) {
			$links[$header] = [
				'isFirst' => $first,
				'items' => $items,
			];
			$first = false;
		}

		return [
			'links' => $links,
		];
	}

	private function setPersonalUrls() {
		if ( !$this->getSkin()->getUser()->isLoggedIn() ) {
			return;
		}
		if ( isset( $this->data['personal_urls']['notifications-alert'] ) ) {
			// We display notification badge separately
			$this->data['notification-alert'] = $this->data['personal_urls']['notifications-alert'];
		}

		// This behaviour may change later on, when other DevCenter functionality is implemented
		$devCenterTitle = Title::newFromText( 'Dashboard' );
		$filteredUrls = [
			'devcenter' => [
				'text' => $devCenterTitle->getText(),
				'href' => $devCenterTitle->getLocalURL(),
			]
		];
		foreach ( $this->data['personal_urls'] as $key => $data ) {
			if ( in_array( $key, self::PERSONAL_LINKS_ALLOWED_LIST ) ) {
				$filteredUrls[$key] = $data;
			}
		}

		$this->data['personal_urls'] = $filteredUrls;
	}

	private function getWmLinks() : array {
		// Maybe this should be handled differently? Configurable?
		return [
			'contact' => [
				'text' => Message::newFromKey(
					"wikimediaapiportal-skin-button-contact-label"
				)->text(),
				'href' => 'https://wikimediafoundation.org/about/contact/'
			],
			'about-wm' => [
				'text' => Message::newFromKey(
					"wikimediaapiportal-skin-button-about-wm-label"
				)->text(),
				'href' => 'https://wikimediafoundation.org/about/'
			],
		];
	}

	/**
	 * Set page title to the last subpage only, since the subpage structure
	 * will be shown in the navigation and bread crumbs
	 *
	 * Subpages will display only the last part of the page as the page title.
	 * This is done to allow for the site to be structured using subpages,
	 * while still keeping the page names that are display nice
	 */
	private function setSubpageDisplayTitle() {
		if ( !$this->getSkin()->isViewAction() ) {
			return;
		}
		$title = $this->getSkin()->getTitle();
		if (
			!MediaWikiServices::getInstance()->getNamespaceInfo()
				->hasSubpages( $title->getNamespace() )
		) {
			return;
		}
		if ( $title->isSubpage() ) {
			$newTitle = Title::makeTitle( $title->getNamespace(), $title->getSubpageText() );
			$this->set( 'title', $newTitle->getPrefixedText() );
		}
	}
}
