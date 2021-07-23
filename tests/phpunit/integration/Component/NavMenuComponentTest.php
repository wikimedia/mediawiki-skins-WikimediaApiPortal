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

use MediaWiki\Skin\WikimediaApiPortal\Component\NavMenuComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\PageToolsComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\PrimaryNavComponent;
use MediaWiki\Skin\WikimediaApiPortal\Component\SecondaryNavComponent;
use MediaWikiIntegrationTestCase;
use OOUI\BlankTheme;
use OOUI\Theme;

/**
 * @covers \MediaWiki\Skin\WikimediaApiPortal\Component\NavMenuComponent
 */
class NavMenuComponentTest extends MediaWikiIntegrationTestCase {
	use ComponentTestTrait;

	public function setUp(): void {
		parent::setUp();

		// enable OOUI
		Theme::setSingleton( new BlankTheme() );
	}

	public function testAllComponentArgumentPassedToRender() {
		$component = new NavMenuComponent(
			$this->newTitleFactory(),
			$this->createComponentMock( PrimaryNavComponent::class, 'primary_nav_component' ),
			$this->createComponentMock( SecondaryNavComponent::class, 'secondary_nav_component' ),
			$this->createComponentMock( PageToolsComponent::class, 'page_tools_mobile_component' )
		);
		$this->assertNotNull( $component->parseTemplate( $this->newTemplateParser() ) );
	}

	public function testAllComponentArgumentPassedToRenderAllNull() {
		$titleFactory = $this->newTitleFactory();
		$primaryNav = null;
		$secondaryNav = null;
		$pageToolsMobile = null;

		$component = new NavMenuComponent( $titleFactory, $primaryNav, $secondaryNav,
			$pageToolsMobile );

		$templateParser = $this->newTemplateParser();
		$html = $component->parseTemplate( $templateParser );

		$this->assertNotNull( $html );
	}

	protected function getComponentClass(): string {
		return NavMenuComponent::class;
	}
}
