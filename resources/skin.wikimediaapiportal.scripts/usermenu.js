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
$( () => {
	$( '.user-menu' ).each( function () {
		const items = [];
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

		const tooltip =
			$( this ).find( '.oo-ui-icon-userAvatarOutline' )[ 0 ].title;
		const menu = new OO.ui.ButtonMenuSelectWidget( {
			icon: 'userAvatarOutline',
			framed: false,
			title: tooltip,
			menu: {
				horizontalPosition: 'end',
				items: items
			}
		} );

		menu.getMenu().on( 'choose', ( menuOption ) => {
			location.replace( menuOption.getData() );
		} );

		$( this ).replaceWith( menu.$element );
	} );
} );
