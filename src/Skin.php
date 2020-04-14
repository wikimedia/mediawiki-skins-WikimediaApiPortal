<?php

namespace MediaWiki\Skin\WikimediaApiPortal;

use Skins\Chameleon\Chameleon;

class Skin extends Chameleon {
	public $skinname = 'wikimediaapiportal';

	public static function init() {
		$GLOBALS['egChameleonLayoutFile'] = dirname( __DIR__ ) . '/layouts/default.xml';
		$GLOBALS['wgUseMediaWikiUIEverywhere'] = true;
		$GLOBALS['wgSkipSkins'][] = 'chameleon';

		parent::init();
	}
}
