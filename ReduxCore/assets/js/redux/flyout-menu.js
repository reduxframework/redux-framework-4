/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};
    
    $.redux.flyoutSubmenus = function() {
	// Close flyouts when a new menu item is activated
	$( '.redux-group-tab-link-li a' ).click( function() {
            if (redux.optName.args.flyout_submenus === true) {
                $( '.redux-group-tab-link-li' ).removeClass( 'redux-section-hover' );
            }
	});

        if (redux.optName.args.flyout_submenus === true) {
            // Submenus flyout when a main menu item is hovered
            $( '.redux-group-tab-link-li.hasSubSections' ).each( function() {
                $( this ).mouseenter( function() {
                    if ( ! $( this ).hasClass( 'active' ) && ! $( this ).hasClass( 'activeChild' ) ) {
                        $( this ).addClass( 'redux-section-hover' );
                    }
                });

                $( this ).mouseleave( function() {
                    $( this ).removeClass( 'redux-section-hover' );
                });
            });
        }
    };
})(jQuery);
