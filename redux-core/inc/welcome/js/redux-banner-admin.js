/* global console:true, ajaxurl */

(function( $ ) {
    'use strict';

    $.redux_banner = $.redux_banner || {};
  
        
    $( document ).ready( function() {
        $( '.redux-banner-svg-dismiss' ).on( 'click', function () {
            $( '.redux-banner-full-container' ).hide();
        });            
    });


})( jQuery );
