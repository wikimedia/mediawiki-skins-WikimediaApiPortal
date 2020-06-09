<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component\Footer;

use Html;
use Skins\Chameleon\Components\Component;

class FooterLinks extends Component {

	/**
	 * @inheritDoc
	 */
	public function getHtml() {
		$data = $this->getSkinTemplate()->get( 'wm_footer_links' );
		if ( !$data ) {
			return '';
		}

		$html = Html::openElement( 'div', [
			'class' => 'row wm-footer-link-container'
		] );

		$first = true;
		foreach ( $data as $header => $items ) {
			$classes = [ 'col' ];
			$classes[] = $first ? 'col-6' : 'col-3';
			$html .= Html::openElement( 'div', [
				'class' => implode( ' ', $classes )
			] );
			$first = false;
			$html .= $this->buildGroup( $header, $items );

			$html .= Html::closeElement( 'div' );
		}
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	private function buildGroup( $header, $items ) {
		$html = Html::openElement( 'div', [
			'class' => 'wm-footer-link-group'
		] );
		$html .= Html::openElement( 'div', [
			'class' => 'wm-footer-link-group-name'
		] );
		$html .= Html::element( 'h3', [], $header );
		$html .= Html::closeElement( 'div' );

		$html .= Html::openElement( 'div', [
			'class' => 'wm-footer-link-group-links'
		] );
		foreach( $items as $link ) {
			$html .= Html::element( 'a', [
				'class' => 'btn wm-button',
				'href' => $link['href'],
			], $link['text'] );
		}
		$html .= Html::closeElement( 'div' );
		$html .= Html::closeElement( 'div' );

		return $html;
	}

}
