<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component\Header;

use Html;
use Skins\Chameleon\Components\Component;

class NotificationAlert extends Component {

	public function getHtml() {
		if ( !isset( $this->getSkinTemplate()->data[ 'notification-alert' ] ) ) {
			return '';
		}

		$data = $this->getSkinTemplate()->get( 'notification-alert' );
		$count = $data[ 'data' ][ 'counter-num' ];

		$html = Html::openElement( 'div', [ 'class' => 'wm-notification-button wm-header-item' ] );
		$html .= Html::element( 'a', [
			'class' => 'wm-icon-button',
			'title' => $data[ 'text' ]->escaped(),
			'href' => $data[ 'href' ]
		] );
		if ( $count > 0 ) {
			$html .= Html::element( 'span', [
				'class' => 'wm-notification-count-badge'
			], $data[ 'data' ][ 'counter-text' ] );
		}
		$html .= Html::closeElement( 'div' );

		return $html;
	}
}
