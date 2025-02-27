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
( function () {
	$( () => {
		const $header = $( '.wm-header.fixed-top:visible' );
		if ( !$header.length ) {
			return;
		}
		$( 'a[href*="#"]:not([href="#"])' ).on( 'click', function () {
			adjustScroll( $header, this.hash );
		} );

		$( window ).on( 'hashchange', () => {
			adjustScroll( $header );
		} );

		adjustScroll( $header );
	} );

	function adjustScroll( $header, hash ) {
		hash = hash || window.location.hash;
		const $target = $( hash ),
			headerHeight = $header.height() + 5;

		if ( $target.length ) {
			$( 'html, body' ).animate( {
				scrollTop: $target.offset().top - headerHeight
			}, 500 );
			return false;
		}
	}
}() );
