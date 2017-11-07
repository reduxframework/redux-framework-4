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
 * @subpackage  Field_Images
 * @author      Dovy Paukstys
 * @author      Kevin Provance (kprovance)
 * @version     4.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_image_select' ) ) {

    /**
     * Main ReduxFramework_image_select class
     *
     * @since       1.0.0
     */
    class ReduxFramework_image_select extends Redux_Field {

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {
            if ( ! empty( $this->field['options'] ) ) {
                echo '<div class="redux-table-container">';
                echo '<ul class="redux-image-select">';

                $x = 1;

                foreach ( $this->field['options'] as $k => $v ) {

                    if ( ! is_array( $v ) ) {
                        $v = array( 'img' => $v );
                    }

                    if ( ! isset( $v['title'] ) ) {
                        $v['title'] = '';
                    }

                    if ( ! isset( $v['alt'] ) ) {
                        $v['alt'] = $v['title'];
                    }

                    if ( ! isset( $v['class'] ) ) {
                        $v['class'] = '';
                    }                    
                    
                    $style = '';

                    if ( ! empty( $this->field['width'] ) ) {
                        $style .= 'width: ' . $this->field['width'];

                        if ( is_numeric( $this->field['width'] ) ) {
                            $style .= 'px';
                        }

                        $style .= ';';
                    } else {
                        $style .= " width: 100%; ";
                    }

                    if ( ! empty( $this->field['height'] ) ) {
                        $style .= 'height: ' . $this->field['height'];

                        if ( is_numeric( $this->field['height'] ) ) {
                            $style .= 'px';
                        }

                        $style .= ';';
                    }

                    $theValue = $k;
                    if ( ! empty( $this->field['tiles'] ) && $this->field['tiles'] == true ) {
                        $theValue = $v['img'];
                    }

                    $selected = ( checked( $this->value, $theValue, false ) != '' ) ? ' redux-image-select-selected' : '';

                    $presets   = '';
                    $is_preset = false;

                    $this->field['class'] .= ' noUpdate ';
                    if ( isset( $this->field['presets'] ) && $this->field['presets'] !== false ) {
                        $this->field['class'] = trim($this->field['class']);
                        if ( ! isset( $v['presets'] ) ) {
                            $v['presets'] = array();
                        }

                        if ( ! is_array( $v['presets'] ) ) {
                            $v['presets'] = json_decode( $v['presets'], true );
                        }

                        // Only highlight the preset if it's the same
                        if ( $selected ) {
                            if ( empty( $v['presets'] ) ) {
                                $selected = false;
                            } else {
                                foreach ( $v['presets'] as $pk => $pv ) {
                                    if ( isset( $v['merge'] ) && $v['merge'] !== false ) {
                                        if( ( $v['merge'] === true || in_array( $pk, $v['merge'] ) ) && is_array( $this->parent->options[ $pk ] ) ) {
                                            $pv = array_merge( $this->parent->options[ $pk ], $pv );
                                        }
                                    }

                                    if ( empty( $pv ) && isset( $this->parent->options[ $pk ] ) && ! empty( $this->parent->options[ $pk ] ) ) {
                                        $selected = false;
                                    } else if ( ! empty( $pv ) && ! isset( $this->parent->options[ $pk ] ) ) {
                                        $selected = false;
                                    //} else if ( isset( $this->parent->options[ $pk ] ) && $this->parent->options[ $pk ] != $pv ) {
                                    //    $selected = false;
                                    }

                                    if ( ! $selected ) { // We're still not using the same preset. Let's unset that shall we?
                                        $this->value = "";
                                        break;
                                    }
                                }
                            }
                        }

                        $v['presets']['redux-backup'] = 1;

                        $presets   = ' data-presets="' . htmlspecialchars( json_encode( $v['presets'] ), ENT_QUOTES, 'UTF-8' ) . '"';
                        $is_preset = true;

                        $this->field['class'] = trim( $this->field['class'] ) . ' redux-presets';
                    }

                    $is_preset_class = $is_preset ? '-preset-' : ' ';

                    $merge   = '';
                    if ( isset( $v['merge'] ) && $v['merge'] !== false ) {
                        $merge = is_array( $v['merge'] ) ? implode( '|', $v['merge'] ) : 'true';
                        $merge = ' data-merge="' . htmlspecialchars( $merge, ENT_QUOTES, 'UTF-8' ) . '"';
                    }

                    echo '<li class="redux-image-select">';
                    echo '<label class="' . esc_attr($selected) . ' redux-image-select' . $is_preset_class . esc_attr($this->field['id'] . '_' . $x) . '" for="' . esc_attr($this->field['id'] . '_' . ( array_search( $k, array_keys( $this->field['options'] ) ) + 1 ) ) . '">';

                echo '<input type="radio" class="' . esc_attr($this->field['class']) . '" id="' . esc_attr($this->field['id'] . '_' . ( array_search( $k, array_keys( $this->field['options'] ) ) + 1 ) ) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix'] ) . '" value="' . esc_attr($theValue) . '" ' . checked( $this->value, $theValue, false ) . $presets . $merge . '/>';
                    if ( ! empty( $this->field['tiles'] ) && $this->field['tiles'] == true ) {
                        echo '<span class="tiles ' . esc_attr($v['class']) . '" style="background-image: url(' . esc_url($v['img']) . ');" rel="' . esc_url($v['img']) . '"">&nbsp;</span>';
                    } else {
                        echo '<img src="' . esc_url($v['img']) . '" title="'. esc_attr($v['alt']) . '" alt="' . esc_attr($v['alt']) . '" class="' . esc_attr($v['class']) . '" style="' . esc_attr($style) . '"' . $presets . $merge . ' />';
                    }

                    if ( $v['title'] != '' ) {
                        echo '<br /><span>' . wp_kses_post(($v['title'])) . '</span>';
                    }

                    echo '</label>';
                    echo '</li>';

                    $x ++;
                }

                echo '</ul>';
                echo '</div>';
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

            wp_enqueue_script(
                'redux-field-image-select-js',
                ReduxCore::$_url . 'inc/fields/image_select/field_image_select' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                $this->timestamp,
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-image-select-css',
                    ReduxCore::$_url . 'inc/fields/image_select/field_image_select.css',
                    array(),
                    $this->timestamp,
                    'all'
                );
            }
        }

        public function css_style($data) {
            $css = $output = '';
            
            $mode = ( isset( $this->field['mode'] ) && ! empty( $this->field['mode'] ) ? $this->field['mode'] : 'background-image' );
            
            if ( ! empty( $data ) && ! is_array($data) ) {
                switch ( $mode ) {
                    case 'background-image':
                        $img = isset($this->field['options'][$data]['img']) ? $this->field['options'][$data]['img'] : '';
                        if ($img != '') {
                            $output = "background-image: url('" . esc_url($img) . "');";
                        }
                    break;

                    default:
                        $output = $mode . ": " . $data . ";";
                }
            }

            $css .= $output;
            
            return $css;
        }
    }
}