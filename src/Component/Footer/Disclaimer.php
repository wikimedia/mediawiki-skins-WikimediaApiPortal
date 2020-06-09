<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component\Footer;

use Html;
use Skins\Chameleon\Components\Component;

class Disclaimer extends Component {

	/**
	 * @inheritDoc
	 */
	public function getHtml() {
		$html = Html::openElement( 'div', [
			'class' => 'wm-footer-disclaimer'
		] );
		$html .= $this->getSkin()->msg(
			'wikimediaapiportal-skin-disclaimer'
		)->inContentLanguage()->parseAsBlock();
		$html .= Html::closeElement( 'div' );

		return $html;
	}
}
