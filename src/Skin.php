<?php

namespace MediaWiki\Skin\WikimediaApiPortal;

use OutputPage;
use ParserOptions;
use QuickTemplate;
use SkinTemplate;
use Title;

class Skin extends SkinTemplate {
	public $skinname = 'wikimediaapiportal';
	public $template = WikimediaApiPortalTemplate::class;

	/**
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
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
		$this->getOutput()->getWikiPage()->getParserOutput(
			ParserOptions::newFromContext( $this->getContext() )
		);

		return $title->equals( $currentTitle ) || $currentTitle->isSubpageOf( $title );
	}

	/** @return QuickTemplate */
	protected function setupTemplateForOutput() : QuickTemplate {
		$template = parent::setupTemplateForOutput();
		$template->set( 'skin', $this );

		$output = $this->getOutput();
		$output->enableOOUI();
		$output->addModuleStyles( [
			'mediawiki.skinning.content',
			'oojs-ui.styles.icons-user',
			'oojs-ui.styles.icons-content',
			'oojs-ui.styles.icons-editing-core',
			'oojs-ui.styles.icons-interactions',
			'oojs-ui.styles.icons-movement',
			'skin.wikimediaapiportal.styles'
		] );
		$output->addModules( "skin.wikimediaapiportal.scripts" );
		if ( $this->getTitle()->isMainPage() && $this->isViewAction() ) {
			$output->addModuleStyles( [
				"skin.wikimediaapiportal.mainpage",
			] );
		}

		return $template;
	}
}
