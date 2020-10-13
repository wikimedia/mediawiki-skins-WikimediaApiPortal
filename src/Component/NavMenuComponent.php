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

use OOUI\IconWidget;
use TitleFactory;

class NavMenuComponent extends Component {
	/**
	 * @param TitleFactory $titleFactory
	 * @param ?PrimaryNavComponent $primaryNav
	 * @param ?SecondaryNavComponent $secondaryNav
	 * @param ?PageToolsComponent $pageToolsMobile
	 */
	public function __construct(
		TitleFactory $titleFactory,
		?PrimaryNavComponent $primaryNav,
		?SecondaryNavComponent $secondaryNav,
		?PageToolsComponent $pageToolsMobile
	) {
		parent::__construct( 'NavMenu' );

		$menuIcon = new IconWidget( [ 'icon' => 'menu' ] );
		$previousIcon = new IconWidget( [ 'icon' => 'previous' ] );
		$mainPage = $titleFactory->newMainPage();

		$this->args = [
			'menuIcon' => $menuIcon,
			'previousIcon' => $previousIcon,
			'mainpage-href' => $mainPage->getLinkURL(),
			'mainpage-text' => $mainPage->getText(),
			'PrimaryNav' => $primaryNav,
			'SecondaryNav' => $secondaryNav,
			'PageToolsMobile' => $pageToolsMobile
		];
	}
}
