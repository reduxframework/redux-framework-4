/* global jQuery, document, parent, redux_customizer_preview, wp */

(function ($) {

    function get_redux_field_id(fieldID, opt_name) {
        var matches = fieldID.match(/\[(.*?)\]/);
        if (matches && matches[1] in parent.window['redux_' + opt_name].options) {
            return matches[1];
        }
        return fieldID;
    }

    redux_customizer_preview.parse_css = function ($selector_array, $style) {
        console.log($selector_array, $style);

        // Something wrong happened.
        if (0 === $selector_array.length) {
            return '';
        }

        var $append_to_selector = false;

        Object.keys($style).forEach(function ($k) {
            if ($k.includes(':')) {
                $append_to_selector = true;
            }
        });

        var $style_string = '';

        Object.keys($style).forEach(function ($k) {
            $style_string += $k + ':' + $style[$k] + ';';
        });

        var $css = '';
        if (!$append_to_selector) {
            // Single Selectors that can be compressed
            if (0 === Object.keys($selector_array)[0]) {

                var $keys = $selector_array.join(',');
                $css = $keys + '{' + $style_string + '}';
                return $css;
            }
        }

        Object.keys($selector_array).forEach(function ($element) {
            var $selector = $selector_array[$element];
            var $style_string_loop = $style_string;
            if (isNaN($element)) {
                if (1 === $style.length) {
                    $style_string_loop = $element + ':' + $style[Object.keys($style)[0]] + ';';
                }
                $element = $selector;
            }
            if ($append_to_selector) {
                Object.keys($style).forEach(function ($k) {
                    if (':' === $k[0]) {
                        $css += $element + $k + '{' + $style[$k] + ';}';
                    } else {
                        $css += $element + '{' + $style_string_loop + '}';
                    }
                });

            } else {
                $css += $selector + '{' + $style_string_loop + '}';
            }
        });
        return $css;
    }


    function live_preview(fieldID, newVal, opt_name) {
        var selectors = redux_customizer_preview.fields[opt_name][fieldID];

        if (typeof selectors === 'string') {
            selectors = selectors.split(',');
        }

        if (!newVal) {
            return;
        }
        var field_controls = parent.wp.customize(fieldID).findControls();
        var field_type = false;
        if (field_controls.length > 0) {
            field_type = parent.wp.customize(fieldID).findControls()[0].params.type.replace('redux-', '');
        }

        var redux_field_id = get_redux_field_id(fieldID, opt_name);

        if (field_controls.length > 0 && typeof parent.redux.field_objects[field_type].customizer_preview_css_style !== 'undefined') {
            newVal = parent.redux.field_objects[field_type].customizer_preview_css_style(redux_field_id, newVal, opt_name);
        }

        var complete_styles = '';
        if (field_controls.length > 0 && typeof parent.redux.field_objects[field_type].customizer_preview_output !== 'undefined') {
            // Allow fields to override how the output is constructed.
            complete_styles = parent.redux.field_objects[field_type].customizer_preview_output(selectors, newVal);
        } else {

            $style_data = redux_customizer_preview.css_style( newVal );

            var complete_styles = redux_customizer_preview.parse_css(selectors, newVal);
            // var styles = '';
            // Object.keys(newVal).forEach(function (key) {
            //     if (key === 'units') {
            //         return;
            //     }
            //     styles += key + ':' + newVal[key] + ';';
            // });
            // for (var i = 0; i < selectors.length; i++) {
            //     complete_styles += selectors[i] + ' {' + styles + '}';
            // }
        }
        if (complete_styles.length > 0) {
            var fieldID_container = fieldID.replace('[', '-').replace(']', '')
            jQuery('#redux-preview-' + fieldID_container).text(complete_styles);
        }
    }

    $.each(redux_customizer_preview.fields, function (opt_name, the_fields) {
        var params = [opt_name]
        $.each(the_fields, function (fieldID) {
            opt_name = params[0]

            // Add <style>
            var fieldID_container = fieldID.replace('[', '-').replace(']', '')
            var preview_selector = 'redux-preview-' + fieldID_container
            if (null === document.getElementById(preview_selector) || 'undefined' === typeof document.getElementById(preview_selector)) {
                $('head').append('<style id="' + preview_selector + '"></style>');
            }

            var redux_field_id = get_redux_field_id(fieldID, opt_name);
            if (redux_field_id in parent.window['redux_' + opt_name].options) {
                var styles = live_preview(fieldID, parent.window['redux_' + opt_name].options[redux_field_id], opt_name)
                // Take care of styles on initial load and page-refreshes.
                $('#redux-preview-' + fieldID).text(styles);
            }

            // Add listener.
            wp.customize(fieldID, function (control) {
                control.bind(function (newVal) {
                    live_preview(fieldID, newVal, opt_name);
                });
            });

        }, params);
    });

})(jQuery);
