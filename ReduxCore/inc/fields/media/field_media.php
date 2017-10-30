<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @subpackage  Field_Media
 * @author      Kevin Provance (kprovance)
 * @author      Dovy Paukstys
 * @version     4.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_media' ) ) {

    /**
     * Main ReduxFramework_media class
     *
     * @since       1.0.0
     */
    class ReduxFramework_media extends Redux_Field {
        private $filters_enabled = false;
        
        public function set_defaults() {
            // No errors please
            $defaults = array(
                'id'        => '',
                'url'       => '',
                'width'     => '',
                'height'    => '',
                'thumbnail' => '',
            );

            // Since value subarrays do not get parsed in wp_parse_args
            // Fixed that motherfracker, didn't we?  Bitches! - kp
            $this->value = Redux_Functions::parse_args( $this->value, $defaults );

            $defaults = array(
                'mode'          => 'image',
                'preview'       => true,
                'preview_size'  => 'thumbnail',
                'url'           => true,
                'placeholder'   => esc_html__( 'No media selected', 'redux-framework' ),
                'readonly'      => true,
                'class'         => '',
                );


            $this->field = Redux_Functions::parse_args( $this->field, $defaults );

            if ($this->field['mode'] == false) {
                $this->field['mode'] = 0;
            }
        
            if (ReduxCore::$_pro_loaded) {
                $this->field = apply_filters('redux/pro/media/field/set_defaults', $this->field);
                $this->value = apply_filters('redux/pro/media/value/set_defaults', $this->value);
            }
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {
            if (!isset($this->field['library_filter'])) {
                $libFilter = '';
            } else {
                if (!is_array($this->field['library_filter'])) {
                    $this->field['library_filter'] = array($this->field['library_filter']);
                }

                $mimeTypes = get_allowed_mime_types();

                $libArray = $this->field['library_filter'];

                $jsonArr = array();

                // Enum mime types
                foreach ($mimeTypes as $ext => $type) {
                    if (strpos($ext,'|')) {
                        $expArr = explode('|', $ext);

                        foreach($expArr as $ext){
                            if (in_array($ext, $libArray )) {
                                $jsonArr[$ext] = $type;
                            }
                        }
                    } elseif (in_array($ext, $libArray )) {
                        $jsonArr[$ext] = $type;
                    }

                }

                $libFilter = urlencode(json_encode($jsonArr));
            }

            if ( empty( $this->value ) && ! empty( $this->field['default'] ) ) { // If there are standard values and value is empty
                if ( is_array( $this->field['default'] ) ) {
                    if ( ! empty( $this->field['default']['id'] ) ) {
                        $this->value['id'] = $this->field['default']['id'];
                    }

                    if ( ! empty( $this->field['default']['url'] ) ) {
                        $this->value['url'] = $this->field['default']['url'];
                    }
                } else {
                    if ( is_numeric( $this->field['default'] ) ) { // Check if it's an attachment ID
                        $this->value['id'] = $this->field['default'];
                    } else { // Must be a URL
                        $this->value['url'] = $this->field['default'];
                    }
                }
            }

            if ( empty( $this->value['url'] ) && ! empty( $this->value['id'] ) ) {
                $img                   = wp_get_attachment_image_src( $this->value['id'], 'full' );
                $this->value['url']    = $img[0];
                $this->value['width']  = $img[1];
                $this->value['height'] = $img[2];
            }

            $hide = 'hide ';

            if ( $this->field['preview'] === false ) {
                $this->field['class'] .= " noPreview";
            }

            if ( ( ! empty( $this->field['url'] ) && $this->field['url'] === true ) || $this->field['preview'] === false ) {
                $hide = '';
            }

            $readOnly = '';
            if ( $this->field['readonly'] ) {
                $readOnly = ' readonly="readonly"';
            }

            echo '<input placeholder="' . esc_attr($this->field['placeholder']) . '" type="text" class="' . $hide . 'upload large-text ' . esc_attr($this->field['class']) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[url]" id="' . esc_attr($this->parent->args['opt_name']) . '[' . esc_attr($this->field['id']) . '][url]" value="' . esc_attr($this->value['url']) . '"' . $readOnly . '/>';
            echo '<input type="hidden" class="data" data-preview-size="' . esc_attr($this->field['preview_size']) . '" data-mode="' . esc_attr($this->field['mode']) . '" />';
            echo '<input type="hidden" class="library-filter" data-lib-filter="' . $libFilter . '" />';
            echo '<input type="hidden" class="upload-id ' . esc_attr($this->field['class']) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[id]" id="' . esc_attr($this->parent->args['opt_name']) . '[' . esc_attr($this->field['id']) . '][id]" value="' . esc_attr($this->value['id']) . '" />';
            echo '<input type="hidden" class="upload-height" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[height]" id="' . esc_attr($this->parent->args['opt_name']) . '[' . esc_attr($this->field['id']) . '][height]" value="' . esc_attr($this->value['height']) . '" />';
            echo '<input type="hidden" class="upload-width" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[width]" id="' . esc_attr($this->parent->args['opt_name']) . '[' . esc_attr($this->field['id']) . '][width]" value="' . esc_attr($this->value['width']) . '" />';
            echo '<input type="hidden" class="upload-thumbnail" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[thumbnail]" id="' . esc_attr($this->parent->args['opt_name']) . '[' . esc_attr($this->field['id']) . '][thumbnail]" value="' . esc_attr($this->value['thumbnail']) . '" />';

            //Preview
            $hide = '';

            if ( ( $this->field['preview'] === false ) || empty( $this->value['url'] ) ) {
                $hide .= 'display:none;';
            }

            if ( empty( $this->value['thumbnail'] ) && ! empty( $this->value['url'] ) ) { // Just in case
                if ( ! empty( $this->value['id'] ) ) {
                    $image = wp_get_attachment_image_src( $this->value['id'], array( 150, 150 ) );

                    if (empty($image[0]) || $image[0] == '') {
                        $this->value['thumbnail'] = $this->value['url'];
                    } else {
                        $this->value['thumbnail'] = $image[0];
                    }
                } else {
                    $this->value['thumbnail'] = $this->value['url'];
                }
            }

            $css = '';

            if (ReduxCore::$_pro_loaded) {
                $css = apply_filters('redux/pro/media/render/preview_css', null);
            }
            
            echo '<div class="screenshot" style="' . $hide . '">';
            echo     '<a class="of-uploaded-image" href="' . esc_url($this->value['url']) . '" target="_blank">';
            echo         '<img class="redux-option-image" id="image_' . esc_attr($this->field['id']) . '" src="' . esc_url($this->value['thumbnail']) . '" alt="" target="_blank" rel="external" style="' . $css . '" />';
            echo     '</a>';
            echo '</div>';

            //Upload controls DIV
            echo '<div class="upload_button_div">';

            //If the user has WP3.5+ show upload/remove button
            echo     '<span class="button media_upload_button" id="' . esc_attr($this->field['id']) . '-media">' . esc_html__( 'Upload', 'redux-framework' ) . '</span>';

            $hide = '';
            if ( empty( $this->value['url'] ) || $this->value['url'] == '' ) {
                $hide = ' hide';
            }

            echo     '<span class="button remove-image' . $hide . '" id="reset_' . esc_attr($this->field['id']) . '" rel="' . esc_attr($this->field['id']) . '">' . esc_html__( 'Remove', 'redux-framework' ) . '</span>';
            echo '</div>';

            if (ReduxCore::$_pro_loaded) {
                echo apply_filters('redux/pro/media/render/filters', null);
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {
            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            } else {
                wp_enqueue_script( 'media-upload' );
            }

            wp_enqueue_script(
                'redux-field-media-js',
                ReduxCore::$_url . 'assets/js/media/media' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                ReduxCore::$_version,
                true
            );

            if (ReduxCore::$_pro_loaded) {
                do_action ('redux/pro/media/enqueue');
            }
            
            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style('redux-field-media-css');

            }
        }
        
        public function css_style($data) {
            if (ReduxCore::$_pro_loaded) {
                $pro_data = apply_filters('redux/pro/media/output', $data);

                return $pro_data;
            }            
        }        
    }
}