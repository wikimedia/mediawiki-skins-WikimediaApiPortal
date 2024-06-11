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

use MediaWiki\Config\ServiceOptions;
use MediaWiki\Context\IContextSource;
use MediaWiki\Linker\Linker;
use MediaWiki\Message\Message;
use OOUI\ButtonWidget;
use Wikimedia\Message\IMessageFormatterFactory;

class SearchButtonComponent extends MessageComponent {
	public const CONSTRUCTOR_OPTIONS = [
		'Script',
	];

	/**
	 * @param IMessageFormatterFactory $messageFormatterFactory
	 * @param IContextSource $contextSource
	 * @param ServiceOptions $serviceOptions
	 */
	public function __construct(
		IMessageFormatterFactory $messageFormatterFactory,
		IContextSource $contextSource,
		ServiceOptions $serviceOptions
	) {
		parent::__construct(
			'SearchButton',
			$messageFormatterFactory,
			$contextSource
		);

		$serviceOptions->assertRequiredOptions( self::CONSTRUCTOR_OPTIONS );
		$scriptUrl = $serviceOptions->get( 'Script' );

		$searchSubmitButton = new ButtonWidget( [
			'name' => 'Search',
			'framed' => false,
			'icon' => 'search',
			'title' => Linker::titleAttrib( 'search-fulltext' ),
			'classes' => [ 'wm-search-button-submit' ],
			'disabled' => true,
		] );

		$searchButton = new ButtonWidget( [
			'name' => 'Search',
			'framed' => false,
			'icon' => 'search',
			'title' => Message::newFromKey( 'searchbutton' )->text(),
			'classes' => [ 'wm-search-trigger' ],
		] );

		$searchClearButton = new ButtonWidget( [
			'name' => 'clear',
			'title' =>
				Message::newFromKey( 'wikimediaapiportal-skin-button-clear-search-label' )->text(),
			'classes' => [ 'wm-search-clear-button' ],
			'framed' => false,
			'icon' => 'close',
		] );

		$this->args = [
			'searchButton' => $searchButton,
			'wgScript' => $scriptUrl,
			'searchSubmitButton' => $searchSubmitButton,
			'searchClearButton' => $searchClearButton,
			'searchPlaceholder' => $this->formatMessage( 'wikimediaapiportal-skin-search-placeholder' ),
			'searchTooltip' => $this->formatMessage( 'wikimediaapiportal-skin-search-placeholder' )
		];
	}
}
