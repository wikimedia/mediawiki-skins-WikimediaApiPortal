<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use Skins\Chameleon\Components\MainContent as ChameleonMainContent;

class MainContent extends ChameleonMainContent {
	protected function buildContentHeader() {
		$html = parent::buildContentHeader();

		$pageTools = new PageTools( $this->getSkinTemplate(), $this->getDomElement() );
		$html .= $pageTools->getHtml();

		return $html;
	}
}
