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
namespace MediaWiki\Skin\WikimediaApiPortal\Test;

use MediaWiki\Config\Config;
use MediaWiki\Config\ServiceOptions;
use MediaWiki\Context\IContextSource;
use MediaWiki\Language\Language;
use MediaWiki\Message\Message;
use MediaWiki\Message\MessageFormatterFactory;
use MediaWiki\Message\TextFormatter;
use MediaWiki\Page\PageProps;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\Request\WebRequest;
use MediaWiki\Skin\WikimediaApiPortal\Skin;
use MediaWiki\Skin\WikimediaApiPortal\TemplateParser;
use MediaWiki\Skin\WikimediaApiPortal\WikimediaApiPortalTemplate;
use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\Title\NamespaceInfo;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use MediaWiki\User\User;
use Wikimedia\Message\IMessageFormatterFactory;
use Wikimedia\Message\MessageValue;
use Wikimedia\ObjectCache\EmptyBagOStuff;

trait ComponentMockTrait {
	/**
	 * @return Skin
	 */
	private function newSkin(): Skin {
		$skin = $this->createNoOpMock(
			Skin::class,
			[
				'getTitle',
				'getUser',
				'getLanguage',
				'getRequest',
				'msg',
				'addToSidebarPlain'
			]
		);

		$skin->method( 'getTitle' )->willReturn( $this->newTitle() );

		$skin->method( 'getUser' )->willReturn( $this->newUser() );

		$language = $this->createNoOpMock( Language::class, [ 'getCode' ] );
		$language->method( 'getCode' )->willReturn( 'qqq' );
		$skin->method( 'getLanguage' )->willReturn( $language );

		$request = $this->createNoOpMock( WebRequest::class, [ 'getRawVal' ] );
		$request->method( 'getRawVal' )->willReturn( 'rawvalue' );
		$skin->method( 'getRequest' )->willReturn( $request );

		$skin->method( 'msg' )->willReturnCallback( function ( $msgKey ) {
			$message = $this->createNoOpMock( Message::class, [ 'parse' ] );
			$message->method( 'parse' )->willReturn( $msgKey );
			return $message;
		} );

		$skin->method( 'addToSidebarPlain' )->willReturn( [] );

		return $skin;
	}

	/**
	 * @return IMessageFormatterFactory
	 */
	private function newMessageFormatterFactory(): IMessageFormatterFactory {
		$textFormatter = $this->createNoOpMock(
			TextFormatter::class,
			[ 'format' ]
		);
		$textFormatter->method( 'format' )->willReturnCallback(
			static function ( MessageValue $messageValue ) {
				return $messageValue->getKey();
			}
		);
		$messageFormatterFactory = $this->createNoOpMock(
			MessageFormatterFactory::class,
			[ 'getTextFormatter' ]
		);
		$messageFormatterFactory->method( 'getTextFormatter' )->willReturn( $textFormatter );

		return $messageFormatterFactory;
	}

	/**
	 * @return IContextSource
	 */
	private function newContextSource(): IContextSource {
		return $this->newSkin();
	}

	/**
	 * @return Title
	 */
	private function newTitle(): Title {
		$title = $this->createNoOpMock(
			Title::class,
			[
				'getLinkURL',
				'getLocalURL',
				'getText',
				'isSpecialPage',
				'isTalkPage',
				'isMainPage',
				'isSubpage',
				'hasSubpages',
				'__toString'
			]
		);
		$title->method( 'getLinkURL' )->willReturn( 'http://example.com' );
		$title->method( 'getLocalURL' )->willReturn( '/testurl' );
		$title->method( 'getText' )->willReturn( 'MainPage' );
		$title->method( 'isSpecialPage' )->willReturn( false );
		$title->method( 'isTalkPage' )->willReturn( false );
		$title->method( 'isMainPage' )->willReturn( false );
		$title->method( 'isSubpage' )->willReturn( false );
		$title->method( 'hasSubpages' )->willReturn( false );
		$title->method( '__toString' )->willReturn( 'MainPage' );
		return $title;
	}

