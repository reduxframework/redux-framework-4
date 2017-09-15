/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};

    $.redux.hideFields = function() {
        $( "label[for='redux_hide_field']" ).each(
            function( idx, val ) {
                var tr = $( this ).parent().parent();
                
                $( tr ).addClass( 'hidden' );
            }
        );
    };

    $.redux.checkRequired = function(el) {
        $.redux.required();

        $( "body" ).on(
            'change',
            '.redux-main select, .redux-main radio, .redux-main input[type=checkbox], .redux-main input[type=hidden]',
            function( e ) {
                $.redux.check_dependencies( this );
            }
        );

        $( "body" ).on(
            'check_dependencies', function( e, variable ) {
                $.redux.check_dependencies( variable );
            }
        );

        el.find( 'td > fieldset:empty,td > div:empty' ).parent().parent().hide();
    };

    $.redux.required = function() {
        // Hide the fold elements on load ,
        // It's better to do this by PHP but there is no filter in tr tag , so is not possible
        // we going to move each attributes we may need for folding to tr tag

        $.each(
            redux.optName.folds, function( i, v ) {
                var fieldset = $( '#' + redux.optName.args.opt_name + '-' + i );

                fieldset.parents( 'tr:first' ).addClass( 'fold' );

                if ( v === "hide" ) {
                    fieldset.parents( 'tr:first' ).addClass( 'hide' );

                    if ( fieldset.hasClass( 'redux-container-section' ) ) {
                        var div = $( '#section-' + i );

                        if ( div.hasClass( 'redux-section-indent-start' ) ) {
                            $( '#section-table-' + i ).hide().addClass( 'hide' );
                            div.hide().addClass( 'hide' );
                        }
                    }

                    if ( fieldset.hasClass( 'redux-container-info' ) ) {
                        $( '#info-' + i ).hide().addClass( 'hide' );
                    }

                    if ( fieldset.hasClass( 'redux-container-divide' ) ) {
                        $( '#divide-' + i ).hide().addClass( 'hide' );
                    }

                    if ( fieldset.hasClass( 'redux-container-raw' ) ) {
                        var rawTable = fieldset.parents().find( 'table#' + redux.optName.args.opt_name + '-' + i );
                        rawTable.hide().addClass( 'hide' );
                    }
                }
            }
        );
    };
    
    $.redux.getContainerValue = function( id ) {
        var value = $( '#' + redux.optName.args.opt_name + '-' + id ).serializeForm();

        if ( value !== null && typeof value === 'object' && value.hasOwnProperty( redux.optName.args.opt_name ) ) {
            value = value[redux.optName.args.opt_name][id];
        }
        
        if ( $( '#' + redux.optName.args.opt_name + '-' + id ).hasClass( 'redux-container-media' ) ) {
            value = value.url;
        }
        
        return value;
    };

    $.redux.check_dependencies = function( variable ) {
        //console.log ('check deps');
        if ( redux.optName.required === null ) {
            return;
        }

        var current = $( variable );
        var id = current.parents( '.redux-field:first' ).data( 'id' );

        if ( !redux.optName.required.hasOwnProperty( id ) ) {
            return;
        }

        var container = current.parents( '.redux-field-container:first' );
        var is_hidden = container.parents( 'tr:first' ).hasClass( '.hide' );

        if ( !container.parents( 'tr:first' ).length ) {
            is_hidden = container.parents( '.customize-control:first' ).hasClass( '.hide' );
        }

//console.log('check deps: ' + redux.optName.args.opt_name);
        $.each(
            redux.optName.required[id], function( child, dependents ) {
                var current         = $( this );
                var show            = false;
                var childFieldset   = $( '#' + redux.optName.args.opt_name + '-' + child );
                var tr              = childFieldset.parents( 'tr:first' );

                if ( !is_hidden ) {
                    show = $.redux.check_parents_dependencies( child );
                }

                if ( show === true ) {
                    // Shim for sections
                    if ( childFieldset.hasClass( 'redux-container-section' ) ) {
                        var div = $( '#section-' + child );

                        if ( div.hasClass( 'redux-section-indent-start' ) && div.hasClass( 'hide' ) ) {
                            $( '#section-table-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                            div.fadeIn( 300 ).removeClass( 'hide' );
                        }
                    }

                    if ( childFieldset.hasClass( 'redux-container-info' ) ) {
                        $( '#info-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                    }

                    if ( childFieldset.hasClass( 'redux-container-divide' ) ) {
                        $( '#divide-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                    }

                    if ( childFieldset.hasClass( 'redux-container-raw' ) ) {
                        var rawTable = childFieldset.parents().find( 'table#' + redux.optName.args.opt_name + '-' + child );
                        rawTable.fadeIn( 300 ).removeClass( 'hide' );
                    }

                    tr.fadeIn(
                        300, function() {
                            $( this ).removeClass( 'hide' );
                            if ( redux.optName.required.hasOwnProperty( child ) ) {
                                $.redux.check_dependencies( $( '#' + redux.optName.args.opt_name + '-' + child ).children().first() );
                            }
                            
                            $.redux.initFields();
                        }
                    );
            
                    if ( childFieldset.hasClass( 'redux-container-section' ) || childFieldset.hasClass( 'redux-container-info' ) ) {
                        tr.css( {display: 'none'} );
                    }
                } else if ( show === false ) {
                    tr.fadeOut(
                        100, function() {
                            $( this ).addClass( 'hide' );
                            if ( redux.optName.required.hasOwnProperty( child ) ) {
                                //console.log('Now check, reverse: '+child);
                                $.redux.required_recursive_hide( child );
                            }
                        }
                    );
                }

                current.find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
            }
        );
    };

    $.redux.required_recursive_hide = function( id ) {
        var toFade = $( '#' + redux.optName.args.opt_name + '-' + id ).parents( 'tr:first' );

        toFade.fadeOut(
            50, function() {
                $( this ).addClass( 'hide' );

                if ( $( '#' + redux.optName.args.opt_name + '-' + id ).hasClass( 'redux-container-section' ) ) {
                    var div = $( '#section-' + id );
                    if ( div.hasClass( 'redux-section-indent-start' ) ) {
                        $( '#section-table-' + id ).fadeOut( 50 ).addClass( 'hide' );
                        div.fadeOut( 50 ).addClass( 'hide' );
                    }
                }

                if ( $( '#' + redux.optName.args.opt_name + '-' + id ).hasClass( 'redux-container-info' ) ) {
                    $( '#info-' + id ).fadeOut( 50 ).addClass( 'hide' );
                }

                if ( $( '#' + redux.optName.args.opt_name + '-' + id ).hasClass( 'redux-container-divide' ) ) {
                    $( '#divide-' + id ).fadeOut( 50 ).addClass( 'hide' );
                }

                if ( $( '#' + redux.optName.args.opt_name + '-' + id ).hasClass( 'redux-container-raw' ) ) {
                    var rawTable = $( '#' + redux.optName.args.opt_name + '-' + id ).parents().find( 'table#' + redux.optName.args.opt_name + '-' + id );
                    rawTable.fadeOut( 50 ).addClass( 'hide' );
                }

                if ( redux.optName.required.hasOwnProperty( id ) ) {
                    $.each(
                        redux.optName.required[id], function( child ) {
                            $.redux.required_recursive_hide( child );
                        }
                    );
                }
            }
        );
    };

    $.redux.check_parents_dependencies = function( id ) {
        var show = "";

        if ( redux.optName.required_child.hasOwnProperty( id ) ) {
            $.each(
                redux.optName.required_child[id], function( i, parentData ) {
                    if ( $( '#' + redux.optName.args.opt_name + '-' + parentData.parent ).parents( 'tr:first' ).hasClass( '.hide' ) ) {
                        show = false;
                    } else {
                        if ( show !== false ) {
                            var parentValue = $.redux.getContainerValue( parentData.parent );
                            
                            show = $.redux.checkDependenciesVisibility( parentValue, parentData );
                        }
                    }
                }
            );
        } else {
            show = true;
        }
        
        return show;
    };

    $.redux.checkDependenciesVisibility = function( parentValue, data ) {
        var show        = false;
        var checkValue  = data.checkValue;
        var operation   = data.operation;
        var arr;

        if ($.isPlainObject( parentValue )) {
            parentValue = Object.keys( parentValue ).map(
                function( key ) {
                    return [key, parentValue[key]];
                }
            );            
        }

        switch ( operation ) {
            case '=':
            case 'equals':
                if ( $.isArray( parentValue ) ) {
                    $( parentValue[0] ).each(
                        function( idx, val ) {
                            if ( $.isArray( checkValue ) ) {
                                $( checkValue ).each(
                                    function( i, v ) {
                                        if ( val == v ) {
                                            show = true;
                                            
                                            return true;
                                        }
                                    }
                                );
                            } else {
                                if ( val == checkValue ) {
                                    show = true;
                                    
                                    return true;
                                }
                            }
                        }
                    );
                } else {
                    if ( $.isArray( checkValue ) ) {
                        $( checkValue ).each(
                            function( i, v ) {
                                if ( parentValue == v ) {
                                    show = true;
                                }
                            }
                        );
                    } else {
                        if ( parentValue == checkValue ) {
                            show = true;
                        }
                    }
                }
            break;

            case '!=':
            case 'not':
                if ( $.isArray( parentValue ) ) {
                    $( parentValue[0] ).each(
                        function( idx, val ) {
                            if ( $.isArray( checkValue ) ) {
                                $( checkValue ).each(
                                    function( i, v ) {
                                        if ( val != v ) {
                                            show = true;
                                            
                                            return true;
                                        }
                                    }
                                );
                            } else {
                                if ( val != checkValue ) {
                                    show = true;
                                    
                                    return true;
                                }
                            }
                        }
                    );
                } else {
                    if ( $.isArray( checkValue ) ) {
                        $( checkValue ).each(
                            function( i, v ) {
                                if ( parentValue != v ) {
                                    show = true;
                                }
                            }
                        );
                    } else {
                        if ( parentValue != checkValue ) {
                            show = true;
                        }
                    }
                }
            break;

            case '>':
            case 'greater':
            case 'is_larger':
                if ( parseFloat( parentValue ) > parseFloat( checkValue ) ) {
                    show = true;
                }
            break;

            case '>=':
            case 'greater_equal':
            case 'is_larger_equal':
                if ( parseFloat( parentValue ) >= parseFloat( checkValue ) ) {
                    show = true;
                }
            break;

            case '<':
            case 'less':
            case 'is_smaller':
                if ( parseFloat( parentValue ) < parseFloat( checkValue ) ) {
                    show = true;
                }
            break;

            case '<=':
            case 'less_equal':
            case 'is_smaller_equal':
                if ( parseFloat( parentValue ) <= parseFloat( checkValue ) ) {
                    show = true;
                }
            break;

            case 'contains':
                if ( $.isPlainObject( parentValue ) ) {
                    parentValue = Object.keys( parentValue ).map(
                        function( key ) {
                            return [key, parentValue[key]];
                        }
                    );
                }

                if ( $.isPlainObject( checkValue ) ) {
                    checkValue = Object.keys( checkValue ).map(
                        function( key ) {
                            return [key, checkValue[key]];
                        }
                    );
                }

                if ( $.isArray( checkValue ) ) {
                    $( checkValue ).each(
                        function( idx, val ) {
                            var breakMe = false;
                            var toFind = val[0];
                            var findVal = val[1];

                            $( parentValue ).each(
                                function( i, v ) {
                                    var toMatch = v[0];
                                    var matchVal = v[1];

                                    if ( toFind === toMatch ) {
                                        if ( findVal == matchVal ) {
                                            show = true;
                                            breakMe = true;

                                            return false;
                                        }
                                    }
                                }
                            );

                            if ( breakMe === true ) {
                                return false;
                            }
                        }
                    );
                } else {
                    if ( parentValue.toString().indexOf( checkValue ) !== -1 ) {
                        show = true;
                    }
                }
            break;

            case 'doesnt_contain':
            case 'not_contain':
                if ( $.isPlainObject( parentValue ) ) {
                    arr = Object.keys( parentValue ).map(
                        function( key ) {
                            return parentValue[key];
                        }
                    );
                    parentValue = arr;
                }

                if ( $.isPlainObject( checkValue ) ) {
                    arr = Object.keys( checkValue ).map(
                        function( key ) {
                            return checkValue[key];
                        }
                    );
                    checkValue = arr;
                }

                if ( $.isArray( checkValue ) ) {
                    $( checkValue ).each(
                        function( idx, val ) {
                            if ( parentValue.toString().indexOf( val ) === -1 ) {
                                show = true;
                            }
                        }
                    );
                } else {
                    if ( parentValue.toString().indexOf( checkValue ) === -1 ) {
                        show = true;
                    }
                }
            break;

            case 'is_empty_or':
                if ( parentValue === "" || parentValue == checkValue ) {
                    show = true;
                }
            break;

            case 'not_empty_and':
                if ( parentValue !== "" && parentValue != checkValue ) {
                    show = true;
                }
            break;

            case 'is_empty':
            case 'empty':
            case '!isset':
                if ( !parentValue || parentValue === "" || parentValue === null ) {
                    show = true;
                }
            break;

            case 'not_empty':
            case '!empty':
            case 'isset':
                if ( parentValue && parentValue !== "" && parentValue !== null ) {
                    show = true;
                }
            break;
        }
        
        return show;
    };
})(jQuery);