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

use MediaWiki\Skin\WikimediaApiPortal\Component\PageToolsComponent;
use MediaWikiIntegrationTestCase;
use OOUI\BlankTheme;
use OOUI\Theme;

/**
 * @covers \MediaWiki\Skin\WikimediaApiPortal\Component\PageToolsComponent
 */
class PageToolsComponentTest extends MediaWikiIntegrationTestCase {
	use ComponentTestTrait;

	public function setUp() : void {
		parent::setUp();

		// enable OOUI
		Theme::setSingleton( new BlankTheme() );
	}

	public function testAllComponentArgumentPassedToRender() {
		$messageFormatterFactory = $this->newMessageFormatterFactory();
		$contextSource = $this->newContextSource();
		$permissionManager = $this->newPermissionManager();
		$requestedAction = 'view';
		$actions = [
			'move' => [
				'visible' => [ 'view' ],
				'group' => 'secondary',

			],
			'delete' => [
				'visible' => [ 'view' ],
				'flags' => 'destructive',
				'group' => 'secondary',
			]
		];

		$templateParser = $this->newTemplateParser();

		$component = new PageToolsComponent(
			$messageFormatterFactory,
			$contextSource,
			$permissionManager,
			$requestedAction,
			$actions,
			false
		);
		$this->assertNotNull(
			$component->parseTemplate( $templateParser ),
			'page tools should be non-null'
		);

		$component = new PageToolsComponent(
			$messageFormatterFactory,
			$contextSource,
			$permissionManager,
			$requestedAction,
			$actions,
			true
		);
		$this->assertNotNull(
			$component->parseTemplate( $templateParser ),
			'mobile page tools should be non-null'
		);
	}

	protected function getComponentClass(): string {
		return PageToolsComponent::class;
	}
}
