<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component\Header;

use Html;
use MediaWiki\Skin\WikimediaApiPortal\Component\IconDropdown;
use Message;
use SpecialPage;

class UserMenu extends IconDropdown {

	public function getHtml() {
		if ( $this->getSkin()->getUser()->isAnon() ) {
			return $this->loginLink();
		}

		return parent::getHtml();
	}

	private function loginLink() {
		return Html::element( 'a', [
			'class' => 'btn wm-button wm-header-item',
			'href' => SpecialPage::getTitleFor( 'UserLogin' )->getLocalURL( [
				'returnto' => $this->getSkin()->getTitle()
			] )
		], Message::newFromKey( 'wikimediaapiportal-skin-login-link-label' )->escaped() );
	}

	protected function getCssClass() {
		return 'wm-personal-tools wm-header-item';
	}

	protected function getHtmlId() {
		return 'wm-personal-tools-trigger';
	}

	protected function getMainLabel() {
		return Message::newFromKey( 'wikimediaapiportal-skin-personal-tools-label' )->escaped();
	}

	protected function getItems() {
		return $this->getSkinTemplate()->get( 'personal_urls' );
	}

	protected function getDropdownCssClass() {
		return 'wm-dropdown-menu-auto  dropdown-menu-right';
	}

	protected function getIconClass() {
		return 'wm-personal-tools-icon';
	}
}
