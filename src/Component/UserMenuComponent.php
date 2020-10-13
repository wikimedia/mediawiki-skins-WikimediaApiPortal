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

use Html;
use IContextSource;
use MediaWiki\Config\ServiceOptions;
use Message;
use OOUI\IconWidget;
use SpecialPage;
use Title;
use TitleFactory;
use User;
use Wikimedia\Message\IMessageFormatterFactory;

class UserMenuComponent extends MessageComponent {
	// TODO: take DevCenter structure from config
	public const CONSTRUCTOR_OPTIONS = [
		];

	// Personal url keys that will be allowed in the user menu
	private const PERSONAL_LINKS_ALLOWED_LIST = [ 'logout', 'preferences' ];

	/**
	 * @param IMessageFormatterFactory $messageFormatterFactory
	 * @param IContextSource $contextSource
	 * @param TitleFactory $titleFactory
	 * @param ServiceOptions $options
	 * @param User $user
	 * @param Title $title
	 * @param array $personalUrls
	 */
	public function __construct(
		IMessageFormatterFactory $messageFormatterFactory,
		IContextSource $contextSource,
		TitleFactory $titleFactory,
		ServiceOptions $options,
		User $user,
		Title $title,
		array $personalUrls
	) {
		parent::__construct(
			'UserMenu',
			$messageFormatterFactory,
			$contextSource
		);
		$options->assertRequiredOptions( self::CONSTRUCTOR_OPTIONS );
		if ( $user->isAnon() ) {
			$this->args = [
				'isAnon' => true,
				'login-href' => SpecialPage::getTitleFor( 'Userlogin' )->getLocalURL( [
					'returnto' => $title
				] ),
				'login-label' => Message::newFromKey( 'wikimediaapiportal-skin-login-link-label' )->text(),
			];
			return;
		}

		$items = [];

		// This behaviour may change later on, when other DevCenter functionality is implemented
		$devCenterTitle = $titleFactory->newFromText( 'Dashboard' );
		$items[] = Html::element(
			'a',
			[ 'href' => $devCenterTitle->getLocalURL() ],
			$devCenterTitle->getText()
		);

		foreach ( $personalUrls as $key => $data ) {
			if ( in_array( $key, self::PERSONAL_LINKS_ALLOWED_LIST ) ) {
				$items[] = Html::element(
					'a',
					[ 'href' => $data['href'] ],
					$data['text']
				);
			}
		}

		$label = new IconWidget( [
			'icon' => 'userAvatarOutline',
			'title' => $user->getName()
		] );

		$this->args = [
			'isAnon' => false,
			'user-menu-label' => $label,
			'user-menu-items' => $items,
		];
	}
}
