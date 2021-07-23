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

use MediaWiki\Skin\WikimediaApiPortal\TemplateParser;

abstract class Component {
	/** @var string */
	protected $templateName;

	/** @var ?array */
	protected $args;

	/**
	 * @param string $templateName
	 */
	public function __construct( string $templateName ) {
		$this->templateName = $templateName;
	}

	/**
	 * @param TemplateParser $templateParser
	 * @return ?string
	 */
	public function parseTemplate( TemplateParser $templateParser ): ?string {
		if ( !$this->args ) {
			return null;
		}
		$parsedArgs = [];
		foreach ( $this->args as $key => $arg ) {
			$parsedArgs[$key] = is_subclass_of( $arg, self::class )
				? $arg->parseTemplate( $templateParser )
				: $arg;
		}
		return $templateParser->processTemplate( $this->templateName, $parsedArgs );
	}
}
