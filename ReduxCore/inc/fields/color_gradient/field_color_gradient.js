/*
 Field Color Gradient
 */

/*global jQuery, document, redux_change, redux*/

(function( $ ) {
    'use strict';

    redux.field_objects                 = redux.field_objects || {};
    redux.field_objects.color_gradient  = redux.field_objects.color_gradient || {};

    redux.field_objects.color_gradient.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-color_gradient:visible' );
        }

        $( selector ).each(
            function() {
                var el      = $( this );
                var parent  = el;

                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                
                if ( parent.is( ":hidden" ) ) {
                    return;
                }
                
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }

                el.find( '.redux-color-init' ).wpColorPicker({
                    change: function( e, ui ) {
                        $( this ).val( ui.color.toString() );

                        redux.field_objects.color_gradient.changeValue($(this), true);

                        el.find( '#' + e.target.getAttribute( 'data-id' ) + '-transparency' ).removeAttr( 'checked' );
                    },
                    clear: function( e, ui ) {
                        $( this ).val( ui.color.toString() );

                        redux.field_objects.color_gradient.changeValue($( this ).parent().find( '.redux-color-init' ), true);
                    }
                });

                el.find( '.redux-color' ).on(
                    'keyup', function() {
                        var value   = $( this ).val();
                        var color   = colorValidate( this );
                        var id      = '#' + $( this ).attr( 'id' );

                        if ( value === "transparent" ) {
                            $( this ).parent().parent().find( '.wp-color-result' ).css(
                                'background-color', 'transparent'
                            );

                            el.find( id + '-transparency' ).attr( 'checked', 'checked' );
                        } else {
                            el.find( id + '-transparency' ).removeAttr( 'checked' );

                            if ( color && color !== $( this ).val() ) {
                                $( this ).val( color );
                            }
                        }
                    }
                );

                // Replace and validate field on blur
                el.find( '.redux-color' ).on(
                    'blur', function() {
                        var value   = $( this ).val();
                        var id      = '#' + $( this ).attr( 'id' );

                        if ( value === "transparent" ) {
                            $( this ).parent().parent().find( '.wp-color-result' ).css(
                                'background-color', 'transparent'
                            );

                            el.find( id + '-transparency' ).attr( 'checked', 'checked' );
                        } else {
                            if ( colorValidate( this ) === value ) {
                                if ( value.indexOf( "#" ) !== 0 ) {
                                    $( this ).val( $( this ).data( 'oldcolor' ) );
                                }
                            }

                            el.find( id + '-transparency' ).removeAttr( 'checked' );
                        }
                    }
                );

                // Store the old valid color on keydown
                el.find( '.redux-color' ).on(
                    'keydown', function() {
                        $( this ).data( 'oldkeypress', $( this ).val() );
                    }
                );

                // When transparency checkbox is clicked
                el.find( '.color-transparency' ).on(
                    'click', function() {
                        if ( $( this ).is( ":checked" ) ) {
                            el.find( '.redux-saved-color' ).val( $( '#' + $( this ).data( 'id' ) ).val() );
                            el.find( '#' + $( this ).data( 'id' ) ).val( 'transparent' );
                            el.find( '#' + $( this ).data( 'id' ) ).parent().parent().find( '.wp-color-result' ).css(
                                'background-color', 'transparent'
                            );
                        } else {
                            if ( el.find( '#' + $( this ).data( 'id' ) ).val() === 'transparent' ) {
                                var prevColor = $( '.redux-saved-color' ).val();

                                if ( prevColor === '' ) {
                                    prevColor = $( '#' + $( this ).data( 'id' ) ).data( 'default-color' );
                                }

                                el.find( '#' + $( this ).data( 'id' ) ).parent().parent().find( '.wp-color-result' ).css(
                                    'background-color', prevColor
                                );

                                el.find( '#' + $( this ).data( 'id' ) ).val( prevColor );
                            }
                        }
                        
                        redux.field_objects.color_gradient.changeValue($( this ), true);
                    }
                );
            }
        );
    };
    
    redux.field_objects.color_gradient.changeValue = function(el, update){
        var parent      = el.parents('.redux-container-color_gradient');
        var mainID      = parent.data('id');
        var preview     = parent.find('.redux-gradient-preview');
        
        var colorFrom   = parent.find('#' + mainID + '-from').val();
        var colorTo     = parent.find('#' + mainID + '-to').val();
        
        var angle = 0;
        var fromReach = 0;
        var toReach = 100;
        
        var w3c_deg     = Math.abs(angle - 450) % 360;
        var colors      = colorFrom + ' ' + fromReach + '%, ' + colorTo + ' ' + toReach + '%)';
        var result_w3c  = '';
        var result      = '';
        
        result_w3c  = 'linear-gradient(' + w3c_deg + 'deg,' + colors;
            result      = 'linear-gradient(' + angle + 'deg,' + colors;
        var hide = preview.css('display');

        if (hide === 'none') {
            preview.fadeIn();
        }
        
        preview.css('background', result_w3c);
        preview.css('background', '-moz-' + result);
        preview.css('background', '-webkit-' + result);
        preview.css('background', '-o-' + result);
        preview.css('background', '-ms-' + result);
        
        if (update) {
            redux_change( el );
        }
    };
})( jQuery );