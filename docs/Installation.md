# Installation

## Install and enable skin

First install the Bootstrap and SCSS extensions (run from the MediaWiki directory):

  COMPOSER=composer.local.json composer require --no-update mediawiki/bootstrap:4.2
  composer update mediawiki/bootstrap --no-dev -o

Add to `LocalSettings.php`:

	wfLoadExtension( 'Bootstrap' );
	wfLoadSkin( 'WikimediaApiPortal' );
	$wgDefaultSkin = 'wikimediaapiportal';

## Recommended configuration

	// Needed (TODO: Document why).
	$wgUseMediaWikiUIEverywhere = true;

	// Needed to enable subpage navigation and shortened display titles
  $wgNamespacesWithSubpages[NS_MAIN] = true;

## Site customization

* `MediaWiki:Sidebar`: Use this to control the header navigation bar,
  e.g. set to plain list of main-namespace articles (instead of the default sections).

* `MediaWiki:FooterLinks`: This this to control the footer area, will
  be empty otherwise.
