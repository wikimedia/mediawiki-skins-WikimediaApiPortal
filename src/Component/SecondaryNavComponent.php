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

use NamespaceInfo;
use PageProps;
use Title;
use TitleFactory;

class SecondaryNavComponent extends Component {
	/**
	 * @param Title $title
	 * @param array $predefined
	 * @param NamespaceInfo $namespaceInfo
	 * @param TitleFactory $titleFactory
	 * @param PageProps $pageProps
	 */
	public function __construct(
		Title $title,
		array $predefined,
		NamespaceInfo $namespaceInfo,
		TitleFactory $titleFactory,
		PageProps $pageProps
	) {
		parent::__construct( 'SecondaryNav' );

		$parsed = $this->parsePredefinedNavStructure( $title, $predefined );
		if ( $parsed ) {
			$this->args = [ 'items' => $parsed ];
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
	 * @param Title $parent
	 * @param PageProps $pageProps
	 * @return array
	 */
	private function getPageNav(
		Title $currentTitle,
		Title $parent,
		PageProps $pageProps
	) : array {
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
	 * Get page navigation hierarchy from predefined list
	 * @param Title $title
	 * @param array $predefined
	 * @return ?array
	 */
	private function parsePredefinedNavStructure( Title $title, array $predefined ) : ?array {
		foreach ( $predefined as $root => $subpages ) {
			$currentURL = $title->getLocalURL();
			$matches = array_filter( $subpages, function ( $item ) use ( $currentURL ) {
				return $item['href'] === $currentURL;
			} );
			if ( count( $matches ) ) {
				foreach ( $subpages as &$subpage ) {
					$subpage['isActive'] = $this->isActiveLink( $title, $subpage['href'] );
					$subpage['subpages'] = false;
				}
				return [ [
					'text' => $root,
					'href' => '#',
					'isActive' => true,
					'subpages' => $subpages
				] ];
			}
		}

		return null;
	}

	/**
	 * Whether the link points to the current title (or a subpage thereof).
	 * @param Title $title
	 * @param string $link
	 * @return bool
	 */
	private function isActiveLink( Title $title, string $link ) : bool {
		// Match logic in Skin::addToSidebarPlain
		$currentLink = $title->fixSpecialName()->getLinkURL();

		return $link === $currentLink || strpos( $currentLink, "$link/" ) === 0;
	}

	/**
	 * Whether the Title points to the current title (or a subpage thereof).
	 * @param Title $currentTitle
	 * @param Title $title
	 * @return bool
	 */
	private function isActiveTitle( Title $currentTitle, Title $title ) : bool {
		// Match logic in Skin::addToSidebarPlain
		$currentTitle = $currentTitle->fixSpecialName();

		return $currentTitle->equals( $title ) || $currentTitle->isSubpageOf( $title );
	}
}
