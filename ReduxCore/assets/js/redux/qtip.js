/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};

    $.redux.initQtip = function() {
        if ( $().qtip ) {
            // Shadow
            var shadow = '';
            var tip_shadow = redux.optName.args.hints.tip_style.shadow;

            if ( tip_shadow === true ) {
                shadow = 'qtip-shadow';
            }

            // Color
            var color = '';
            var tip_color = redux.optName.args.hints.tip_style.color;

            if ( tip_color !== '' ) {
                color = 'qtip-' + tip_color;
            }

            // Rounded
            var rounded = '';
            var tip_rounded = redux.optName.args.hints.tip_style.rounded;

            if ( tip_rounded === true ) {
                rounded = 'qtip-rounded';
            }

            // Tip style
            var style = '';
            var tip_style = redux.optName.args.hints.tip_style.style;

            if ( tip_style !== '' ) {
                style = 'qtip-' + tip_style;
            }

            var classes = shadow + ',' + color + ',' + rounded + ',' + style + ',redux-qtip';
            classes = classes.replace( /,/g, ' ' );

            // Get position data
            var myPos = redux.optName.args.hints.tip_position.my;
            var atPos = redux.optName.args.hints.tip_position.at;

            // Gotta be lowercase, and in proper format
            myPos = $.redux.verifyPos( myPos.toLowerCase(), true );
            atPos = $.redux.verifyPos( atPos.toLowerCase(), false );

            // Tooltip trigger action
            var showEvent = redux.optName.args.hints.tip_effect.show.event;
            var hideEvent = redux.optName.args.hints.tip_effect.hide.event;

            // Tip show effect
            var tipShowEffect = redux.optName.args.hints.tip_effect.show.effect;
            var tipShowDuration = redux.optName.args.hints.tip_effect.show.duration;

            // Tip hide effect
            var tipHideEffect = redux.optName.args.hints.tip_effect.hide.effect;
            var tipHideDuration = redux.optName.args.hints.tip_effect.hide.duration;

            $( 'div.redux-dev-qtip' ).each(
                function() {
                    $( this ).qtip(
                        {
                            content: {
                                text: $( this ).attr( 'qtip-content' ),
                                title: $( this ).attr( 'qtip-title' )
                            },
                            show: {
                                effect: function() {
                                    $( this ).slideDown( 500 );
                                },
                                event: 'mouseover'
                            },
                            hide: {
                                effect: function() {
                                    $( this ).slideUp( 500 );
                                },
                                event: 'mouseleave'
                            },
                            style: {
                                classes: 'qtip-shadow qtip-light'
                            },
                            position: {
                                my: 'top center',
                                at: 'bottom center'
                            }
                        }
                    );
                }
            );

            $( 'div.redux-hint-qtip' ).each(
                function() {
                    $( this ).qtip(
                        {
                            content: {
                                text: $( this ).attr( 'qtip-content' ),
                                title: $( this ).attr( 'qtip-title' )
                            },
                            show: {
                                effect: function() {
                                    switch ( tipShowEffect ) {
                                        case 'slide':
                                            $( this ).slideDown( tipShowDuration );
                                            break;
                                        case 'fade':
                                            $( this ).fadeIn( tipShowDuration );
                                            break;
                                        default:
                                            $( this ).show();
                                            break;
                                    }
                                },
                                event: showEvent
                            },
                            hide: {
                                effect: function() {
                                    switch ( tipHideEffect ) {
                                        case 'slide':
                                            $( this ).slideUp( tipHideDuration );
                                            break;
                                        case 'fade':
                                            $( this ).fadeOut( tipHideDuration );
                                            break;
                                        default:
                                            $( this ).hide( tipHideDuration );
                                            break;
                                    }
                                },
                                event: hideEvent
                            },
                            style: {
                                classes: classes
                            },
                            position: {
                                my: myPos,
                                at: atPos
                            }
                        }
                    );
                }
            );

            $( 'input[qtip-content]' ).each(
                function() {
                    $( this ).qtip(
                        {
                            content: {
                                text: $( this ).attr( 'qtip-content' ),
                                title: $( this ).attr( 'qtip-title' )
                            },
                            show: 'focus',
                            hide: 'blur',
                            style: classes,
                            position: {
                                my: myPos,
                                at: atPos
                            }
                        }
                    );
                }
            );
        }
    };

    $.redux.verifyPos = function( s, b ) {

        // trim off spaces
        s = s.replace( /^\s+|\s+$/gm, '' );

        // position value is blank, set the default
        if ( s === '' || s.search( ' ' ) === -1 ) {
            if ( b === true ) {
                return 'top left';
            } else {
                return 'bottom right';
            }
        }

        // split string into array
        var split = s.split( ' ' );

        // Evaluate first string.  Must be top, center, or bottom
        var paramOne = b ? 'top' : 'bottom';
        
        if ( split[0] === 'top' || split[0] === 'center' || split[0] === 'bottom' ) {
            paramOne = split[0];
        }

        // Evaluate second string.  Must be left, center, or right.
        var paramTwo = b ? 'left' : 'right';
        
        if ( split[1] === 'left' || split[1] === 'center' || split[1] === 'right' ) {
            paramTwo = split[1];
        }

        return paramOne + ' ' + paramTwo;
    };    
})(jQuery);