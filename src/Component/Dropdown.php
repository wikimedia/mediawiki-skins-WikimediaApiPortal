<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use Html;
use Skins\Chameleon\Components\Component;
use Skins\Chameleon\IdRegistry;

abstract class Dropdown extends Component {

	public function getHtml() {
		$html = Html::openElement( 'div', [
			'class' => 'wm-dropdown ' . $this->getCssClass()
		] );
		$html .= Html::openElement( 'div', [
			'class' => 'dropdown'
		] );

		$html .= $this->getTrigger();

		$html .= Html::openElement( 'div', [
			'class' => 'dropdown-menu ' . $this->getDropdownCssClass(),
			'aria-labelledby' => IdRegistry::getRegistry()->getId( $this->getHtmlId() )
		] );

		foreach ( $this->getItems() as $id => $data ) {
			$html .= $this->getSkinTemplate()->makeListItem(
				$id,
				$data,
				[ 'tag' => 'div', 'link-class' => 'dropdown-item' ]
			);
		}

		$html .= Html::closeElement( 'div' );
		$html .= Html::closeElement( 'div' );
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	protected function getTriggerAttributes() {
		return [
			'href' => '#',
			'role' => 'button',
			'id' => IdRegistry::getRegistry()->getId( $this->getHtmlId() ),
			'data-toggle' => 'dropdown',
			'aria-haspopup' => 'true',
			'aria-expanded' => 'false',
		];
	}

	/**
	 * @return string
	 */
	abstract protected function getTrigger();

	/**
	 * @return string
	 */
	abstract protected function getCssClass();

	/**
	 * @return string
	 */
	abstract protected function getHtmlId();

	/**
	 * @return string
	 */
	abstract protected function getMainLabel();

	/**
	 * @return array
	 */
	abstract protected function getItems();

	/**
	 * @return string
	 */
	abstract protected function getDropdownCssClass();
}
