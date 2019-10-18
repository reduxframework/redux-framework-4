/*
 * Field Link Color
 */

/*global jQuery, document, redux_change, redux, colorValidate, window */

(function ($) {
    'use strict';

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.link_color = redux.field_objects.link_color || {};

    redux.field_objects.link_color.customizer_preview_css_style = function (fieldID, newVal, opt_name) {
        var $style = {};
        var defaults = window['redux_' + opt_name].defaults[fieldID];
        Object.keys(newVal).forEach(function (key) {
            if (newVal[key] && defaults[key] && defaults[key] !== newVal[key]) {
                $style[key] = ['color:' + newVal[key] + ';'];
            }
        });

        return $style;
    }
    redux.field_objects.link_color.customizer_preview_output = function (selectors, newVal) {
        if (!Array.isArray(selectors)) {
            selectors = [selectors];
        }

        var $output_style = '';
        for (var i = 0; i < selectors.length; i++) {
            Object.keys(newVal).forEach(function (val) {
                if (val === 'regular') {
                    $output_style += selectors[i] + '{' + newVal[val].join() + '}';
                } else {
                    $output_style += selectors[i] + ':' + val + '{' + newVal[val].join() + '}';
                }
            });
        }

        return $output_style;
    }

    redux.field_objects.link_color.init = function (selector) {
        if (!selector) {
            selector = $(document).find('.redux-container-link_color:visible');
        }

        $(selector).each(
            function () {
                var el = $(this);
                var parent = el;

                if (!el.hasClass('redux-field-container')) {
                    parent = el.parents('.redux-field-container:first');
                }

                if (parent.is(':hidden')) {
                    return;
                }

                if (parent.hasClass('redux-field-init')) {
                    parent.removeClass('redux-field-init');
                } else {
                    return;
                }

                el.find('.redux-color-init').wpColorPicker(
                    {
                        change: function (e, ui) {
                            $(this).val(ui.color.toString());

                            redux_change($(this));

                            el.find('#' + e.target.getAttribute('data-id') + '-transparency').removeAttr('checked');
                        }, clear: function (e, ui) {
                            e = null;
                            $(this).val(ui.color.toString());

                            redux_change($(this).parent().find('.redux-color-init'));
                        }
                    }
                );

                el.find('.redux-color').on(
                    'keyup',
                    function () {
                        var value = $(this).val();
                        var color = colorValidate(this);
                        var id = '#' + $(this).attr('id');

                        if ('transparent' === value) {
                            $(this).parent().parent().find('.wp-color-result').css('background-color', 'transparent');

                            el.find(id + '-transparency').attr('checked', 'checked');
                        } else {
                            el.find(id + '-transparency').removeAttr('checked');

                            if (color && color !== $(this).val()) {
                                $(this).val(color);
                            }
                        }
                    }
                );

                // Replace and validate field on blur.
                el.find('.redux-color').on(
                    'blur',
                    function () {
                        var value = $(this).val();
                        var id = '#' + $(this).attr('id');

                        if ('transparent' === value) {
                            $(this).parent().parent().find('.wp-color-result').css('background-color', 'transparent');

                            el.find(id + '-transparency').attr('checked', 'checked');
                        } else {
                            if (colorValidate(this) === value) {
                                if (0 !== value.indexOf('#')) {
                                    $(this).val($(this).data('oldcolor'));
                                }
                            }

                            el.find(id + '-transparency').removeAttr('checked');
                        }
                    }
                );

                // Store the old valid color on keydown.
                el.find('.redux-color').on(
                    'keydown',
                    function () {
                        $(this).data('oldkeypress', $(this).val());
                    }
                );
            }
        );
    };
})(jQuery);
