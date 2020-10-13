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

use MediaWiki\Skin\WikimediaApiPortal\Component\FooterComponent;
use MediaWikiIntegrationTestCase;

/**
 * @covers \MediaWiki\Skin\WikimediaApiPortal\Component\FooterComponent
 */
class FooterComponentTest extends MediaWikiIntegrationTestCase {
	use ComponentTestTrait;

	public function testAllComponentArgumentPassedToRender() {
		$messageFormatterFactory = $this->newMessageFormatterFactory();
		$contextSource = $this->newContextSource();
		$groups = [];
		$header = 'groups_header_test_input';
		$items = 'groups_items_test_input';
		$groups[ $header ] = [ $items ];

		$component = new FooterComponent( $messageFormatterFactory, $contextSource, $groups );

		$html = $component->parseTemplate( $this->newTemplateParser() );

		$this->assertStringContainsString( $header, $html,
			'parsed result should contain groups header' );
		$this->assertStringContainsString( 'wikimediaapiportal-skin-wikimedia-info', $html );
		$this->assertStringContainsString( 'wikimediaapiportal-skin-button-contact-label', $html );
		$this->assertStringContainsString( 'https://wikimediafoundation.org/about/contact/', $html );
		$this->assertStringContainsString( 'wikimediaapiportal-skin-button-about-wm-label', $html );
		$this->assertStringContainsString( 'https://wikimediafoundation.org/about/', $html );
		$this->assertStringContainsString( 'wikimediaapiportal-skin-disclaimer', $html );
	}

	protected function getComponentClass(): string {
		return FooterComponent::class;
	}
}
