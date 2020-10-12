# Installation

### Load and enable skin

	wfLoadSkin( 'WikimediaApiPortal' );
	$wgDefaultSkin = 'wikimediaapiportal';

## Recommended configuration

	// Needed to enable subpage navigation and shortened display titles
	$wgNamespacesWithSubpages[NS_MAIN] = true;

## Site customization

* `MediaWiki:Sidebar`: Use this to control the header navigation bar,
  e.g. set to plain list of main-namespace articles (instead of the default sections).
