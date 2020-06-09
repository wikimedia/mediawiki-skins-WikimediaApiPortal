<?php

namespace MediaWiki\Skin\WikimediaApiPortal;

use Skins\Chameleon\Chameleon;

class Skin extends Chameleon {
	public $skinname = 'wikimediaapiportal';
	public $template = WikimediaApiPortalTemplate::class;

	public static function init() {
		$GLOBALS['egChameleonLayoutFile'] = dirname( __DIR__ ) . '/layouts/default.xml';
		$GLOBALS['wgUseMediaWikiUIEverywhere'] = true;
		$GLOBALS['wgSkipSkins'][] = 'chameleon';
		$GLOBALS['wgLogo'] = $GLOBALS['wgScriptPath'] . "/skins/WikimediaApiPortal/resources/images/icon/wikimedia-black.svg";

		parent::init();
		static::overrideSCSSVariables();
	}

	protected static function overrideSCSSVariables() {
		$GLOBALS['egChameleonExternalStyleVariables'] = [
			'container-max-widths' => '(sm: 899px, md: 1150px, lg: 1300px, xl: 1440px)',
			'cmln-collapse-point' => '992px',
		];
	}

	public function addSkinModulesToOutput() {
		parent::addSkinModulesToOutput();

		$this->getOutput()->enableOOUI();
		$this->getOutput()->addModuleStyles( [
			'oojs-ui.styles.icons-user',
			'oojs-ui.styles.icons-content',
			'oojs-ui.styles.icons-editing-core',
			'oojs-ui.styles.icons-interactions',
			'oojs-ui.styles.icons-movement',
			'skin.wikimediaapiportal.styles'
		] );
		$this->getOutput()->addModules( [
			"skin.wikimediaapiportal.searchform",
			"skin.wikimediaapiportal.scrollAdjust"
		] );

		if ( $this->getTitle()->isMainPage() && $this->isViewMode() ) {
			$this->getOutput()->addModuleStyles( [
				"skin.wikimediaapiportal.mainpage",
			] );
		}
	}

	public function isViewMode() {
		return $this->getRequest()->getText( 'action', 'view' ) === 'view';
	}
}
