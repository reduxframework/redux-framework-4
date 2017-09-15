/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};

    $.redux.sanitize = function() {
        if ( redux.optName.sanitize && redux.optName.sanitize.sanitize ) {
            $.each(
                redux.optName.sanitize.sanitize, function( sectionID, sectionArray ) {
                    $.each(
                        sectionArray.sanitize, function( key, value ) {
                            $.redux.fixInput(value);
                        }
                    );
                }
            );
        }
    };

    $.redux.fixInput = function(value) {
        var input   = $('input#' + value.id);
        var mode    = 'input#' + value.id;

        if (input.length === 0) {
            input = $('textarea#' + value.id + '-textarea');
            mode = 'textarea#' + value.id + '-textarea';
        }

        if (input.length > 0) {
            var val = value.current === '' ? value.default : value.current;
            $(mode).val(val);
        }                            
    };

    $.redux.notices = function() {
        if ( redux.optName.errors && redux.optName.errors.errors ) {
            $.each(
                redux.optName.errors.errors, function( sectionID, sectionArray ) {
                    $.each(
                        sectionArray.errors, function( key, value ) {
                            $( "#" + redux.optName.args.opt_name + '-' + value.id ).addClass( "redux-field-error" );
                            if ( $( "#" + redux.optName.args.opt_name + '-' + value.id ).parent().find( '.redux-th-error' ).length === 0 ) {
                                $( "#" + redux.optName.args.opt_name + '-' + value.id ).append( '<div class="redux-th-error">' + value.msg + '</div>' );
                            } else {
                                $( "#" + redux.optName.args.opt_name + '-' + value.id ).parent().find( '.redux-th-error' ).html( value.msg ).css(
                                    'display', 'block'
                                );
                            }
                            
                            $.redux.fixInput(value);
                        }
                    );
                }
            );
    
            $( '.redux-container' ).each(
                function() {
                    var container = $( this );
                    
                    // Ajax cleanup
                    container.find( '.redux-menu-error' ).remove();
                    
                    var totalErrors = container.find( '.redux-field-error' ).length;
                    
                    if ( totalErrors > 0 ) {
                        container.find( ".redux-field-errors span" ).text( totalErrors );
                        container.find( ".redux-field-errors" ).slideDown();
                        container.find( '.redux-group-tab' ).each(
                            function() {
                                var total = $( this ).find( '.redux-field-error' ).length;
                                if ( total > 0 ) {
                                    var sectionID = $( this ).attr( 'id' ).split( '_' );
                                    
                                    sectionID = sectionID[0];
                                    container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="redux-menu-error">' + total + '</span>' );
                                    container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( "hasError" );
                                    
                                    var subParent = container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );
                                    
                                    if ( subParent ) {
                                        subParent.find( '.redux-group-tab-link-a:first' ).addClass( 'hasError' );
                                    }
                                }
                            }
                        );
                    }
                }
            );
        }
        
        if ( redux.optName.warnings && redux.optName.warnings.warnings ) {
            $.each(
                redux.optName.warnings.warnings, function( sectionID, sectionArray ) {
                    $.each(
                        sectionArray.warnings, function( key, value ) {
                            $( "#" + redux.optName.args.opt_name + '-' + value.id ).addClass( "redux-field-warning" );
                            if ( $( "#" + redux.optName.args.opt_name + '-' + value.id ).parent().find( '.redux-th-warning' ).length === 0 ) {
                                $( "#" + redux.optName.args.opt_name + '-' + value.id ).append( '<div class="redux-th-warning">' + value.msg + '</div>' );
                            } else {
                                $( "#" + redux.optName.args.opt_name + '-' + value.id ).parent().find( '.redux-th-warning' ).html( value.msg ).css(
                                    'display', 'block'
                                );
                            }
                            
                            $.redux.fixInput(value);
                        }
                    );
                }
            );
    
            $( '.redux-container' ).each(
                function() {
                    var container = $( this );
                    
                    // Ajax cleanup
                    container.find( '.redux-menu-warning' ).remove();
                    
                    var totalWarnings = container.find( '.redux-field-warning' ).length;
                    
                    if ( totalWarnings > 0 ) {
                        container.find( ".redux-field-warnings span" ).text( totalWarnings );
                        container.find( ".redux-field-warnings" ).slideDown();
                        container.find( '.redux-group-tab' ).each(
                            function() {
                                var total = $( this ).find( '.redux-field-warning' ).length;
                                
                                if ( total > 0 ) {
                                    var sectionID = $( this ).attr( 'id' ).split( '_' );
                                    
                                    sectionID = sectionID[0];
                                    container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="redux-menu-warning">' + total + '</span>' );
                                    container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( "hasWarning" );
                                    
                                    var subParent = container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );
                                    
                                    if ( subParent ) {
                                        subParent.find( '.redux-group-tab-link-a:first' ).addClass( 'hasWarning' );
                                    }
                                }
                            }
                        );
                    }
                }
            );
        }
    };
    
    
})(jQuery);