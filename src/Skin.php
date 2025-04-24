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

use MediaWiki\Output\OutputPage;
use MediaWiki\Skin\QuickTemplate;
use MediaWiki\Skin\SkinTemplate;

class Skin extends SkinTemplate {
	/** @var string */
	public $skinname = 'wikimediaapiportal';
	/** @var string */
	public $template = WikimediaApiPortalTemplate::class;

	/**
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );

		// Enable responsive behaviour on mobile browsers
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1, shrink-to-fit=no' );
	}

	/** @return QuickTemplate */
	protected function setupTemplateForOutput(): QuickTemplate {
		$template = parent::setupTemplateForOutput();
		$template->set( 'skin', $this );

		$output = $this->getOutput();
		$output->enableOOUI();
		$output->addModuleStyles( [
			'oojs-ui.styles.icons-user',
			'oojs-ui.styles.icons-content',
			'oojs-ui.styles.icons-alerts',
			'oojs-ui.styles.icons-editing-core',
			'oojs-ui.styles.icons-editing-advanced',
			'oojs-ui.styles.icons-interactions',
			'oojs-ui.styles.icons-layout',
			'oojs-ui.styles.icons-movement',
			'oojs-ui.styles.icons-wikimedia',
			'skin.wikimediaapiportal.styles'
		] );
		$output->addModules( "skin.wikimediaapiportal.scripts" );
		if ( $this->getTitle()->isMainPage() &&
			( $this->getRequest()->getRawVal( 'action' ) ?? 'view' ) === 'view' ) {
			$output->addModuleStyles( [
				"skin.wikimediaapiportal.mainpage",
			] );
		}

		return $template;
	}
}
