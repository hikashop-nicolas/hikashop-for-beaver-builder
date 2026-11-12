/* global jQuery, hikashopBeaverBuilder */
( function ( $ ) {
	'use strict';

	var cfg = hikashopBeaverBuilder;

	function request( data ) {
		return $.getJSON( cfg.ajaxUrl, $.extend( { action: 'hikashop_bb_products', nonce: cfg.nonce }, data ) );
	}

	function ids( picker ) {
		var value = picker.find( '.hikashop-bb-picker-value' ).val() || '';
		return value.split( ',' ).filter( function ( id ) {
			return /^\d+$/.test( id );
		} );
	}

	function save( picker ) {
		var list = picker.find( '.hikashop-bb-picker-list li' ).map( function () {
			return this.getAttribute( 'data-id' );
		} ).get();
		// The builder refreshes the preview on change
		picker.find( '.hikashop-bb-picker-value' ).val( list.join( ',' ) ).trigger( 'change' );
	}

	function item( id, text ) {
		var li = $( '<li>' ).attr( 'data-id', id );
		li.append( $( '<span>' ).text( text ) );
		li.append( $( '<button type="button" class="hikashop-bb-up">' ).attr( 'aria-label', cfg.up ).text( '↑' ) );
		li.append( $( '<button type="button" class="hikashop-bb-down">' ).attr( 'aria-label', cfg.down ).text( '↓' ) );
		li.append( $( '<button type="button" class="hikashop-bb-remove">' ).attr( 'aria-label', cfg.remove ).text( '×' ) );
		return li;
	}

	function init( picker ) {
		picker.addClass( 'is-ready' );
		var chosen = ids( picker );
		if ( ! chosen.length ) {
			return;
		}
		// The saved products need their names
		request( { ids: chosen.join( ',' ) } ).done( function ( response ) {
			if ( ! response || ! response.success ) {
				return;
			}
			var list = picker.find( '.hikashop-bb-picker-list' ).empty();
			response.data.forEach( function ( product ) {
				list.append( item( product.id, product.text ) );
			} );
		} );
	}

	function search( picker ) {
		var results = picker.find( '.hikashop-bb-picker-results' );
		var term = picker.find( '.hikashop-bb-picker-search' ).val();
		request( { q: term } ).done( function ( response ) {
			results.empty();
			var chosen = ids( picker );
			var found = response && response.success ? response.data : [];
			found.forEach( function ( product ) {
				if ( chosen.indexOf( product.id ) === -1 ) {
					results.append( $( '<li tabindex="0">' ).attr( 'data-id', product.id ).text( product.text ) );
				}
			} );
			if ( ! results.children().length ) {
				results.append( $( '<li>' ).text( cfg.none ) );
			}
		} );
	}

	var timer = null;
	$( document )
		.on( 'focus', '.hikashop-bb-picker-search', function () {
			search( $( this ).closest( '.hikashop-bb-picker' ) );
		} )
		.on( 'input', '.hikashop-bb-picker-search', function () {
			var picker = $( this ).closest( '.hikashop-bb-picker' );
			clearTimeout( timer );
			timer = setTimeout( function () {
				search( picker );
			}, 250 );
		} )
		.on( 'click keydown', '.hikashop-bb-picker-results li[data-id]', function ( e ) {
			if ( 'keydown' === e.type && 'Enter' !== e.key ) {
				return;
			}
			e.preventDefault();
			var picker = $( this ).closest( '.hikashop-bb-picker' );
			picker.find( '.hikashop-bb-picker-list' ).append( item( this.getAttribute( 'data-id' ), $( this ).text() ) );
			$( this ).remove();
			save( picker );
		} )
		.on( 'click', '.hikashop-bb-picker-list button', function () {
			var li = $( this ).closest( 'li' );
			var picker = li.closest( '.hikashop-bb-picker' );
			if ( $( this ).hasClass( 'hikashop-bb-remove' ) ) {
				li.remove();
			} else if ( $( this ).hasClass( 'hikashop-bb-up' ) ) {
				li.prev().before( li );
			} else {
				li.next().after( li );
			}
			save( picker );
		} );

	// Settings forms are built on the fly: set up each picker once it is in the page
	new MutationObserver( function () {
		$( '.hikashop-bb-picker:not(.is-ready)' ).each( function () {
			init( $( this ) );
		} );
	} ).observe( document.body, { childList: true, subtree: true } );

	// The HikaShop module settings are saved by HikaShop under an id derived from the node id
	$( document ).on( 'click', '.hikashop-bb-module-settings', function () {
		var form = $( this ).closest( '.fl-builder-settings' );
		var node = String( form.attr( 'data-node' ) || '' ).toLowerCase();
		if ( ! /^[0-9a-z]{6,}$/.test( node ) ) {
			return;
		}
		var numericId = 1000000000 + ( parseInt( node.substr( 0, 6 ), 36 ) % 1000000000 );
		var url = cfg.settingsUrl
			+ '&module_name=' + encodeURIComponent( this.getAttribute( 'data-module' ) )
			+ '&options_view=' + encodeURIComponent( this.getAttribute( 'data-view' ) )
			+ '&options_field=' + encodeURIComponent( this.getAttribute( 'data-field' ) )
			+ '&numeric_id=' + numericId;
		var popup = window.open( url, 'hikashop_module_settings', 'width=1000,height=760,scrollbars=yes' );
		var poll = setInterval( function () {
			if ( popup && ! popup.closed ) {
				return;
			}
			clearInterval( poll );
			// A changed setting makes the builder render the module again
			form.find( 'input[name="hikashop_saved_at"]' ).val( String( Date.now() ) ).trigger( 'change' );
		}, 700 );
	} );
}( jQuery ) );
