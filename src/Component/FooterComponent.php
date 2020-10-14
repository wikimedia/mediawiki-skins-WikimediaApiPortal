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

use IContextSource;
use Wikimedia\Message\IMessageFormatterFactory;

class FooterComponent extends MessageComponent {
	/**
	 * @param IMessageFormatterFactory $messageFormatterFactory
	 * @param IContextSource $contextSource
	 * @param array $groups
	 */
	public function __construct(
		IMessageFormatterFactory $messageFormatterFactory,
		IContextSource $contextSource,
		array $groups
	) {
		parent::__construct(
			'Footer',
			$messageFormatterFactory,
			$contextSource
		);

		$links = [];
		$first = true;
		foreach ( $groups as $header => $items ) {
			$links[$header] = [
				'isFirst' => $first,
				'items' => $items,
			];
			$first = false;
		}

		$this->args = [
			'info' => $contextSource->msg( 'wikimediaapiportal-skin-wikimedia-info' )->parse(),
			'contact' => [
				'text' => $this->formatMessage( 'wikimediaapiportal-skin-button-contact-label' ),
				'href' => 'https://wikimediafoundation.org/about/contact/'
			],
			'about-wm' => [
				'text' => $this->formatMessage( 'wikimediaapiportal-skin-button-about-wm-label' ),
				'href' => 'https://wikimediafoundation.org/about/'
			],
			'links' => $links,
			'disclaimer' => $contextSource->msg( 'wikimediaapiportal-skin-disclaimer' )->parse()
		];
	}
}
