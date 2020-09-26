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
use Wikimedia\Message\ITextFormatter;
use Wikimedia\Message\MessageValue;

abstract class MessageComponent extends Component {
	/** @var ITextFormatter */
	private $textFormatter;

	/**
	 * @param string $templateName
	 * @param IMessageFormatterFactory $messageFormatterFactory
	 * @param IContextSource $contextSource
	 */
	public function __construct(
		string $templateName,
		IMessageFormatterFactory $messageFormatterFactory,
		IContextSource $contextSource
	) {
		parent::__construct( $templateName );
		$this->textFormatter = $messageFormatterFactory->getTextFormatter(
			$contextSource->getLanguage()->getCode()
		);
	}

	/**
	 * @param string $msg
	 * @param array $params
	 * @return string
	 */
	protected function formatMessage( string $msg, array $params = [] ) : string {
		return $this->textFormatter->format( new MessageValue( $msg, $params ) );
	}
}