	/**
	 * @return TitleFactory
	 */
	private function newTitleFactory(): TitleFactory {
		$titleFactory = $this->createNoOpMock(
			TitleFactory::class,
			[ 'newMainPage' ]
		);
		$titleFactory->method( 'newMainPage' )->willReturn( $this->newTitle() );
		return $titleFactory;
	}

	/**
	 * @return SpecialPage
	 */
	private function newSpecialPage(): SpecialPage {
		$specialPage = $this->createNoOpMock(
			SpecialPage::class,
			[ 'getDescription' ]
		);
		$specialPage->method( 'getDescription' )->willReturn( 'special page description' );
		return $specialPage;
	}

	/**
	 * @return SpecialPageFactory
	 */
	private function newSpecialPageFactory(): SpecialPageFactory {
		$specialPageFactory = $this->createNoOpMock(
			SpecialPageFactory::class,
			[ 'getPage' ]
		);
		$specialPageFactory->method( 'getPage' )->willReturn( $this->newSpecialPage() );
		return $specialPageFactory;
	}

	/**
	 * @return ExtensionRegistry
	 */
	private function newExtensionRegistry(): ExtensionRegistry {
		return $this->createNoOpMock( ExtensionRegistry::class );
	}

	/**
	 * @return User
	 */
	private function newUser(): User {
		$user = $this->createNoOpMock(
			User::class,
			[ 'isAnon' ]
		);
		$user->method( 'isAnon' )->willReturn( true );
		return $user;
	}

	/**
	 * @return NamespaceInfo
	 */
	private function newNamespaceInfo(): NamespaceInfo {
		return $this->createNoOpMock( NamespaceInfo::class );
	}

	/**
	 * @return PermissionManager
	 */
	private function newPermissionManager(): PermissionManager {
		$permissionManager = $this->createNoOpMock(
			PermissionManager::class,
			[ 'userHasRight' ]
		);
		$permissionManager->method( 'userHasRight' )->willReturn( true );
		return $permissionManager;
	}

	/**
	 * @return PageProps
	 */
	private function newPageProps(): PageProps {
		return $this->createNoOpMock( PageProps::class );
	}

	/**
	 * @return Config
	 */
	private function newConfig(): Config {
		$config = $this->createNoOpMock(
			Config::class,
			[ 'has', 'get' ]
		);
		$config->method( 'has' )->willReturn( true );
		$config->method( 'get' )->will( $this->returnArgument( 0 ) );
		return $config;
	}

	/**
	 * @return WikimediaApiPortalTemplate
	 */
	private function newWikimediaApiPortalTemplate(): WikimediaApiPortalTemplate {
		$template = $this->createNoOpMock(
			WikimediaApiPortalTemplate::class,
			[
				'get',
				'getSkin',
				'getPrimaryNavSidebar'
			]
		);
		$template->method( 'get' )->willReturnCallback(
			static function ( $param ) {
				if ( $param === 'content_navigation' ) {
					return [];
				}
				return $param;
			}
		);
		$template->method( 'getSkin' )->willReturn( $this->newSkin() );
		$template->method( 'getPrimaryNavSidebar' )->willReturn( [] );
		$template->data['sitenotice'] = 'sitenotice';
		$template->data['personal_urls'] = [];
		return $template;
	}

	/**
	 * @return TemplateParser
	 */
	private function newTemplateParser(): TemplateParser {
		$parser = new TemplateParser( __DIR__ . '/../../../components', new EmptyBagOStuff() );
		$parser->enableRecursivePartials( true );
		return $parser;
	}

	/**
	 * @return ServiceOptions
	 */
	protected function newServiceOptions(): ServiceOptions {
		Assert::fail( 'Must be overridden if required' );
	}

	/**
	 * provided by MediaWikiTestCaseTrait
	 * @param string $type
	 * @param string[] $allow methods to allow
	 */
	abstract protected function createNoOpMock( $type, $allow = [] );
}
