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
	$( '.primary-nav-menu' ).each( function () {
		const items = [];
		$( this ).find( '.primary-nav-menu-item' ).each( function () {
			items.push(
				new OO.ui.MenuOptionWidget( {
					data: this.href,
					label: this.text
				} )
			);
		} );

		const label = $( this ).children().first().text();
		const menu = new OO.ui.ButtonMenuSelectWidget( {
			label: label,
			indicator: 'down',
			framed: false,
			classes: [ 'primary-nav-menu-button' ],
			menu: {
				horizontalPosition: 'end',
				items: items
			}
		} );

		menu.getMenu().on( 'choose', ( menuOption ) => {
			location.replace( menuOption.getData() );
		} );

		$( this ).children().first().replaceWith( menu.$element );
	} );
} );
