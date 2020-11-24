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
namespace MediaWiki\Skin\WikimediaApiPortal\Test\Component;

use MediaWiki\Skin\WikimediaApiPortal\Component\ContentComponent;
use MediaWikiIntegrationTestCase;

/**
 * @covers \MediaWiki\Skin\WikimediaApiPortal\Component\ContentComponent
 */
class ContentComponentTest extends MediaWikiIntegrationTestCase {
	use ComponentTestTrait;

	public function testConstructorSetsArgsArrayNull() {
		$title = 'title_test_input';
		$subtitle = null;
		$undelete = null;
		$pageTools = null;
		$bodyContent = 'bodyContent_test_input';
		$afterContent = 'afterContent_test_input';
		$catlinks = 'catlinks_test_input';

		$component = new ContentComponent(
			$title,
			$subtitle,
			$undelete,
			$pageTools,
			$bodyContent,
			$afterContent,
			$catlinks
		);

		$html = $component->parseTemplate( $this->newTemplateParser() );

		$substrings = [ $title, $bodyContent, $afterContent, $catlinks ];
		foreach ( $substrings as $substring ) {
			$this->assertStringContainsString( $substring, $html );
		}
	}

	protected function getComponentClass(): string {
		return ContentComponent::class;
	}
}
