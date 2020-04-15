# Installation

### Load and enable skin

	wfLoadSkin( 'WikimediaApiPortal' );
	$wgDefaultSkin = 'wikimediaapiportal';

## Recommended configuration

	// Needed (TODO: Document why).
	$wgUseMediaWikiUIEverywhere = true;

	// Needed to enable subpage navigation and shortened display titles
	$wgNamespacesWithSubpages[NS_MAIN] = true;

	// To enable a complex Main Page
	$wgRawHtml = true;

## Site customization

* `MediaWiki:Sidebar`: Use this to control the header navigation bar,
  e.g. set to plain list of main-namespace articles (instead of the default sections).

* `MediaWiki:FooterLinks`: This this to control the footer area, will
  be empty otherwise.

## FancyCaptcha

Install it as described in https://www.mediawiki.org/wiki/Extension:ConfirmEdit#FancyCaptcha
