<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use Html;

abstract class LinkDropdown extends Dropdown {
	protected function getTrigger() {
		return Html::element( 'a', array_merge( [
			'class' => 'btn dropdown-toggle',
		], $this->getTriggerAttributes() ), $this->getMainLabel() );
	}
}
