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

use MediaWiki\Context\IContextSource;
use OOUI\IconWidget;
use Wikimedia\Message\IMessageFormatterFactory;

class LogoComponent extends MessageComponent {
	/**
	 * @param IMessageFormatterFactory $messageFormatterFactory
	 * @param IContextSource $contextSource
	 * @param string $href
	 * @param string $tooltip
	 */
	public function __construct(
		IMessageFormatterFactory $messageFormatterFactory,
		IContextSource $contextSource,
		string $href,
		string $tooltip
	) {
		parent::__construct(
			'Logo',
			$messageFormatterFactory,
			$contextSource
		);

		// Ignore wgLogo and 'logopath' from SkinTemplate
		$logoIcon = new IconWidget( [
			'icon' => 'logoWikimedia',
			'title' => $tooltip
		] );

		$this->args = [
			'logoIcon' => $logoIcon,
			'sitename-main' => $this->formatMessage( 'wikimediaapiportal-skin-site-name-main' ),
			'sitename-sub' => $this->formatMessage( 'wikimediaapiportal-skin-site-name-sub' ),
			'mainpage-href' => $href,
			'logo-tooltip' => $tooltip
		];
	}
}
