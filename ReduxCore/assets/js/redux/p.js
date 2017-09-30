/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};

    $( document ).ready(
        function() {
            if ( redux.optName.rAds ) {

                var el;
                var instance = redux.optName.core_instance;
                var thread = redux.optName.core_thread;

                if ( $( '#redux-header' ).length > 0 ) {
                    $( '#redux-header' ).append( '<div id="' + instance + '"></div>' );
                    el = $( '#redux-header' );
                } else {
                    $( '#customize-theme-controls ul' ).first().prepend(
                        '<li id="' + thread + '" class="accordion-section" style="position: relative;"><div id="' + instance + '"></div></li>' );
                    el = $( '#' + thread );
                }

                el.css( 'position', 'relative' );

                el.find( '#' + instance ).attr(
                    'style',
                    'position:absolute; top: 6px; right: 9px; display:block !important;overflow:hidden;'
                ).css( 'left', '-99999px' );

                el.find( '#' + instance ).html( redux.optName.rAds.replace( /<br\s?\/?>/, '' ) );

                var rAds = el.find( '#' + instance );

                $( rAds ).hide();
                rAds.bind( "DOMSubtreeModified", function() {
                    if ( $( this ).html().indexOf( "<a href" ) >= 0 ) {
                        rAds.find( 'img' ).css( 'visibility', 'hidden' );
                        setTimeout( function() {
                            rAds.show();
                            $.redux.resizeAds();
                        }, 400 );
                        rAds.find( 'img' ).css( 'visibility', 'inherit' );
                        rAds.unbind( "DOMSubtreeModified" );
                    }
                } );
                $( window ).resize(
                    function() {
                        $.redux.resizeAds();
                    }
                );
            }
        }
    );

    $.redux.scaleToRatio = function( el, maxHeight, maxWidth ) {
        var ratio = 0;  // Used for aspect ratio
        var width = el.attr( 'data-width' );
        var height = el.attr( 'data-height' );
        var eHeight = el.height();

        if ( !width ) {
            width = el.width();
            el.attr( 'data-width', width );
        }

        if ( !height || eHeight > height ) {
            height = eHeight;

            el.attr( 'data-height', height );
            el.css( "width", 'auto' );
            el.attr( 'data-width', el.width() );

            width = el.width();
        }


        // Check if the current width is larger than the max
        if ( width > maxWidth ) {
            ratio = maxWidth / width;   // get ratio for scaling image
            el.css( "width", maxWidth ); // Set new width
            el.css( "height", height * ratio );  // Scale height based on ratio
            height = height * ratio;    // Reset height to match scaled image
            width = width * ratio;    // Reset width to match scaled image
        } else {
            el.css( "width", 'auto' );   // Set new height
        }

        // Check if current height is larger than max
        if ( height > maxHeight ) {
            ratio = maxHeight / height; // get ratio for scaling image
            el.css( "height", maxHeight );   // Set new height
            el.css( "width", width * ratio );    // Scale width based on ratio
            width = width * ratio;    // Reset width to match scaled image
            height = height * ratio;    // Reset height to match scaled image


        } else {
            el.css( "height", 'auto' );   // Set new height
        }

        var test = ($( document.getElementById( 'redux-header' ) ).height() - el.height()) / 2;

        if ( test > 0 ) {
            el.css( "margin-top", test );
        } else {
            el.css( "margin-top", 0 );
        }

        if ( $( '#redux-header .redux_field_search' ) ) {
            $( '#redux-header .redux_field_search' ).css( 'right', ($( el ).width() + 20) );
        }
    };

    $.redux.resizeAds = function() {
        var el = $( '#redux-header' );
        var maxHeight = el.height();
        var rAds = el.find( '#' + redux.optName.core_instance );
        var maxWidth;

        if ( el.length ) {
            maxWidth = el.width() - el.find( '.display_header' ).width() - 30;
        } else {
            el = $( '#customize-info' );

            maxWidth = el.width();
        }

        $( rAds ).find( 'video' ).each(
            function() {
                $.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );

        $( rAds ).find( 'img' ).each(
            function() {
                $.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );

        $( rAds ).find( 'div' ).each(
            function() {
                $.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );

        if ( rAds.css( 'left' ) === "-99999px" ) {
            rAds.css( 'display', 'none' ).css( 'left', 'auto' );
        }

        rAds.fadeIn( 'slow' );
    };
})( jQuery );