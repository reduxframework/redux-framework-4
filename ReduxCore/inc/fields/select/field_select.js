/*global redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.select = redux.field_objects.select || {};

    redux.field_objects.select.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( '.redux-container-select:visible' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;

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

                var default_params = {};

                el.find( 'select.redux-select-item' ).each(
                    function() {
                        if ( $( this ).hasClass( 'font-icons' ) ) {
                            default_params = $.extend(
                                {}, {
                                    templateResult: redux.field_objects.select.addIcon,
                                    templateSelection: redux.field_objects.select.addIcon,
                                    escapeMarkup: function( m ) {
                                        return m;
                                    }
                                }, default_params
                            );
                        }
                        if ( $( this ).data( 'ajax' ) ) {
                            console.log( $( this ).data() );
                            console.log( 'here' );
                            var ajax_url = $( this ).data( 'ajax_url' );
                            var nonce = $( this ).data( 'nonce' );
                            var data = $( this ).data( 'data' );

                            default_params = {
                                ajax: {
                                    // url: "https://api.github.com/search/repositories",
                                    url: ajaxurl + ajax_url,
                                    dataType: 'json',
                                    delay: 250,
                                    data: function( params ) {
                                        return {
                                            nonce: nonce,
                                            data: data,
                                            q: params.term, // search term
                                            page: params.page
                                        };
                                    },
                                    processResults: function( data, params ) {
                                        // parse the results into the format expected by Select2
                                        // since we are using custom formatting functions we do not need to
                                        // alter the remote JSON data, except to indicate that infinite
                                        // scrolling can be used
                                        params.page = params.page || 1;

                                        // !IMPORTANT! your every item in data.items has to have an .id property - this is the actual value that Select2 uses
                                        // Luckily the source data.items already have one
                                        return {
                                            results: data.items,
                                            pagination: {
                                                more: (params.page * 30) < data.total_count
                                            }
                                        };
                                    },
                                    cache: true
                                },
                                escapeMarkup: function( markup ) {
                                    return markup; // let our custom formatter work
                                },
                                minimumInputLength: 1,
                                templateResult: function( repo ) {
                                    if ( repo.loading ) return repo.text;
                                    return repo.full_name;
                                },
                                templateSelection: function( repo ) {
                                    return repo.full_name || repo.text;
                                }
                            };
                        }
                        
                        if ( $( this ).attr( 'data-ajax' ) ) {
                            console.log( default_params );
                        }

                        $( this ).select2( default_params );

                        el.find( '.select2-search__field' ).width( 'auto' );

                        if ( $( this ).hasClass( 'select2-sortable' ) ) {
                            default_params = {};
                            default_params.bindOrder = 'sortableStop';
                            default_params.sortableOptions = {placeholder: 'ui-state-highlight'};
                            $( this ).select2Sortable( default_params );
                        }

                        $( this ).on(
                            "change", function() {
                                redux_change( $( $( this ) ) );
                                $( this ).select2SortableOrder();
                            }
                        );
                    }
                );
            }
        );
    };

    redux.field_objects.select.addIcon = function( icon ) {
        if ( icon.hasOwnProperty( 'id' ) ) {
            return "<span class='elusive'><i class='" + icon.id + "'></i>" + "&nbsp;&nbsp;" + icon.text + "</span>";
        }
    };
})( jQuery );