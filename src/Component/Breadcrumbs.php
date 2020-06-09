<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use Html;
use Skins\Chameleon\Components\Component;
use Title;

class Breadcrumbs extends Component {
	public function getHtml() {
		$title = $this->getSkin()->getTitle();
		if ( !$title instanceof Title || $title->isSpecialPage() ) {
			return '';
		}

		return $this->makeBreadcrumbs( $title );
	}

	private function makeBreadcrumbs( Title $title ) {
		$text = $title->getText();
		$ns = $title->getNamespace();

		$pages = explode( '/', $text );
		if ( count( $pages ) === 1 ) {
			// No point doing it for one page
			return '';
		}

		$html = Html::openElement( 'div', [
			'class' => 'wm-breadcrumbs'
		] );

		$pageConcat = [];
		foreach ( $pages as $idx => $page ) {
			if ( $idx > 0 ) {
				$html .= Html::element( 'span', [
					'class' => 'wm-breadcrumbs-separator'
				], '/' );
			}
			$pageConcat[] = $page;
			$html .= $this->getButton( implode( '/', $pageConcat ),$page, $ns );
		}

		$html .= Html::closeElement( 'div' );

		return $html;
	}

	private function getButton( $page, $text, $ns ) {
		$title = Title::makeTitleSafe( $ns, $page );

		return Html::element( 'a', [
			'class' => 'btn wm-button',
			'href' => $title->getLocalURL(),
		], $text );
	}

}
