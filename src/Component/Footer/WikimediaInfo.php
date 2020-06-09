<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component\Footer;

use Html;
use Skins\Chameleon\Components\Component;

class WikimediaInfo extends Component {

	/**
	 * @inheritDoc
	 */
	public function getHtml() {
		$html = Html::openElement( 'div', [
			'class' => 'container wm-footer-wikimedia-info'
		] );
		$html .= Html::openElement( 'div', [
			'class' => 'row'
		] );
		$html .= $this->getImage();
		$html .= Html::closeElement( 'div' );
		$html .= Html::openElement( 'div', [
			'class' => 'row'
		] );
		$html .= $this->getInfoText();
		$html .= Html::closeElement( 'div' );
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	private function getImage() {
		return Html::element( 'div', [
			'class' => 'wmf-logo'
		] );
	}

	private function getInfoText() {
		return Html::element( 'span', [],
			$this->getSkin()->msg(
				'wikimediaapiportal-skin-wikimedia-info'
			)->inContentLanguage()->plain()
		);
	}

}
