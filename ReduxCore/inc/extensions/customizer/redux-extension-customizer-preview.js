/* global jQuery, document, parent, redux_customizer_preview, wp */

(function ($) {

    function get_redux_field_id(fieldID, opt_name) {
        var matches = fieldID.match(/\[(.*?)\]/);
        if (matches && matches[1] in parent.window['redux_' + opt_name].options) {
            return matches[1];
        }
        return fieldID;
    }

    var redux_output = {};

    redux_output.parse_css = function ($selector_array, $style) {
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


    redux_output.css_style = function ($type, $selector_array, $style) {
        var $output = [];
        if ($type === 'color') { // For `color` type, we need special handling.
            // Expected Input
            // - type=>color, selector_array => {background_color: ".site-background", color: ".site-title"}, $style => #000000
            // Desired Output
            // - [{[".site-background"], background_color: #000000}, {[".site-title"], color: #000000}]
            if (typeof $selector_array === 'object') {
                Object.keys($selector_array).forEach(function($key) {
                    var $atom = {};
                    $atom[$key] = $style;

                    $output.push({selector: [$selector_array[$key]], style: $atom});
                });
            }
        }

        if ($type === 'color_gradient') { // For `color` type, we need special handling.
            // Expected Input
            // - type => color_gradient, selector_array => [".site-header"], $style => {from: "#1e73be", to: "#00897e"}
            // Desired Output
            // - [{[".site-background"], background_color: #000000}, {[".site-title"], color: #000000}]
            var $atom = {"background-image": `linear-gradient (${$style.from}, ${$style.to})`};
            $selector_array.forEach(function($key) {
                $output.push({selector: $key, style: $atom});
            });
        }

        if ($type === 'link_color') { // For `color` type, we need special handling.
            // Expected Input
            // - type => color_gradient, selector_array => [".site-header"], $style => {from: "#1e73be", to: "#00897e"}
            // Desired Output
            // - [{[".site-background"], background_color: #000000}, {[".site-title"], color: #000000}]
            console.log("For Link Color", $selector_array, $style);
            var $atom = {"background-image": `linear-gradient (${$style.from}, ${$style.to})`};
            $selector_array.forEach(function($key) {
                Object.keys($style).forEach(function(psuedo) {
                    $output.push({selector: `${$key}:${psuedo}`, style: $style[psuedo]});
                });
            });
            console.log("After PROCESSING", $output);
        }

        return $output;
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
        var draft_styles = [];
        if (field_controls.length > 0 && typeof parent.redux.field_objects[field_type].customizer_preview_output !== 'undefined') {
            // Allow fields to override how the output is constructed.
            complete_styles = parent.redux.field_objects[field_type].customizer_preview_output(selectors, newVal);
        } else {

            console.log("INNOVATION expected", redux_customizer_preview);
            console.log("Actual INPUT", field_type, selectors, newVal);
            // var $style_data = redux_customizer_preview.css_style( newVal );

            draft_styles = redux_output.css_style(field_type, selectors, newVal);
            console.log("Draft Styles", draft_styles);
            complete_styles = redux_output.parse_css(selectors, newVal);
            var styles = '';
            Object.keys(newVal).forEach(function (key) {
                if (key === 'units') {
                    return;
                }
                styles += key + ':' + newVal[key] + ';';
            });
            for (var i = 0; i < selectors.length; i++) {
                complete_styles += selectors[i] + ' {' + styles + '}';
            }
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
