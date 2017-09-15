/*global jQuery, document, redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects             = redux.field_objects || {};
    redux.field_objects.sortable    = redux.field_objects.sortable || {};

    var scrollDir = '';

    redux.field_objects.sortable.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-sortable:visible' );
        }

        $( selector ).each(
            function() {
                var el      = $( this );
                var parent  = el;
                
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }
                
                el.find( ".redux-sortable" ).sortable(
                    {
                        handle: ".drag",
                        placeholder: "placeholder",
                        opacity: 0.7,
                        scroll: false,
                        out: function( event, ui ) {
                            if ( !ui.helper ) return;
                            if ( ui.offset.top > 0 ) {
                                scrollDir = 'down';
                            } else {
                                scrollDir = 'up';
                            }
                            
                            redux.field_objects.sortable.scrolling( $( this ).parents( '.redux-field-container:first' ) );
                        },
                        over: function( event, ui ) {
                            scrollDir = '';
                        },
                        deactivate: function( event, ui ) {
                            scrollDir = '';
                        },
                        update: function(event, ui) {
                            redux_change( $( this ) );
                        }
                    }
                );

                el.find( '.checkbox_sortable' ).on(
                    'click', function() {
                        if ( $( this ).is( ":checked" ) ) {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( 1 );
                        } else {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( '' );
                        }
                    }
                );
            }
        );
    };

    redux.field_objects.sortable.scrolling = function( selector ) {
        if (selector === undefined) {
            return;
        }
        
        var $scrollable = selector.find( ".redux-sorter" );

        if ( scrollDir == 'up' ) {
            $scrollable.scrollTop( $scrollable.scrollTop() - 20 );
            setTimeout( redux.field_objects.sortable.scrolling, 50 );
        } else if ( scrollDir == 'down' ) {
            $scrollable.scrollTop( $scrollable.scrollTop() + 20 );
            setTimeout( redux.field_objects.sortable.scrolling, 50 );
        }
    };
})( jQuery );