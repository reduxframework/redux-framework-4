/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};

    $( document ).ready(
        function() {
            $.fn.isOnScreen = function() {
                if ( !window ) {
                    return;
                }

                var win = $( window );
                var viewport = {
                    top: win.scrollTop()
                };

                viewport.right = viewport.left + win.width();
                viewport.bottom = viewport.top + win.height();

                var bounds = this.offset();

                bounds.right = bounds.left + this.outerWidth();
                bounds.bottom = bounds.top + this.outerHeight();

                return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
            };

            // weed out multiple instances of duplicate Redux instance
            var tempArr = [];
            var opt_name;
            
            if ($('body').hasClass('wp-customizer')) {
                var li = $('.panel-meta.customize-info.redux-panel.accordion-section');
                
                opt_name = li.data('opt-name');

                redux.optName = window['redux_' + opt_name];
            }
            
            $( '.redux-container' ).each( function(idx, val){
                opt_name = $(this).data('opt-name');

                if ($.inArray(opt_name, tempArr) === -1) {
                    tempArr.push (opt_name);
                    
                    redux.optName = window['redux_' + opt_name];
                    
                    $.redux.checkRequired($(this));
                    $.redux.initEvents($(this));
                }
            });
            

            $( '.redux-container' ).on('click', function() {
                opt_name = $(this).data('opt-name');
                
                redux.optName = window['redux_' + opt_name];
            });

            $.redux.hideFields();    
            $.redux.initQtip();
            $.redux.tabCheck();
            $.redux.notices();
        }
    );
})(jQuery);