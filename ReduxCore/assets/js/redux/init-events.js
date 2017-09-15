/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};

    $.redux.initEvents = function(el) {
        el.find( '.redux-presets-bar' ).on(
            'click', function() {
                window.onbeforeunload = null;
            }
        );

        el.find( '#toplevel_page_' + redux.optName.args.slug + ' .wp-submenu a, #wp-admin-bar-' + redux.optName.args.slug + ' a.ab-item' ).click(
            function( e ) {
                if ( ( el.find( '#toplevel_page_' + redux.optName.args.slug ).hasClass( 'wp-menu-open' ) || $( this ).hasClass( 'ab-item' ) ) && !$( this ).parents( 'ul.ab-submenu:first' ).hasClass( 'ab-sub-secondary' ) && $( this ).attr( 'href' ).toLowerCase().indexOf( redux.optName.args.slug + "&tab=" ) >= 0 ) {
                    e.preventDefault();
                    
                    var url = $( this ).attr( 'href' ).split( '&tab=' );
                    
                    el.find( '#' + url[1] + '_section_group_li_a' ).click();
                    
                    $( this ).parents( 'ul:first' ).find( '.current' ).removeClass( 'current' );
                    $( this ).addClass( 'current' );
                    $( this ).parent().addClass( 'current' );
                    
                    return false;
                }
            }
        );

        // Save button clicked
        el.find( '.redux-action_bar input' ).on(
            'click', function( e ) {
                if ( $( this ).attr( 'name' ) === redux.optName.args.opt_name + '[defaults]' ) {
                    // Defaults button clicked
                    if ( !confirm( redux.optName.args.reset_confirm ) ) {
                        return false;
                    }
                } else if ( $( this ).attr( 'name' ) === redux.optName.args.opt_name + '[defaults-section]' ) {
                    // Default section clicked
                    if ( !confirm( redux.optName.args.reset_section_confirm ) ) {
                        return false;
                    }
                }

                window.onbeforeunload = null;

                if ( redux.optName.args.ajax_save === true ) {
                    $.redux.ajax_save( $( this ) );
                    e.preventDefault();
                }
            }
        );

        $( '.expand_options' ).click(
            function( e ) {
                e.preventDefault();

                var container = el; //$( '.redux-container' );
                
                if ( $( container ).hasClass( 'fully-expanded' ) ) {
                    $( container ).removeClass( 'fully-expanded' );

                    var tab = $.cookie( "redux_current_tab" );

                    el.find( '#' + tab + '_section_group' ).fadeIn(
                        200, function() {
                            if ( el.find( '#redux-footer' ).length !== 0 ) {
                                $.redux.stickyInfo(); // race condition fix
                            }
                            
                            $.redux.initFields();
                        }
                    );
                }

                $.redux.expandOptions( $( this ).parents( '.redux-container:first' ) );

                return false;
            }
        );

        if ( el.find( '.saved_notice' ).is( ':visible' ) ) {
            el.find( '.saved_notice' ).slideDown();
        }

        $( document.body ).on(
            'change', '.redux-field input, .redux-field textarea, .redux-field select', function() {
                if ( !$( this ).hasClass( 'noUpdate' ) ) {
                    redux_change( $( this ) );
                }
            }
        );

        var stickyHeight = el.find( '#redux-footer' ).height();

        el.find( '#redux-sticky-padder' ).css(
            {
                height: stickyHeight
            }
        );

        el.find( '#redux-footer-sticky' ).removeClass( 'hide' );

        if ( el.find( '#redux-footer' ).length !== 0 ) {
            $( window ).scroll(
                function() {
                    $.redux.stickyInfo();
                }
            );

            $( window ).resize(
                function() {
                    $.redux.stickyInfo();
                }
            );
        }

        el.find( '.saved_notice' ).delay( 4000 ).slideUp();
    };    
    
})(jQuery);