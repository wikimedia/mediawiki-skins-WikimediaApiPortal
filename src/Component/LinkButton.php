<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use Html;
use Skins\Chameleon\Components\Component;

class LinkButton extends Component {
	public function getHtml() {
		$link = $this->getDomElement()->getAttribute( 'link' );
		$allLinks = $this->getSkinTemplate()->get( 'wm_links' );

		if ( $link && isset( $allLinks[$link] ) ) {
			return $this->getButton( $allLinks[$link]['href'], $allLinks[$link]['text'] );
		}

		return '';
	}

	/**
	 * @param string $href
	 * @param string $text
	 * @return string
	 */
	public function getButton( $href, $text ) {
		$classes = array_merge(
			[ 'btn', 'wm-button' ],
			$this->getDomElementClasses()
		);
		return Html::element( 'a', [
			'class' => implode( ' ', $classes ),
			'href' => $href,
		], $text );
	}

	/**
	 * @return array
	 */
	protected function getDomElementClasses() {
		$classString = $this->getClassString();
		$classes = [];
		if ( $classString ) {
			$classes = explode( ' ', trim( $classString ) );

		}
		if ( $this->isFramed() ) {
			$classes[] = 'wm-framed';
		}
		return $classes;
	}

	protected function isFramed() {
		$domElement = $this->getDomElement();
		if ( $domElement === null ) {
			return true;
		}
		$attribute = $domElement->getAttribute( 'framed' );

		return filter_var( $attribute, FILTER_VALIDATE_BOOLEAN );
	}
}
