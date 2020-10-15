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

use ExtensionRegistry;
use IContextSource;
use InvalidArgumentException;
use MediaWiki\Config\ServiceOptions;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Skin\WikimediaApiPortal\Component\Component;
use MediaWiki\Skin\WikimediaApiPortal\Skin;
use MediaWiki\Skin\WikimediaApiPortal\Test\ComponentMockTrait;
use MediaWiki\SpecialPage\SpecialPageFactory;
use NamespaceInfo;
use PageProps;
use ReflectionMethod;
use ReflectionParameter;
use Title;
use TitleFactory;
use User;
use Wikimedia\Message\IMessageFormatterFactory;

trait ComponentTestTrait {
	use ComponentMockTrait;

	/**
	 * @param string $class
	 * @param string $outputHtml
	 * @return Component
	 */
	private function createComponentMock( string $class, string $outputHtml ) {
		$mock = $this->createNoOpMock( $class, [ 'parseTemplate' ] );
		$mock->method( 'parseTemplate' )->willReturn( $outputHtml );
		return $mock;
	}

	private function getMockValueForParam( ReflectionParameter $param ) : array {
		if ( $param->isArray() ) {
			return [ null, [] ];
		}

		$type = $param->getClass();
		$stringValue = "VALUE:{$param->getName()}:{$param->getPosition()}";
		if ( !$type ) {
			// Assume string
			return [ $stringValue, $stringValue ];
		}

		$types = [
			[ Skin::class, 'newSkin' ],
			[ IMessageFormatterFactory::class, 'newMessageFormatterFactory' ],
			[ IContextSource::class, 'newContextSource' ],
			[ TitleFactory::class, 'newTitleFactory' ],
			[ SpecialPageFactory::class, 'newSpecialPageFactory' ],
			[ Title::class, 'newTitle' ],
			[ ExtensionRegistry::class, 'newExtensionRegistry' ],
			[ User::class, 'newUser' ],
			[ NamespaceInfo::class, 'newNamespaceInfo' ],
			[ PageProps::class, 'newPageProps' ],
			[ PermissionManager::class, 'newPermissionManager' ],
			[ ServiceOptions::class, 'newServiceOptions' ]
		];
		foreach ( $types as [ $class, $method ] ) {
			if ( $type->getName() === $class || $type->isSubclassOf( $class ) ) {
				return [ null, $this->$method() ];
			}
		}

		if ( $type->getName() === Component::class ||
			$type->isSubclassOf( Component::class )
		) {
			return [ $stringValue, $this->createComponentMock( $type->getName(), $stringValue ) ];
		}
		throw new InvalidArgumentException( "Unsupported parameter type {$type->getName()}" );
	}

	public function testAllComponentArgumentPassedToRender() {
		$componentClass = $this->getComponentClass();
		$componentClassConstructor = new ReflectionMethod( $componentClass, '__construct' );
		$mockParams = [];
		$expectedInOutput = [];
		foreach ( $componentClassConstructor->getParameters() as $param ) {
			list( $expectation, $mockParam ) = $this->getMockValueForParam( $param );
			$mockParams[] = $mockParam;
			if ( $expectation !== null ) {
				$expectedInOutput[] = $expectation;
			}
		}
		$componentInstance = new $componentClass( ...$mockParams );
		$result = $componentInstance->parseTemplate( $this->newTemplateParser() );
		foreach ( $expectedInOutput as $expected ) {
			$this->assertStringContainsString( $expected, $result );
		}
	}

	/**
	 * provided by each component test class
	 * @return string
	 */
	abstract protected function getComponentClass(): string;

	/**
	 * provided by Assert
	 * @param string $needle
	 * @param string $haystack
	 * @param string $message
	 * @return mixed
	 */
	abstract protected static function assertStringContainsString(
		string $needle,
		string $haystack,
		string $message = ''
	);
}
