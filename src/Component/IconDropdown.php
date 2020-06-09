<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use Html;

abstract class IconDropdown extends Dropdown {
	protected function getTrigger() {
		return Html::element( 'div', array_merge( [
			'class' => 'wm-icon-button dropdown-toggle ' . $this->getIconClass(),
			'title' => htmlspecialchars( $this->getMainLabel() )
		], $this->getTriggerAttributes() ) );
	}

	/**
	 * @return string
	 */
	abstract protected function getIconClass();
}
