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
namespace MediaWiki\Skin\WikimediaApiPortal;

use BaseTemplate;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

/**
 * Class WikimediaApiPortalTemplate
 * @package MediaWiki\Skin\WikimediaApiPortal
 * @method Skin getSkin()
 */
class WikimediaApiPortalTemplate extends BaseTemplate {

	public function execute() {
		$this->setSubpageDisplayTitle();

		$templateParser = new TemplateParser( __DIR__ . '/../components' );
		$templateParser->enableRecursivePartials( true );

		$componentFactory = MediaWikiServices::getInstance()
			->getService( 'WAPSkinComponentFactory' );

		echo $componentFactory
			->createMainComponent( $this )
			->parseTemplate( $templateParser );
	}

	/**
	 * Set page title to the last subpage only, since the subpage structure
	 * will be shown in the navigation and bread crumbs
	 *
	 * Subpages will display only the last part of the page as the page title.
	 * This is done to allow for the site to be structured using subpages,
	 * while still keeping the page names that are display nice
	 */
	private function setSubpageDisplayTitle() {
		$requestedAction = $this->getSkin()->getRequest()->getRawVal( 'action', 'view' );
		if ( $requestedAction !== 'view' ) {
			return;
		}
		$title = $this->getSkin()->getTitle();
		if ( !MediaWikiServices::getInstance()
			->getNamespaceInfo()
			->hasSubpages( $title->getNamespace() ) ) {
			return;
		}
		if ( $title->isSubpage() ) {
			$newTitle = Title::makeTitle( $title->getNamespace(), $title->getSubpageText() );
			$this->set( 'title', $newTitle->getPrefixedText() );
		}
	}

	/** @return array */
	public function getPrimaryNavSidebar() {
		return $this->getSidebar( [
			'search' => false,
			'toolbox' => false,
			'languages' => false,
		] );
	}
}
