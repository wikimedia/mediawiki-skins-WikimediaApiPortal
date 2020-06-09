<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use Html;
use MediaWiki\MediaWikiServices;
use Skins\Chameleon\Components\Component;
use Title;
use TitleArray;

class PageNav extends Component {
	/** @var Title|null */
	protected $currentTitle = null;

	public function getHtml() {
		$this->currentTitle = $this->getSkin()->getTitle();
		if ( $this->currentTitle->isTalkPage() ) {
			$subjectNS = MediaWikiServices::getInstance()->getNamespaceInfo()
				->getSubject( $this->currentTitle->getNamespace() );
			$this->currentTitle = Title::makeTitle( $subjectNS, $this->currentTitle->getDBkey() );
		}
		if ( !$this->shouldBuildNav() ) {
			return '';
		}

		return $this->buildNav();
	}

	private function shouldBuildNav() {
		return $this->currentTitle->isSubpage() || $this->currentTitle->hasSubpages();
	}

	private function buildNav() {
		$pages = explode( '/', $this->currentTitle->getText() );
		$base = array_shift( $pages );

		$html = Html::openElement( 'ul', [
			'class' =>  'nav flex-column wm-page-nav'
		] );
		$this->doBuild( $base, $this->currentTitle->getNamespace(), $html );
		$html .= Html::closeElement( 'ul' );

		return $html;
	}

	/**
	 * @param string $page
	 * @param int $ns
	 * @param string &$html
	 */
	private function doBuild( $page, $ns, &$html ) {
		$title = Title::makeTitleSafe( $ns, $page );
		$pages = $title->getSubpages();

		if ( count( $pages ) > 0 ) {
			/** @var Title $pageTitle */
			foreach( $pages as $pageTitle ) {
				// Not direct sub
				if ( $pageTitle->getBaseText() !== $page ) {
					continue;
				}

				$subpages = $pageTitle->getSubpages();
				$active = $this->isActive( $pageTitle, $subpages );
				$html .= Html::openElement( 'li', [
					'class' => $active ? 'nav-item active' : 'nav-item',
				] );
				$html .= Html::element( 'a', [
					'class' => 'nav-link',
					'href' => $pageTitle->getLocalURL()
				], $pageTitle->getSubpageText() );

				if ( count( $subpages ) > 0 ) {
					$html .= Html::openElement( 'ul', [
						'class' => ( $active || $this->shouldExpandAll() ) ?
							'nav flex-column wm-page-nav-sub' : 'nav flex-column wm-page-nav-sub collapsed'
					] );
					$this->doBuild( $pageTitle->getText(), $pageTitle->getNamespace(), $html );
					$html .= Html::closeElement( 'ul' );
				}
				$html .= Html::closeElement( 'li' );
			}
		}
	}

	private function isActive( ?Title $title, $subpages ) {
		return $title instanceof Title &&
			( $this->isCurrent( $title ) || $this->subpageIsActive( $subpages ) );
	}

	/**
	 * @param TitleArray $pages
	 * @return bool
	 */
	private function subpageIsActive( $pages ) {
		foreach ( $pages as $title ) {
			if ( $this->isCurrent( $title ) ) {
				return true;
			}
		}

		return false;
	}

	private function isCurrent( ?Title $title) {
		return $title instanceof Title && $title->equals( $this->currentTitle );
	}

	private function shouldExpandAll() {
		$domElement = $this->getDomElement();
		$attribute = $domElement->getAttribute( 'expandAll' );

		return filter_var( $attribute, FILTER_VALIDATE_BOOLEAN );
	}

}
