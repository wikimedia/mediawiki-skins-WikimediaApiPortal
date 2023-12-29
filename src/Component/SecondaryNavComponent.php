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
use MediaWiki\Config\ServiceOptions;
use MediaWiki\Page\PageProps;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\Title\NamespaceInfo;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use Wikimedia\Message\IMessageFormatterFactory;

class SecondaryNavComponent extends MessageComponent {
	public const CONSTRUCTOR_OPTIONS = [
		'WMAPIPSidebarSpecialPages',
	];

	/**
	 * @param ServiceOptions $options
	 * @param IMessageFormatterFactory $messageFormatterFactory
	 * @param IContextSource $contextSource
	 * @param Title $title
	 * @param NamespaceInfo $namespaceInfo
	 * @param TitleFactory $titleFactory
	 * @param SpecialPageFactory $specialPageFactory
	 * @param PageProps $pageProps
	 */
	public function __construct(
		ServiceOptions $options,
		IMessageFormatterFactory $messageFormatterFactory,
		IContextSource $contextSource,
		Title $title,
		NamespaceInfo $namespaceInfo,
		TitleFactory $titleFactory,
		SpecialPageFactory $specialPageFactory,
		PageProps $pageProps
	) {
		parent::__construct( 'SecondaryNav', $messageFormatterFactory, $contextSource );

		$options->assertRequiredOptions( self::CONSTRUCTOR_OPTIONS );

		if ( $title->isSpecialPage() ) {
			$specialSidebar = $this->getSpecialSidebar(
				$title,
				$options->get( 'WMAPIPSidebarSpecialPages' ),
				$titleFactory,
				$specialPageFactory
			);
			if ( $specialSidebar ) {
				$this->args = [
					'items' => $specialSidebar
				];
			}
			return;
		}

		if ( $title->isTalkPage() ) {
			$subjectNS = $namespaceInfo->getSubject( $title->getNamespace() );
			$title = $titleFactory->makeTitleSafe( $subjectNS, $title->getDBkey() );
		}

		if ( !( $title && ( $title->isSubpage() || $title->hasSubpages() ) ) ) {
			$this->args = null;
			return;
		}

		$root = $titleFactory->makeTitleSafe( $title->getNamespace(), $title->getRootText() );
		if ( !$root ) {
			$this->args = null;
			return;
		}

		$this->args = [
			'items' => $this->getPageNav( $title, $root, $pageProps )
		];
	}

	/**
	 * @param Title $currentTitle
	 * @param array $sidebarSpecialPages
	 * @param TitleFactory $titleFactory
	 * @param SpecialPageFactory $specialPageFactory
	 * @return ?array
	 */
	private function getSpecialSidebar(
		Title $currentTitle,
		array $sidebarSpecialPages,
		TitleFactory $titleFactory,
		SpecialPageFactory $specialPageFactory
	): ?array {
		$items = [];
		$found = false;
		foreach ( $sidebarSpecialPages as $specialPageName ) {
			$title = $titleFactory->newFromText( $specialPageName, NS_SPECIAL );
			$specialPageObj = $specialPageFactory->getPage( $specialPageName );
			if ( $title && $specialPageObj ) {
				if ( $this->isActiveTitle( $currentTitle, $title ) ) {
					$found = true;
					$isActive = true;
					$href = '#';
				} else {
					$isActive = false;
					$href = $title->getLocalURL();
				}
				$items[] = [
					'text' => $specialPageObj->getDescription(),
					'href' => $href,
					'isActive' => $isActive,
					'subpages' => false
				];
			}
		}
		if ( $found ) {
			return $items;
		}
		return null;
	}

	/**
	 * @param Title $currentTitle
	 * @param Title $parent
	 * @param PageProps $pageProps
	 * @return array
	 */
	private function getPageNav(
		Title $currentTitle,
		Title $parent,
		PageProps $pageProps
	): array {
		$subpages = $parent->getSubpages();
		if ( !count( $subpages ) ) {
			return [];
		}
		$defaultsort = $pageProps->getProperties( $subpages, 'defaultsort' );
		$nav = [];
		foreach ( $subpages as $page ) {
			if ( $page->getBaseText() !== $parent->getText() ) {
				// Not direct sub
				continue;
			}

			if ( $defaultsort && array_key_exists( $page->getArticleID(), $defaultsort ) ) {
				$key = $defaultsort[$page->getArticleID()];
			} else {
				$key = $page->getSubpageText();
			}
			$nav[$key] = [
				'isActive' => $this->isActiveTitle( $currentTitle, $page ),
				'href' => $page->getLocalURL(),
				'text' => $page->getSubpageText(),
				'subpages' => $this->getPageNav( $currentTitle, $page, $pageProps ) ?: false,
			];
		}
		ksort( $nav );
		return $nav;
	}

	/**
	 * Whether the Title points to the current title (or a subpage thereof).
	 * @param Title $currentTitle
	 * @param Title $title
	 * @return bool
	 */
	private function isActiveTitle( Title $currentTitle, Title $title ): bool {
		$currentTitle = $currentTitle->fixSpecialName();
		return $currentTitle->equals( $title ) || $currentTitle->isSubpageOf( $title );
	}
}
