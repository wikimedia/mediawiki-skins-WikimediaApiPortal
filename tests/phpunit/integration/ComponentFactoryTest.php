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
namespace MediaWiki\Skin\WikimediaApiPortal\Test;

use MediaWiki\Skin\WikimediaApiPortal\ComponentFactory;
use MediaWikiIntegrationTestCase;
use Wikimedia\TestingAccessWrapper;

/**
 * @covers \MediaWiki\Skin\WikimediaApiPortal\ComponentFactory
 */
class ComponentFactoryTest extends MediaWikiIntegrationTestCase {
	use ComponentMockTrait;

	public static function provideComponentFactory() {
		return [
			[ 'createContentComponent' ],
			[ 'createFooterComponent' ],
			[ 'createLogoComponent' ],
			[ 'createMainComponent' ],
			[ 'createNavBarComponent' ],
			[ 'createNavMenuComponent' ],
			[ 'createSearchButtonComponent' ],
			[ 'createSearchFieldComponent' ],
			[ 'createSecondaryNavComponent' ],
			[ 'createUserMenuComponent' ]
		];
	}

	/**
	 * @dataProvider provideComponentFactory
	 * @param string $test
	 */
	public function testComponentFactory( string $test ) {
		$componentFactory = $this->getComponentFactory();
		$template = $this->newWikimediaApiPortalTemplate();
		$this->assertNotNull( $componentFactory->$test( $template ) );
	}

	public function testCreatePageToolsComponent() {
		$componentFactory = $this->getComponentFactory();
		$template = $this->newWikimediaApiPortalTemplate();
		$this->assertNotNull( $componentFactory->createPageToolsComponent( $template, false ) );
	}

	public function testCreatePageToolsMobileComponent() {
		$componentFactory = $this->getComponentFactory();
		$template = $this->newWikimediaApiPortalTemplate();
		$this->assertNotNull( $componentFactory->createPageToolsComponent( $template, true ) );
	}

	public function testCreatePrimaryNavComponent() {
		$componentFactory = $this->getComponentFactory();
		$template = $this->newWikimediaApiPortalTemplate();
		$this->assertNotNull(
			$componentFactory->createPrimaryNavComponent( $template, 'test_id' )
		);
	}

	private function getComponentFactory() {
		return TestingAccessWrapper::newFromObject( new ComponentFactory(
			$this->newConfig(),
			$this->newMessageFormatterFactory(),
			$this->newTitleFactory(),
			$this->newNamespaceInfo(),
			$this->newPageProps(),
			$this->newPermissionManager(),
			$this->newExtensionRegistry()
		) );
	}
}
