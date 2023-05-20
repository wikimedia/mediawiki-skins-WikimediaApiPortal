<?php
/**
 * Makes sure all the templates in the skin are compilable.
 *
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
 * @coversNothing
 */
class TemplateIntegrityTest extends MediaWikiIntegrationTestCase {

	private static function getTemplateDir() {
		return __DIR__ . '/../../../components';
	}

	public static function provideTemplateNames() {
		foreach ( array_slice( scandir( self::getTemplateDir() ), 2 ) as $filename ) {
			yield $filename => [ pathinfo( $filename, PATHINFO_FILENAME ) ];
		}
	}

	/**
	 * @dataProvider provideTemplateNames
	 * @param string $templateName
	 */
	public function testTemplateCompilable( string $templateName ) {
		$parser = new TemplateParser(
			self::getTemplateDir(),
			new EmptyBagOStuff()
		);
		$parser->enableRecursivePartials( true );
		$html = $parser->processTemplate( $templateName, [] );
		// Mostly interested in compiler not throwing,
		// but let's make this bogus assertion instead
		// since at least one assert is required.
		$this->assertNotNull( $html );
	}
}
