/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};

    $.redux.initFields = function() {
        $( ".redux-group-tab:visible" ).find( ".redux-field-init:visible" ).each(
            function() {
                var type = $( this ).attr( 'data-type' );

                if ( typeof redux.field_objects !== 'undefined' && redux.field_objects[type] && redux.field_objects[type] ) {
                    redux.field_objects[type].init();
                }

                if ( typeof redux.field_objects.pro !== 'undefined' && redux.field_objects.pro[type] && redux.field_objects.pro[type] ) {
                    redux.field_objects.pro[type].init();
                }
                
                if ( !redux.customizer && $( this ).hasClass( 'redux_remove_th' ) ) {
                    var tr = $( this ).parents( 'tr:first' );
                    var th = tr.find( 'th:first' );
                    
                    if ( th.html() && th.html().length > 0 ) {
                        $( this ).prepend( th.html() );
                        $( this ).find( '.redux_field_th' ).css( 'padding', '0 0 10px 0' );
                    }
                    
                    $( this ).parent().attr( 'colspan', '2' );
                    
                    th.remove();
                }
            }
        );
    };
})(jQuery);