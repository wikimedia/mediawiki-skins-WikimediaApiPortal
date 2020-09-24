( function ( d ) {
	$( function () {
		var $element,
			$trigger,
			$form,
			$clearButton,
			$input,
			$searchSubmitButton;

		function searchContainer() {
			$( '.wm-search-container' ).each( function ( key, element ) {
				if ( !$( element ).is( ':visible' ) ) {
					return;
				}
				$element = $( element );
				$trigger = $element.find( '.wm-search-trigger' );
				$form = $element.find( 'form.mw-search' );
				$clearButton = $form.find( '.wm-search-clear-button' );
				$searchSubmitButton = $form.find( '.wm-search-button-submit' );
				$input = $form.find( 'input[name="search"]' );

				if ( $clearButton.length > 0 ) {
					$clearButton.on( 'click', function () {
						$input.val( '' );
						if ( $trigger.length > 0 ) {
							hideForm();
						}
					} );
				}
				if ( $trigger.length > 0 ) {
					$trigger.on( 'click', function () {
						showForm();
					} );
				}

				if ( $searchSubmitButton.length > 0 ) {
					$searchSubmitButton.on( 'click', function () {
						$form.submit();
					} );
				}

			} );
		}

		function showForm() {
			$trigger.addClass( 'wm-hidden' );
			$form.removeClass( 'wm-hidden' );
			$form.addClass( 'force-view' );
			if ( $element.hasClass( 'search-lg' ) ) {
				$( '#mw-navigation' ).addClass( 'wm-hidden' );
			} else {
				if ( $( d ).width() > 600 ) {
					return;
				}
				$( '.p-logo' ).addClass( 'wm-hidden' );
			}
		}

		function hideForm() {
			$trigger.removeClass( 'wm-hidden' );
			$form.addClass( 'wm-hidden' );
			$form.removeClass( 'force-view' );
			if ( $element.hasClass( 'search-lg' ) ) {
				$( '#mw-navigation' ).removeClass( 'wm-hidden' );
			} else {
				$( '.p-logo' ).removeClass( 'wm-hidden' );
			}
		}

		// When a page is resized rerun searchContainer to get the right $element for the new size
		$( window ).resize( function () {
			searchContainer();
		} );

		// Default: When a page is first visited or refreshed run searchContainer
		searchContainer();
	} );
}( document ) );
