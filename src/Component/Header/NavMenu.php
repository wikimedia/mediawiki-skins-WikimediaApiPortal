<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component\Header;

use Html;
use Linker;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skin\WikimediaApiPortal\Component\PageNav;
use MediaWiki\Skin\WikimediaApiPortal\Component\PageTools;
use MWException;
use Skins\Chameleon\Components\NavMenu as ChameleonNavMenu;
use Skins\Chameleon\IdRegistry;
use Title;

/**
 * Purpose of this class is to allow non-dropdown menus and to select active item.
 * Originally, all menus declared in sidebar have to have a header (section)
 * and then subitems. For this navigation, we need to have nav items that are
 * themselves links, not just triggers for a dropdown.
 *
 * It would be nice for this to go into the upstream (with some modifications)
 */
class NavMenu extends ChameleonNavMenu {
	public function getHtml() {
		if ( $this->shouldIncludePageNav() ) {
			$pageNav = new PageNav( $this->getSkinTemplate(), $this->getDomElement() );
			$html = $pageNav->getHtml();
			if ( $html ) {
				// If $html is not empty, it means page has its own navigation
				$html = $this->buildPageNav( $html );
				return $html;
			}

		}
		return parent::getHtml();
	}

	protected function getDropdownForNavMenu( $menuName, $menuDescription, $flatten = false ) {
		if (
			$this->hasSubmenuItems( $menuDescription ) &&
			count( $menuDescription['content'] ) === 1
		) {
			return $this->buildLinkItem( $menuDescription );
		}
		return parent::getDropdownForNavMenu( $menuName, $menuDescription, $flatten );
	}

	/**
	 * Build nav item for single-item menus
	 *
	 * @param array $menuDescription
	 * @return string
	 * @throws MWException
	 */
	protected function buildLinkItem( $menuDescription ) {
		$item = $menuDescription['content'][0];
		$active = $this->isActive( $item );
		$link = $this->indent() . Html::openElement( 'div', [
			'class' => $active ? 'nav-item active' : 'nav-item',
			'title' => Linker::titleAttrib( $menuDescription[ 'id' ] )
		] );
		$link .= $this->indent() . Html::element( 'a', [
			'class' => 'nav-item nav-link',
			'href' => $item['href']
		], $item['text'] );
		$link .= Html::closeElement( 'div' );

		return $link;
	}

	/**
	 * @inheritDoc
	 */
	protected function buildDropdownOpeningTags( $menuDescription ) {
		$classes = [ 'nav-item', 'dropdown' ];
		if ( $this->hasActive( $menuDescription ) ) {
			$classes[] = 'active';
		}
		$ret = Html::openElement( 'div', [
			'class' => implode( ' ', $classes ),
			'title' => Linker::titleAttrib( $menuDescription['id'] ),
		] );

		$ret .= Html::element( 'a', [
			'href' => '#',
			'class' => 'nav-link dropdown-toggle',
			'data-toggle' => 'dropdown',
			'data-boundary' => 'viewport'
		], $menuDescription['header'] );

		$ret .= Html::openElement( 'div', [
			'class' => 'dropdown-menu ' . $menuDescription[ 'id' ],
			'id'    => IdRegistry::getRegistry()->getId( $menuDescription[ 'id' ] ),
		] );

		return $ret;
	}

	/**
	 * Is the menu descriptor active
	 *
	 * @param array $menuDescription
	 * @return bool
	 */
	private function hasActive( array $menuDescription ) {
		return !empty( array_filter( $menuDescription['content'], function( $item ) {
			return $this->isActive( $item );
		} ) );
	}

	/**
	 * Check if the current page is a subpage
	 * @param array $item
	 * @return bool
	 */
	private function isActive( $item ) {
		$currentTitle = $this->getSkin()->getTitle();
		if ( $currentTitle === null ) {
			return false;
		}
		$title = $this->titleFromURL( $item['href'] );
		if ( !$title instanceof Title )  {
			return false;
		}
		if ( $currentTitle->equals( $title ) ) {
			return true;
		}
		if ( $currentTitle->isSubpage() && $currentTitle->isSubpageOf( $title ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the Title object from the item URL
	 *
	 * @param string $href
	 * @return Title|null
	 */
	private function titleFromURL( $href ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$articlePath = $config->get( 'ArticlePath' );
		$pattern = preg_quote( $articlePath, '/' );
		$pattern = '/^' . str_replace( '\$1', '(.*?)(\?.*?|)', $pattern ) . '$/';
		$matches = [];
		preg_match( $pattern, $href, $matches );
		if ( !isset( $matches[1] ) ) {
			return null;
		}
		return Title::newFromText( $matches[1] );
	}

	/**
	 * @return bool
	 */
	private function shouldIncludePageNav() {
		$attribute = $this->getAttribute( 'includePageNav' );
		return filter_var( $attribute, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * @param string $html
	 * @return string
	 * @throws MWException
	 */
	private function buildPageNav( $html ) {
		$backButton = $this->buildBackButton();
		$pageActions = $this->buildPageTools();

		return $pageActions . $backButton . $html;
	}

	/**
	 * @return string
	 */
	private function buildBackButton() {
		$mainPage = Title::newMainPage();
		$html = Html::openElement( 'div', [
			'class' => 'wm-main-page'
		] );
		$html .= Html::element( 'a', [
			'href' => $mainPage->getLinkURL(),
		], $mainPage->getText() );
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 * @return string
	 * @throws MWException
	 */
	private function buildPageTools() {
		$pageTools = new PageTools( $this->getSkinTemplate(), $this->getDomElement() );
		return $pageTools->getHtml( true, [ 'framed' => true ] );
	}

}
