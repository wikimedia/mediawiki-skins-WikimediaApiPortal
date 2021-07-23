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

use Html;
use MediaWiki\Skin\WikimediaApiPortal\Skin;
use OOUI\ButtonWidget;
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
		$items = [];
		foreach ( $sidebar as $menu ) {
			/** @var array $menu See BaseTemplate::getSidebar */

			// Dropdown or single link
			if ( is_array( $menu['content'] ) && count( $menu['content'] ) > 1 ) {
				$subitems = [];
				$isActive = false;
				foreach ( $menu['content'] as $key => $item ) {
					/** @var array $item See Skin::addToSidebarPlain */
					$isActive = $isActive || $this->isActiveLink( $title, $item['href'] );
					$subitems[] = Html::element(
						'a',
						[
							'href' => $item['href'],
							'class' => 'primary-nav-menu-item'
						],
						$item['text']
					);
				}

				$header = new ButtonWidget( [
					'label' => $menu['header'],
					'framed' => false,
					'indicator' => 'down',
					'classes' => [ 'primary-nav-menu-button' ]
				] );
				$items[] = [
					'isDropdown' => true,
					'isActive' => $isActive,
					'header' => $header,
					'items' => $subitems,
				];
			} elseif ( isset( $menu['content'][0] ) ) {
				$isActive = $this->isActiveLink( $title, $menu['content'][0]['href'] );
				$header = new ButtonWidget( [
					'label' => $menu['header'],
					'framed' => false,
					'href' => $menu['content'][0]['href'],
					'classes' => [ 'primary-nav-menu-button' ]
				] );
				$items[] = [
					'isDropdown' => false,
					'isActive' => $isActive,
					'header' => $header,
				];
			}
		}

		$this->args = [
			'items' => $items
		];
	}

	/**
	 * Whether the link points to the current title (or a subpage thereof).
	 * @param Title $title
	 * @param string $link
	 * @return bool
	 */
	private function isActiveLink( Title $title, string $link ): bool {
		// Match logic in Skin::addToSidebarPlain
		$currentLink = $title->fixSpecialName()->getLinkURL();

		return $link === $currentLink || strpos( $currentLink, "$link/" ) === 0;
	}
}
