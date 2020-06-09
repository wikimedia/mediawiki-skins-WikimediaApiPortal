# Installation

### Prerequisites
Chameleon skin (2.x) must be installed https://github.com/ProfessionalWiki/chameleon/blob/master/docs/installation.md

### Load and enable skin
      wfLoadSkin( 'WikimediaApiPortal' );
      $wgDefaultSkin = 'wikimediaapiportal';
      
Note: Installation procedure will change depending on the outcome of Chameleon installation improvements

### Config

    $wgRawHtml = true; // To enable complex pages in HTML
    $wgWikimediaApiPortalSkinAdjustPageTitle = true; //If true, all subpages, will display only the last part of the page as the page title. This is done to allow for the site to be structured using subpages, while still keeping the page names that are display nice
    $wgWikimediaApiPortalSkinAllowedPersonalUrls = [ 'uls', 'logout' ]; // Links that will be displayed in user menu (after clicking on the user icon). All others are discarted

## FancyCaptcha

Install it as described in https://www.mediawiki.org/wiki/Extension:ConfirmEdit#FancyCaptcha
