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

use MediaWiki\Skin\WikimediaApiPortal\Skin;
use OOUI\IconWidget;
use OOUI\IndicatorWidget;
use Title;

class PrimaryNavComponent extends Component {
	/**
	 * @param Title $title
	 * @param string $id
	 * @param array $sidebar
	 * @param Skin $skin
	 */
	public function __construct(
		Title $title,
		string $id,
		array $sidebar,
		Skin $skin
	) {
		parent::__construct( 'PrimaryNav' );
		$menuIcon = new IconWidget( [ 'icon' => 'menu' ] );
		$items = [];
		foreach ( $sidebar as $menuKey => $menu ) {
			/** @var array $menu See BaseTemplate::getSidebar */

			// Dropdown or single link
			if ( is_array( $menu['content'] ) && count( $menu['content'] ) > 1 ) {
				$subitems = [];
				$hasActive = false;
				foreach ( $menu['content'] as $key => $item ) {
					/** @var array $item See Skin::addToSidebarPlain */
					$hasActive = $hasActive || $this->isActiveLink( $title, $item['href'] );
					$subitems[] = $skin->makeListItem( $key, $item, [
						'tag' => 'div',
						'class' => 'nav-item',
						'link-class' => 'nav-link',
					] );
				}

				$items[] = [
					'isDropdown' => true,
					'hasActive' => $hasActive,
					'menuKey' => $menuKey,
					'id' => $menu['id'],
					'header' => $menu['header'],
					'items' => $subitems,
				];
			} else {
				$isActive = $this->isActiveLink( $title, $menu['content'][0]['href'] );
				$items[] = [
					'isLink' => true,
					'isActive' => $isActive,
					'header' => $menu['header'],
					'href' => $menu['content'][0]['href'],
				];
			}
		}

		$dropDownIndicator = new IndicatorWidget( [ 'indicator' => 'down' ] );
		$this->args = [
			'menuIcon' => $menuIcon,
			'id' => $id,
			'items' => $items,
			'dropDownIndicator' => $dropDownIndicator
		];
	}

	/**
	 * Whether the link points to the current title (or a subpage thereof).
	 * @param Title $title
	 * @param string $link
	 * @return bool
	 */
	private function isActiveLink( Title $title, string $link ) : bool {
		// Match logic in Skin::addToSidebarPlain
		$currentLink = $title->fixSpecialName()->getLinkURL();

		return $link === $currentLink || strpos( $currentLink, "$link/" ) === 0;
	}
}
