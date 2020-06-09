( function( $ ) {
	$( function() {
		var $header = $( '.wm-header.fixed-top:visible' );
		if ( !$header.length ) {
			return;
		}
		$( 'a[href*="#"]:not([href="#"])' ).click( function() {
			adjustScroll( $header, this.hash );
		} );

		$( window ).on( 'hashchange', function( e ) {
			adjustScroll( $header );
		} );
	} );

	function adjustScroll( $header, hash, animate ) {
		hash = hash || window.location.hash;
		var target = $( hash ),
			headerHeight = $header.height() + 5;

		if ( target.length ) {
			$( 'html,body' ).animate( {
				scrollTop: target.offset().top - headerHeight
			}, 500 );
			return false;
		}
	}
} )( jQuery );
