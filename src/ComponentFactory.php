<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */
namespace MediaWiki\Skin\WikimediaApiPortal;

use MediaWiki\Config\Config;
use MediaWiki\Config\ServiceOptions;
use MediaWiki\Linker\Linker;
use MediaWiki\Page\PageProps;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\Skin\WikimediaApiPortal\Component\ContentComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\FooterComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\LogoComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\MainComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\NavBarComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\NavMenuComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\NotificationAlertComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\PageToolsComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\PrimaryNavComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\SearchButtonComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\SearchFieldComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\SecondaryNavComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\UserMenuComponent;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\Title\NamespaceInfo;
use MediaWiki\Title\TitleFactory;
use Wikimedia\Message\IMessageFormatterFactory;
use Wikimedia\Message\MessageValue;

class ComponentFactory {
	public function __construct(
		private readonly Config $config,
		private readonly IMessageFormatterFactory $messageFormatterFactory,
		private readonly TitleFactory $titleFactory,
		private readonly SpecialPageFactory $specialPageFactory,
		private readonly NamespaceInfo $namespaceInfo,
		private readonly PageProps $pageProps,
		private readonly PermissionManager $permissionManager,
		private readonly ExtensionRegistry $extensionRegistry,
	) {
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return MainComponent
	 */
	public function createMainComponent(
		WikimediaApiPortalTemplate $template
	): MainComponent {
		$siteNotice = $template->data['sitenotice'];
		$logo = $this->createLogoComponent( $template );
		$navMenu = $this->createNavMenuComponent( $template );
		$navBar = $this->createNavBarComponent( $template );
		$secondaryNav = $this->createSecondaryNavComponent( $template );
		$userMenu = $this->createUserMenuComponent( $template );
		$notificationAlert = $this->createNotificationAlertComponent( $template );
		$searchField = $this->createSearchFieldComponent( $template );
		$searchButton = $this->createSearchButtonComponent( $template );
		$content = $this->createContentComponent( $template );
		$footer = $this->createFooterComponent( $template );
		return new MainComponent(
			$this->messageFormatterFactory,
			$template->getSkin(),
			$siteNotice,
			$logo,
			$navMenu,
			$navBar,
			$secondaryNav,
			$userMenu,
			$notificationAlert,
			$searchField,
			$searchButton,
			$content,
			$footer
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return ContentComponent
	 */
	private function createContentComponent(
		WikimediaApiPortalTemplate $template
	): ContentComponent {
		$title = $template->get( 'title' );
		$subtitle = $template->get( 'subtitle' ) ?: null;
		$undelete = $template->get( 'undelete' ) ?: null;
		$pageTools = $this->createPageToolsComponent( $template, false );
		$bodyContent = $template->get( 'bodytext' );
		$afterContent = $template->get( 'dataAfterContent' );
		$catlinks = $template->get( 'catlinks' );
		return new ContentComponent(
			$title,
			$subtitle,
			$undelete,
			$pageTools,
			$bodyContent,
			$afterContent,
			$catlinks
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return LogoComponent
	 */
	private function createLogoComponent(
		WikimediaApiPortalTemplate $template
	): LogoComponent {
		$skin = $template->getSkin();
		$href = $skin->getTitle()->isMainPage() ? '#' : $this->titleFactory->newMainPage()->getLocalURL();
		$tooltip = Linker::titleAttrib( 'p-logo' );
		return new LogoComponent(
			$this->messageFormatterFactory,
			$skin,
			$href,
			$tooltip
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @param string $id
	 * @return PrimaryNavComponent
	 */
	private function createPrimaryNavComponent(
		WikimediaApiPortalTemplate $template,
		string $id
	): PrimaryNavComponent {
		$skin = $template->getSkin();
		$title = $skin->getTitle();
		$sidebar = $template->getPrimaryNavSidebar();
		// @phan-suppress-next-next-line SecurityCheck-DoubleEscaped Unclear due to lack of documentation on
		// BaseTemplate::getSidebar().
		return new PrimaryNavComponent(
			$title,
			$id,
			$sidebar,
			$skin
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return SecondaryNavComponent
	 */
	private function createSecondaryNavComponent(
		WikimediaApiPortalTemplate $template
	): SecondaryNavComponent {
		$skin = $template->getSkin();
		$title = $skin->getTitle();
		return new SecondaryNavComponent(
			new ServiceOptions( SecondaryNavComponent::CONSTRUCTOR_OPTIONS, $this->config ),
			$this->messageFormatterFactory,
			$skin,
			$title,
			$this->namespaceInfo,
			$this->titleFactory,
			$this->specialPageFactory,
			$this->pageProps
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return NavBarComponent
	 */
	private function createNavBarComponent(
		WikimediaApiPortalTemplate $template
	): NavBarComponent {
		return new NavBarComponent(
			$this->createPrimaryNavComponent( $template, 'mw-navigation' )
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return NavMenuComponent
	 */
	private function createNavMenuComponent(
		WikimediaApiPortalTemplate $template
	): NavMenuComponent {
		$primaryNav = $this->createPrimaryNavComponent( $template, 'mw-navigation' );
		$secondaryNav = $this->createSecondaryNavComponent( $template );
		$pageToolsMobile = $this->createPageToolsComponent( $template, true );
		return new NavMenuComponent(
			$this->titleFactory,
			$primaryNav,
			$secondaryNav,
			$pageToolsMobile
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return UserMenuComponent
	 */
	private function createUserMenuComponent(
		WikimediaApiPortalTemplate $template
	): UserMenuComponent {
		$skin = $template->getSkin();
		$user = $skin->getUser();
		$title = $skin->getTitle();
		$personalUrls = $template->data['personal_urls'];
		return new UserMenuComponent(
			new ServiceOptions( UserMenuComponent::CONSTRUCTOR_OPTIONS, $this->config ),
			$this->messageFormatterFactory,
			$skin,
			$this->titleFactory,
			$this->specialPageFactory,
			$user,
			$title,
			$personalUrls
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @param bool $mobile
	 * @return PageToolsComponent
	 */
	private function createPageToolsComponent(
		WikimediaApiPortalTemplate $template,
		bool $mobile
	): PageToolsComponent {
		$skin = $template->getSkin();
		return new PageToolsComponent(
			$this->messageFormatterFactory,
			$skin,
			$this->permissionManager,
			$skin->getRequest()->getRawVal( 'action' ) ?? 'view',
			$template->get( 'content_navigation', null ),
			$mobile
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return NotificationAlertComponent
	 */
	private function createNotificationAlertComponent(
		WikimediaApiPortalTemplate $template
	): NotificationAlertComponent {
		$user = $template->getSkin()->getUser();
		if ( isset( $template->data['personal_urls']['notifications-alert'] ) ) {
			$notificationAlert = $template->data['personal_urls']['notifications-alert'];
		} else {
			$notificationAlert = [];
		}
		return new NotificationAlertComponent(
			$this->extensionRegistry,
			$user,
			$notificationAlert
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return SearchFieldComponent
	 */
	private function createSearchFieldComponent(
		WikimediaApiPortalTemplate $template
	): SearchFieldComponent {
		return new SearchFieldComponent(
			$this->messageFormatterFactory,
			$template->getSkin(),
			new ServiceOptions( SearchFieldComponent::CONSTRUCTOR_OPTIONS, $this->config )
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return SearchButtonComponent
	 */
	private function createSearchButtonComponent(
		WikimediaApiPortalTemplate $template
	): SearchButtonComponent {
		return new SearchButtonComponent(
			$this->messageFormatterFactory,
			$template->getSkin(),
			new ServiceOptions( SearchButtonComponent::CONSTRUCTOR_OPTIONS, $this->config )
		);
	}

	/**
	 * @param WikimediaApiPortalTemplate $template
	 * @return FooterComponent
	 */
	private function createFooterComponent(
		WikimediaApiPortalTemplate $template
	): FooterComponent {
		$skin = $template->getSkin();
		$groups = [];
		$skin->addToSidebarPlain(
			$groups,
			$this->messageFormatterFactory->getTextFormatter(
				$skin->getLanguage()->getCode()
			)->format( new MessageValue( 'wikimediaapiportal-skin-footer-links' ) )
		);
		return new FooterComponent(
			$this->messageFormatterFactory,
			$skin,
			$groups
		);
	}
}
