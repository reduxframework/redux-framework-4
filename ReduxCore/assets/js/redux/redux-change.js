/* global redux */

function redux_change( variable ) {
    (function( $ ) {
        'use strict';
        
        variable = $(variable);

        var rContainer  = $( variable ).parents( '.redux-container:first' );
        var opt_name    = rContainer.data('opt-name');

        redux.optName   = window['redux_' + opt_name];

        $( 'body' ).trigger( 'check_dependencies', variable );

        if ( variable.hasClass( 'compiler' ) ) {
            $( '#redux-compiler-hook' ).val( 1 );
        }

        var parentID = $( variable ).closest( '.redux-group-tab' ).attr( 'id' );

        // Let's count down the errors now. Fancy.  ;)
        var id = parentID.split( '_' );

        id = id[0];

        var th = rContainer.find( '.redux-group-tab-link-a[data-key="' + id + '"]' ).parents( '.redux-group-tab-link-li:first' );
        var subParent = $( '#' + parentID + '_li' ).parents( '.hasSubSections:first' );

        if ( $( variable ).parents( 'fieldset.redux-field:first' ).hasClass( 'redux-field-error' ) ) {
            $( variable ).parents( 'fieldset.redux-field:first' ).removeClass( 'redux-field-error' );
            $( variable ).parent().find( '.redux-th-error' ).slideUp();

            var errorCount = (parseInt( rContainer.find( '.redux-field-errors span' ).text() ) - 1);

            if ( errorCount <= 0 ) {
                $( '#' + parentID + '_li .redux-menu-error' ).fadeOut( 'fast' ).remove();
                $( '#' + parentID + '_li .redux-group-tab-link-a' ).removeClass( 'hasError' );
                $( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.redux-field-errors' ).slideUp();
                $( variable ).parents( '.redux-container:first' ).find( '.redux-field-errors' ).slideUp();
                $( '#redux_metaboxes_errors' ).slideUp();
            } else {
                var errorsLeft = (parseInt( th.find( '.redux-menu-error:first' ).text() ) - 1);

                if ( errorsLeft <= 0 ) {
                    th.find( '.redux-menu-error:first' ).fadeOut().remove();
                } else {
                    th.find( '.redux-menu-error:first' ).text( errorsLeft );
                }

                rContainer.find( '.redux-field-errors span' ).text( errorCount );
            }

            if ( subParent.length !== 0 ) {
                if ( subParent.find( '.redux-menu-error' ).length === 0 ) {
                    subParent.find( '.hasError' ).removeClass( 'hasError' );
                }
            }
        }

        if ( $( variable ).parents( 'fieldset.redux-field:first' ).hasClass( 'redux-field-warning' ) ) {
            $( variable ).parents( 'fieldset.redux-field:first' ).removeClass( 'redux-field-warning' );
            $( variable ).parent().find( '.redux-th-warning' ).slideUp();

            var warningCount = (parseInt( rContainer.find( '.redux-field-warnings span' ).text() ) - 1);

            if ( warningCount <= 0 ) {
                $( '#' + parentID + '_li .redux-menu-warning' ).fadeOut( 'fast' ).remove();
                $( '#' + parentID + '_li .redux-group-tab-link-a' ).removeClass( 'hasWarning' );
                $( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.redux-field-warnings' ).slideUp();
                $( variable ).parents( '.redux-container:first' ).find( '.redux-field-warnings' ).slideUp();
                $( '#redux_metaboxes_warnings' ).slideUp();
            } else {
                // Let's count down the warnings now. Fancy.  ;)
                var warningsLeft = (parseInt( th.find( '.redux-menu-warning:first' ).text() ) - 1);

                if ( warningsLeft <= 0 ) {
                    th.find( '.redux-menu-warning:first' ).fadeOut().remove();
                } else {
                    th.find( '.redux-menu-warning:first' ).text( warningsLeft );
                }

                rContainer.find( '.redux-field-warning span' ).text( warningCount );
            }

            if ( subParent.length !== 0 ) {
                if ( subParent.find( '.redux-menu-warning' ).length === 0 ) {
                    subParent.find( '.hasWarning' ).removeClass( 'hasWarning' );
                }
            }
        }

        // Don't show the changed value notice while save_notice is visible.
        if ( rContainer.find( '.saved_notice:visible' ).length > 0 ) {
            return;
        }

        if ( !redux.optName.args.disable_save_warn ) {
            rContainer.find( '.redux-save-warn' ).slideDown();
            window.onbeforeunload = confirmOnPageExit;
        }
    })(jQuery);
}

var confirmOnPageExit = function( e ) {
    //return; // ONLY FOR DEBUGGING
    // If we haven't been passed the event get the window.event
    e = e || window.event;

    var message = redux.optName.args.save_pending;

    // For IE6-8 and Firefox prior to version 4
    if ( e ) {
        e.returnValue = message;
    }

    window.onbeforeunload = null;

    // For Chrome, Safari, IE8+ and Opera 12+
    return message;
};