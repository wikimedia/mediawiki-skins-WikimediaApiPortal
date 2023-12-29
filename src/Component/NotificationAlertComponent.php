<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */
namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use ExtensionRegistry;
use MediaWiki\User\User;
use OOUI\ButtonWidget;

class NotificationAlertComponent extends Component {
	/**
	 * @param ExtensionRegistry $extensionRegistry
	 * @param User $user
	 * @param array $notificationAlert
	 */
	public function __construct(
		ExtensionRegistry $extensionRegistry,
		User $user,
		array $notificationAlert
	) {
		parent::__construct( 'NotificationAlert' );

		if ( !isset( $notificationAlert['data']['counter-num'] ) ||
			!isset( $notificationAlert['data']['counter-text'] ) ||
			!isset( $notificationAlert['href'] ) ||
			!isset( $notificationAlert['text'] )
		) {
			$this->args = null;
			return;
		}

		if ( !$user->isRegistered() ) {
			$this->args = null;
			return;
		}

		// Support: Echo extension
		if ( !$extensionRegistry->isLoaded( 'Echo' ) ) {
			$this->args = null;
			return;
		}

		$this->args = [
			'hasCount' => $notificationAlert['data']['counter-num'] > 0,
			'count' => $notificationAlert['data']['counter-text'],
			'notificationButton' => new ButtonWidget( [
				'icon' => 'bellOutline',
				'title' => $notificationAlert['text'],
				'framed' => false,
				'href' => $notificationAlert['href']
			] )
		];
	}
}
