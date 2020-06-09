<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component\Header;

use Html;
use Linker;
use Message;
use OOUI\ButtonWidget;
use Skins\Chameleon\Components\SearchBar as ChameleonSearchBar;
use Skins\Chameleon\IdRegistry;
use Title;

class SearchBar extends ChameleonSearchBar {

	public function getHtml() {
		if ( !isset( $this->getSkinTemplate()->data[ 'searchtitle' ] ) ) {
			return '';
		}

		$title = Title::newFromText( $this->getSkinTemplate()->data[ 'searchtitle' ] );
		if ( !$title instanceof Title ) {
			return '';
		}

		if ( $this->hidden() ) {
			$this->getSkin()->getOutput()->enableOOUI();
			return $this->buildHidden( $title );
		}
		return $this->wrapSearch( $this->buildForm( $title ) );
	}

	/**
	 * Only show a button for the search, and hide the input
	 *
	 * @param Title $title
	 * @return string
	 */
	private function buildHidden( Title $title ) {
		$html = Html::openElement( 'div',  [
			'class' => 'wm-search-button wm-header-item'
		] );
		$html .= Html::element( 'a', [
			'class' => 'wm-icon-button wm-search-trigger',
			'href' => '#'
		] );

		$html .= Html::closeElement( 'div' );
		$html .= $this->buildForm( $title, true );

		return $this->wrapSearch( $html );
	}

	private function wrapSearch( $content ) {
		$html = Html::openElement( 'div', [
			'class' => 'wm-search-container ' . $this->getClassString()
		] );
		$html .= $content;
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 * @param Title $title
	 * @param bool|null $hidden
	 * @return string
	 */
	private function buildForm( Title $title, $hidden = false ) {
		$html = Html::openElement( 'form', [
			'id'    => IdRegistry::getRegistry()->getId( 'searchform' ),
			'class' => $hidden ? 'mw-search wm-hidden' : 'mw-search',
			'action'=> $this->getSkinTemplate()->data[ 'wgScript' ],
		] );
		$html .= Html::openElement( 'div', array_merge( [
			'id'    => IdRegistry::getRegistry()->getId( 'p-search' ),
			'class' => 'row no-gutters wm-header-border-box p-search',
			'role'  => 'search',
		], Linker::tooltipAndAccesskeyAttribs( 'p-search' ) ) );

		$html .= Html::openElement( 'div', [ 'class' => 'col col-1' ] );
		$html .= $this->makeSearchButton();
		$html .= Html::closeElement( 'div' );

		$inputWidth = $this->hidden() ? '10' : '11';
		$html .= Html::openElement( 'div', [ 'class' => 'col col-' . $inputWidth ] );
		$html .= $this->makeSearchInput();
		$html .= Html::closeElement( 'div' );
		if ( $this->hidden() ) {
			// Clear button will also be used for hiding the search form, so must be present
			$html .= Html::openElement( 'div', [ 'class' => 'col col-1' ] );
			$html .= $this->makeClearButton();
			$html .= Html::closeElement( 'div' );
		}

		$html .= Html::closeElement( 'div' );
		$html .= Html::input( 'title', $title->getPrefixedDBkey(), 'hidden' );
		$html .= Html::closeElement( 'form' );

		return $html;
	}

	private function makeSearchButton() {
		$buttonAttrs = array_merge( [
			'value' => Message::newFromKey( 'searchbutton' )->text(),
			'id' => IdRegistry::getRegistry()->getId( 'mw-searchButton' ),
			'name' => 'fulltext',
			'type' => 'submit',
			'class' => 'wm-search-button-submit wm-icon-button',
		], Linker::tooltipAndAccesskeyAttribs( "search-fulltext" ) );

		return Html::element( 'button', $buttonAttrs );
	}

	private function makeSearchInput() {
		return $this->getSkin()->makeSearchInput( [
			'id' => IdRegistry::getRegistry()->getId( 'searchInput' ),
			'type' => 'text', 'class' => 'form-control',
			'autocomplete' => 'off'
		] );
	}

	private function makeClearButton() {
		return (string) ( new ButtonWidget( [
			'id' => IdRegistry::getRegistry()->getId( 'mw-searchClear' ),
			'name' => 'clear',
			'title' => Message::newFromKey(
				'wikimediaapiportal-skin-button-clear-search-label'
			)->escaped(),
			'classes' => [ 'wm-search-clear-button' ],
			'framed' => false,
			'icon' => 'close'
		] ) );
	}

	private function hidden() {
		$attribute = $this->getAttribute( 'hidden' );
		return filter_var( $attribute, FILTER_VALIDATE_BOOLEAN );
	}
}
