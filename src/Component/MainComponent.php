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

use IContextSource;
use Wikimedia\Message\IMessageFormatterFactory;

class MainComponent extends MessageComponent {
	/**
	 * @param IMessageFormatterFactory $messageFormatterFactory
	 * @param IContextSource $contextSource
	 * @param string $headelement
	 * @param string $bottomelement
	 * @param string $trail
	 * @param ?LogoComponent $logo
	 * @param ?NavMenuComponent $navMenu
	 * @param ?NavBarComponent $navBar
	 * @param ?SecondaryNavComponent $secondaryNav
	 * @param ?UserMenuComponent $userMenu
	 * @param ?NotificationAlertComponent $notificationAlert
	 * @param ?SearchFieldComponent $searchField
	 * @param ?SearchButtonComponent $searchButton
	 * @param ?ContentComponent $content
	 * @param ?FooterComponent $footer
	 */
	public function __construct(
		IMessageFormatterFactory $messageFormatterFactory,
		IContextSource $contextSource,
		string $headelement,
		string $bottomelement,
		string $trail,
		?LogoComponent $logo,
		?NavMenuComponent $navMenu,
		?NavBarComponent $navBar,
		?SecondaryNavComponent $secondaryNav,
		?UserMenuComponent $userMenu,
		?NotificationAlertComponent $notificationAlert,
		?SearchFieldComponent $searchField,
		?SearchButtonComponent $searchButton,
		?ContentComponent $content,
		?FooterComponent $footer
	) {
		parent::__construct(
			'Main',
			$messageFormatterFactory,
			$contextSource
		);
		$this->args = [
			'html-headelement' => $headelement,
			'html-bottomelement' => $bottomelement,
			'jumptocontent' => $this->formatMessage( 'wikimediaapiportal-jumpto-content' ),
			'html-trail' => $trail,
			'Logo' => $logo,
			'NavMenu' => $navMenu,
			'NavBar' => $navBar,
			'SecondaryNav' => $secondaryNav,
			'UserMenu' => $userMenu,
			'NotificationAlert' => $notificationAlert,
			'SearchField' => $searchField,
			'SearchButton' => $searchButton,
			'Content' => $content,
			'Footer' => $footer
		];
	}
}
