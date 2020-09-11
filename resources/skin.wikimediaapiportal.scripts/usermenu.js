$( function () {
	$( '.user-menu' ).each( function () {
		var items = [];
		$( this ).find( 'a' ).each( function () {
			items.push(
				// eslint-disable-next-line mediawiki/class-doc
				new OO.ui.MenuOptionWidget( {
					data: this.href,
					label: this.text,
					icon: this.dataset.icon || '',
					classes: [ this.dataset.class || '' ]
				} )
			);
		} );

		var tooltip =
			$( this ).find( '.oo-ui-icon-userAvatarOutline' )[ 0 ].title;
		var menu = new OO.ui.ButtonMenuSelectWidget( {
			icon: 'userAvatarOutline',
			framed: false,
			title: tooltip,
			menu: {
				horizontalPosition: 'end',
				items: items
			}
		} );

		menu.getMenu().on( 'choose', function ( menuOption ) {
			location.replace( menuOption.getData() );
		} );

		$( this ).replaceWith( menu.$element );
	} );
} );
