/* global console:true, ajaxurl */

(function( $ ) {
    'use strict';
    $.redux_banner = $.redux_banner || {};
    $( document ).ready( function() {
        $( '.redux-banner-svg-dismiss' ).on( 'click', function () {
            $( '.redux-banner-full-container' ).hide();
        });
		$( '.redux-connection-banner-action' ).on( 'click', function (e) {
			$( '#redux-connect-message' ).hide();
			$.get( $( this ).attr('href'), {}, function( response ) {} );
			e.preventDefault();
		});
		jQuery('.redux-insights-data-we-collect').on('click', function(e) {
			e.preventDefault();
			jQuery(this).parents('.updated').find('p.description').slideToggle('fast');
		});
    });
})( jQuery );
