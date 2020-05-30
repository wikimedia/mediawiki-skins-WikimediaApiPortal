<?php

namespace MediaWiki\Skin\WikimediaApiPortal;

use Bootstrap\BootstrapManager;
use SkinTemplate;
use Title;

class Skin extends SkinTemplate {
	public $skinname = 'wikimediaapiportal';
	public $template = WikimediaApiPortalTemplate::class;

	/**
	 * @return string[] Modules
	 */
	public function getDefaultModules() {
		$modules = parent::getDefaultModules();

		$modules['styles']['skin'][] = 'mediawiki.skinning.content';
		$modules['styles']['skin'][] = 'zzz.ext.bootstrap.styles';

		return $modules;
	}

	/**
	 * @param \OutputPage $out
	 */
	public function initPage( \OutputPage $out ) {
		parent::initPage( $out );

		// Enable responsive behaviour on mobile browsers
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1, shrink-to-fit=no' );
	}

	/** @return string */
	public function getRequestedAction() {
		return $this->getRequest()->getRawVal( 'action', 'view' );
	}

	/** @return bool */
	public function isViewAction() {
		return $this->getRequestedAction() === 'view';
	}

	/**
	 * Whether the link points to the current title (or a subpage thereof).
	 * @param string $link
	 * @return bool
	 */
	public function isActiveLink( string $link ) {
		$currentTitle = $this->getTitle();
		if ( !$currentTitle ) {
			return false;
		}
		// Match logic in Skin::addToSidebarPlain
		$currentLink = $currentTitle->fixSpecialName()->getLinkURL();

		return $link === $currentLink || strpos( $currentLink, "$link/" ) === 0;
	}

	/**
	 * Whether the Title points to the current title (or a subpage thereof).
	 * @param Title $title
	 * @return bool
	 */
	public function isActiveTitle( Title $title ) {
		$currentTitle = $this->getTitle();
		if ( !$currentTitle ) {
			return false;
		}
		// Match logic in Skin::addToSidebarPlain
		$currentTitle = $currentTitle->fixSpecialName();

		return $title->equals( $currentTitle ) || $currentTitle->isSubpageOf( $title );
	}

	/** @return \QuickTemplate */
	protected function setupTemplateForOutput() : \QuickTemplate {
		$template = parent::setupTemplateForOutput();
		$template->set( 'skin', $this );

		$output = $this->getOutput();
		$output->addModules( 'ext.bootstrap.scripts' );

		$output->enableOOUI();
		$output->addModuleStyles( [
			'oojs-ui.styles.icons-user',
			'oojs-ui.styles.icons-content',
			'oojs-ui.styles.icons-editing-core',
			'oojs-ui.styles.icons-interactions',
			'oojs-ui.styles.icons-movement',
			'skin.wikimediaapiportal.styles'
		] );
		$output->addModules( "skin.wikimediaapiportal" );
		if ( $this->getTitle()->isMainPage() && $this->isViewAction() ) {
			$output->addModuleStyles( [
				"skin.wikimediaapiportal.mainpage",
			] );
		}

		return $template;
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ResourceLoaderRegisterModules
	 * @param \ResourceLoader $rl
	 */
	public static function onResourceLoaderRegisterModules( \ResourceLoader $rl ) {
		// Prepend our Bootstrap theme variables to the 'ext.bootstrap.styles' module.
		$bootstrapManager = BootstrapManager::getInstance();
		$bootstrapManager->addAllBootstrapModules();
		$bootstrapManager->addStyleFile( dirname( __DIR__ ) . '/resources/bootstrap.scss', 'variables' );

		// This module is a duplicate with no changes. It exist solely for the purpose of making
		// it apply after 'mediawiki.skinning.content', because the styles are sorted alphabetically.
		// https://github.com/ProfessionalWiki/chameleon/commit/7e4259db8f78ff
		$rl->register( 'zzz.ext.bootstrap.styles',
			$GLOBALS['wgResourceModules']['ext.bootstrap.styles'] );
	}
}
