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

use MediaWiki\Config\ServiceOptions;
use MediaWiki\Skin\WikimediaApiPortal\Component\SecondaryNavComponent;
use MediaWikiIntegrationTestCase;

/**
 * @covers \MediaWiki\Skin\WikimediaApiPortal\Component\SecondaryNavComponent
 */
class SecondaryNavComponentTest extends MediaWikiIntegrationTestCase {
	use ComponentTestTrait;

	protected function getComponentClass(): string {
		return SecondaryNavComponent::class;
	}

	/**
	 * @return ServiceOptions
	 */
	protected function newServiceOptions(): ServiceOptions {
		return new ServiceOptions(
			SecondaryNavComponent::CONSTRUCTOR_OPTIONS,
			[
				'WMAPIPSidebarSpecialPages' => []
			]
		);
	}

	public function testInvalidSpecialPageConfigured() {
		$component = new SecondaryNavComponent(
			new ServiceOptions(
				SecondaryNavComponent::CONSTRUCTOR_OPTIONS,
				[
					'WMAPIPSidebarSpecialPages' => [
						'Version',
						'Non_Existent',
						'AllPages'
					]
				]
			),
			$this->newMessageFormatterFactory(),
			$this->newContextSource(),
			$this->getServiceContainer()->getTitleFactory()->makeTitle( NS_SPECIAL, 'Version/Test' ),
			$this->newNamespaceInfo(),
			$this->getServiceContainer()->getTitleFactory(),
			$this->getServiceContainer()->getSpecialPageFactory(),
			$this->newPageProps()
		);
		$output = $component->parseTemplate( $this->newTemplateParser() );
		$this->assertStringContainsString( 'Version', $output );
		$this->assertStringContainsString( 'AllPages', $output );
		$this->assertStringNotContainsString( 'Non_Existent', $output );
	}
}
