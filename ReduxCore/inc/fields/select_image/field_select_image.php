<?php

/**
 * Field Select Image
 *
 * @package     Wordpress
 * @subpackage  ReduxFramework
 * @since       3.1.2
 * @author      Kevin Provance <kprovance>
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_select_image' ) ) {
    
    class ReduxFramework_select_image extends Redux_Field {

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function render() {

            // If options is NOT empty, the process
            if ( ! empty( $this->field['options'] ) ) {

                // beancounter
                $x = 1;

                // Process width
                if ( ! empty( $this->field['width'] ) ) {
                    $width = ' style="width:' . esc_attr($this->field['width']) . ';"';
                } else {
                    $width = ' style="width: 40%;"';
                }

                // Process placeholder
                $placeholder = ( isset( $this->field['placeholder'] ) ) ? esc_attr( $this->field['placeholder'] ) : esc_html__( 'Select an item', 'redux-framework' );

                $this->select2_config['allowClear'] = true;

                if ( isset( $this->field['select2'] ) ) {
                    $this->field['select2'] = wp_parse_args($this->field['select2'], $this->select2_config);
                } else {
                    $this->field['select2'] = $this->select2_config;
                }

                $this->field['select2'] = Redux_Functions::sanitize_camel_case_array_keys($this->field['select2']);

                $select2_data = Redux_Functions::create_data_string($this->field['select2']);
            
                // Begin the <select> tag
                echo '<select data-id="' . esc_attr($this->field['id']) . '" data-placeholder="' . esc_attr($placeholder) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '" class="redux-select-item redux-select-images ' . esc_attr($this->field['class']) . '"' . $width . ' rows="6"' . esc_attr($select2_data) . '>';
                echo '<option></option>';

                // Enum through the options array
                foreach ( $this->field['options'] as $k => $v ) {

                    // No array?  No problem!
                    if ( ! is_array( $v ) ) {
                        $v = array( 'img' => $v );
                    }

                    // No title set?  Make it blank.
                    if ( ! isset( $v['title'] ) ) {
                        $v['title'] = '';
                    }

                    // No alt?  Set it to title.  We do this so the alt tag shows
                    // something.  It also makes HTML/SEO purists happy.
                    if ( ! isset( $v['alt'] ) ) {
                        $v['alt'] = $v['title'];
                    }

                    // Set the selected entry
                    $selected = selected( $this->value, $v['img'], false );

                    // If selected returns something other than a blank space, we
                    // found our default/saved name.  Save the array number in a
                    // variable to use later on when we want to extract its associted
                    // url.
                    if ( '' != $selected ) {
                        $arrNum = $x;
                    }

                    // Add the option tag, with values.
                    echo '<option value="' . esc_url($v['img']) . '" ' . $selected . '>' . esc_attr($v['alt']) . '</option>';

                    // Add a bean
                    $x ++;
                }

                // Close the <select> tag
                echo '</select>';

                // Some space
                echo '<br /><br />';

                // Show the preview image.
                echo '<div>';

                // just in case.  You never know.
                if ( ! isset( $arrNum ) ) {
                    $this->value = '';
                }

                // Set the default image.  To get the url from the default name,
                // we save the array count from the for/each loop, when the default image
                // is mark as selected.  Since the for/each loop starts at one, we must
                // substract one from the saved array number.  We then pull the url
                // out of the options array, and there we go.
                if ( '' == $this->value ) {
                    echo '<img src="#" class="redux-preview-image" style="visibility:hidden;" id="image_' . esc_attr($this->field['id']) . '">';
                } else {
                    echo '<img src=' . esc_url($this->value) . ' class="redux-preview-image" id="image_' . esc_attr($this->field['id']) . '">';
                }

                // Close the <div> tag.
                echo '</div>';
            } else {

                // No options specified.  Really?
                echo '<strong>' . esc_html__( 'No items of this type were found.', 'redux-framework' ) . '</strong>';
            }
        } //function

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 1.0.0
         */
        function enqueue() {
            wp_enqueue_style( 'select2-css' );

            wp_enqueue_script(
                'field-select-image-js',
                ReduxCore::$_url . 'inc/fields/select_image/field_select_image' . Redux_Functions::isMin() . '.js',
                array('jquery', 'select2-js', 'redux-js'),
                ReduxCore::$_version,
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-select-image-css',
                    ReduxCore::$_url . 'inc/fields/select_image/field_select_image.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }
    }
}