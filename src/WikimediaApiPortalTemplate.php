<?php

namespace MediaWiki\Skin\WikimediaApiPortal;

use MediaWiki\MediaWikiServices;
use Message;
use Skins\Chameleon\ChameleonTemplate;
use Title;

class WikimediaApiPortalTemplate extends ChameleonTemplate {
	public function execute() {
		$this->data['availableLanguages'] = [ 'en', 'de', 'fr' ];
		// Here in case language will be retrieved differently
		$this->data['siteLanguage'] = $this->getSkin()->getConfig()->get( 'LanguageCode' );
		$this->data['activeTitleURL'] = $this->getSkin()->getTitle()->getLocalURL();
		$this->setPersonalUrls();
		$this->setLinks();

		if ( $this->getSkin()->getConfig()->get( 'WikimediaApiPortalSkinAdjustPageTitle' ) ) {
			$this->changeTitle();
		}

		parent::execute();
	}

	public function setPersonalUrls() {
		if ( !$this->getSkin()->getUser()->isLoggedIn() ) {
			return;
		}
		if ( isset( $this->data['personal_urls']['notifications-alert'] ) ) {
			// We display notification badge separately
			$this->data['notification-alert'] = $this->data['personal_urls']['notifications-alert'];
		}

		// This behaviour may change later on, when other DevCenter functionality is implemented
		$devCenterTitle = Title::newFromText( 'DevCenter' );
		$filteredUrls = [
			'devcenter' => [
				'text' => $devCenterTitle->getText(),
				'href' => $devCenterTitle->getLocalURL(),
			]
		];
		$allowedItems = $this->config->get( 'WikimediaApiPortalSkinAllowedPersonalUrls' );
		foreach ( $this->data['personal_urls'] as $key => $data ) {
			if ( in_array( $key, $allowedItems ) ) {
				$filteredUrls[$key] = $data;
			}
		}

		$this->data['personal_urls'] = $filteredUrls;
	}

	private function setLinks() {
		$this->setFooterLinks();

		// Maybe this should be handled differently? Configurable?
		$this->data['wm_links'] = [
			'contact' => [
				'text' => Message::newFromKey(
					"wikimediaapiportal-skin-button-contact-label"
				)->escaped(),
				'href' => 'https://wikimediafoundation.org/about/contact/'
			],
			'about_wm' => [
				'text' => Message::newFromKey(
					"wikimediaapiportal-skin-button-about-wm-label"
				)->escaped(),
				'href' => 'https://wikimediafoundation.org/about/'
			],
		];
	}

	private function setFooterLinks() {
		$this->parseLinks( 'wm_footer_links', 'FooterLinks' );
	}

	private function parseLinks( $key, $messageKey ) {
		$links = [];
		$this->getSkin()->addToSidebarPlain(
			$links, $this->getSkin()->msg( $messageKey )->inContentLanguage()->plain()
		);

		$this->data[$key] = $links;
	}

	/**
	 * Set page title to the last subpage only, since the subpage structure
	 * will be shown in the navigation and bread crumbs
	 */
	private function changeTitle() {
		$title = Title::newFromText( $this->data['title'] );
		if ( !$title ) {
			return;
		}
		if ( $this->getSkin()->getRequest()->getText( 'action', 'view' ) !== 'view' ) {
			return;
		}
		if (
			!MediaWikiServices::getInstance()->getNamespaceInfo()
				->hasSubpages( $title->getNamespace() )
		) {
			return;
		}
		if ( $title->isSubpage() ) {
			$bits = explode( '/', $title->getPrefixedText() );
			$newTitle = Title::makeTitle( $title->getNamespace(), array_pop( $bits ) );
			$this->set( 'title', $newTitle->getPrefixedText() );
		}
	}
}
