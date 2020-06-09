<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component\Header;

use Html;
use Message;
use Skins\Chameleon\Components\Logo as ChameleonLogo;

class Logo extends ChameleonLogo {

	protected function getLogo() {
		return parent::getLogo() . $this->getSiteLogoText();
	}

	private function getSiteLogoText() {
		$html = Html::openElement( 'div', [
			'class' => 'wm-site-name',
		] );
		$html .= Html::element( 'span', [
			'class'=> 'wm-site-name-main'
		], Message::newFromKey( 'wikimediaapiportal-skin-site-name-main' )->escaped() );
		$html .= Html::element( 'span', [
			'class'=> 'wm-site-name-sub'
		], Message::newFromKey( 'wikimediaapiportal-skin-site-name-sub' )->escaped() );
		$html .= Html::closeElement( 'div' );

		return $html;
	}
}
