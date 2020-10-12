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

class ContentComponent extends Component {
	/**
	 * @param string $title
	 * @param string $subtitle
	 * @param ?string $undelete
	 * @param ?PageToolsComponent $pageTools
	 * @param string $bodyContent
	 * @param string $afterContent
	 * @param string $catlinks
	 */
	public function __construct(
		string $title,
		string $subtitle,
		?string $undelete,
		?PageToolsComponent $pageTools,
		string $bodyContent,
		string $afterContent,
		string $catlinks
	) {
		parent::__construct( 'Content' );
		$this->args = [
			'html-title' => $title,
			'html-subtitle' => $subtitle,
			'html-undelete' => $undelete,
			'PageTools' => $pageTools,
			'html-bodyContent' => $bodyContent,
			'html-afterContent' => $afterContent,
			'html-catlinks' => $catlinks
		];
	}
}
