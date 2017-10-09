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
     * @subpackage  Field_Typogrpahy
     * @author      Kevin Provance (kprovance)
     * @author      Dovy Paukstys
     * @version     4.0.0
     */

    if ( ! class_exists( 'ReduxFramework_typography' ) ) {

        class ReduxFramework_typography extends Redux_Field {

            private $std_fonts = array(
                "Arial, Helvetica, sans-serif"                         => "Arial, Helvetica, sans-serif",
                "'Arial Black', Gadget, sans-serif"                    => "'Arial Black', Gadget, sans-serif",
                "'Bookman Old Style', serif"                           => "'Bookman Old Style', serif",
                "'Comic Sans MS', cursive"                             => "'Comic Sans MS', cursive",
                "Courier, monospace"                                   => "Courier, monospace",
                "Garamond, serif"                                      => "Garamond, serif",
                "Georgia, serif"                                       => "Georgia, serif",
                "Impact, Charcoal, sans-serif"                         => "Impact, Charcoal, sans-serif",
                "'Lucida Console', Monaco, monospace"                  => "'Lucida Console', Monaco, monospace",
                "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"   => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
                "'MS Sans Serif', Geneva, sans-serif"                  => "'MS Sans Serif', Geneva, sans-serif",
                "'MS Serif', 'New York', sans-serif"                   => "'MS Serif', 'New York', sans-serif",
                "'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
                "Tahoma,Geneva, sans-serif"                            => "Tahoma, Geneva, sans-serif",
                "'Times New Roman', Times,serif"                       => "'Times New Roman', Times, serif",
                "'Trebuchet MS', Helvetica, sans-serif"                => "'Trebuchet MS', Helvetica, sans-serif",
                "Verdana, Geneva, sans-serif"                          => "Verdana, Geneva, sans-serif",
            );

            private $user_fonts = true;

            function set_defaults() {
                // Shim out old arg to new
                if ( isset( $this->field['all_styles'] ) && ! empty( $this->field['all_styles'] ) ) {
                    $this->field['all-styles'] = $this->field['all_styles'];
                    unset ( $this->field['all_styles'] );
                }

                // Set field array defaults.  No errors please
                $defaults = array(
                    'font-family'       => true,
                    'font-size'         => true,
                    'font-weight'       => true,
                    'font-style'        => true,
                    'font-backup'       => false,
                    'subsets'           => true,
                    'custom_fonts'      => true,
                    'text-align'        => true,
                    'text-transform'    => false,
                    'font-variant'      => false,
                    'text-decoration'   => false,
                    'color'             => true,
                    'preview'           => true,
                    'line-height'       => true,
                    'multi'             => array(
                        'subset' => false,
                        'weight' => false,
                    ),
                    'word-spacing'      => false,
                    'letter-spacing'    => false,
                    'google'            => true,
                    'update_weekly'     => false,    // Enable to force updates of Google Fonts to be weekly
                    'font_family_clear' => true,
                );

                $this->field = wp_parse_args( $this->field, $defaults );

                // Set value defaults.
                $defaults = array(
                    'font-family'     => '',
                    'font-options'    => '',
                    'font-backup'     => '',
                    'text-align'      => '',
                    'text-transform'  => '',
                    'font-variant'    => '',
                    'text-decoration' => '',
                    'line-height'     => '',
                    'word-spacing'    => '',
                    'letter-spacing'  => '',
                    'subsets'         => '',
                    'google'          => false,
                    'font-script'     => '',
                    'font-weight'     => '',
                    'font-style'      => '',
                    'color'           => '',
                    'font-size'       => '',
                );

                $this->value = wp_parse_args( $this->value, $defaults );

                if ( empty( $this->field['units'] ) || ! in_array( $this->field['units'], array(
                        'px',
                        'em',
                        'rem',
                        '%'
                    ) )
                ) {
                    $this->field['units'] = 'px';
                }
                
                if (ReduxCore::$_pro_loaded) {
                    $this->field = apply_filters('redux/pro/typography/field/set_defaults', $this->field);
                    $this->value = apply_filters('redux/pro/typography/value/set_defaults', $this->value);
                } else {
                    $this->field['color_alpha'] = array();
                    $this->field['color_alpha']['color'] = false;
                    $this->field['color_alpha']['shadow-color'] = false;
                }
                
                // Get the google array
                $this->getGoogleArray();

                if ( empty( $this->field['fonts'] ) ) {
                    $this->user_fonts     = false;
                    $this->field['fonts'] = $this->std_fonts;
                }

                // Localize std fonts
                $this->localize_std_fonts();
            }

            public function google_fonts_update() {
                echo "here";
            }

            function localize( $field, $value = "" ) {
                $params = array();

                if ( true == $this->user_fonts && ! empty( $this->field['fonts'] ) ) {
                    $params['std_font'] = $this->field['fonts'];
                }

                return $params;
            }


            /**
             * Field Render Function.
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since ReduxFramework 1.0.0
             */
            function render() {
                // Since fonts declared is CSS (@font-face) are not rendered in the preview,
                // they can be declared in a CSS file and passed here so they DO display in
                // font preview.  Do NOT pass style.css in your theme, as that will mess up
                // admin page styling.  It's recommended to pass a CSS file with ONLY font
                // declarations.
                // If field is set and not blank, then enqueue field
                if ( isset( $this->field['ext-font-css'] ) && $this->field['ext-font-css'] != '' ) {
                    wp_register_style( 'redux-external-fonts', $this->field['ext-font-css'] );
                    wp_enqueue_style( 'redux-external-fonts' );
                }

                if ( empty( $this->field['units'] ) && ! empty( $this->field['default']['units'] ) ) {
                    $this->field['units'] = $this->field['default']['units'];
                }

                $unit = $this->field['units'];

                echo '<div id="' . esc_attr( $this->field['id'] ) . '" class="redux-typography-container" data-id="' . esc_attr( $this->field['id'] ) . '" data-units="' . esc_attr( $unit ) . '">';

                $this->select2_config['allowClear'] = true;

                if ( isset( $this->field['select2'] ) ) {
                    $this->field['select2'] = wp_parse_args( $this->field['select2'], $this->select2_config );
                } else {
                    $this->field['select2'] = $this->select2_config;
                }

                $this->field['select2'] = Redux_Functions::sanitize_camel_case_array_keys( $this->field['select2'] );

                $select2_data = Redux_Functions::create_data_string( $this->field['select2'] );

                /* Font Family */
                if ( $this->field['font-family'] === true ) {
                    if ( filter_var( $this->value['google'], FILTER_VALIDATE_BOOLEAN ) ) {

                        // Divide and conquer
                        $fontFamily = explode( ', ', $this->value['font-family'], 2 );

                        // If array 0 is empty and array 1 is not
                        if ( empty( $fontFamily[0] ) && ! empty( $fontFamily[1] ) ) {

                            // Make array 0 = array 1
                            $fontFamily[0] = $fontFamily[1];

                            // Clear array 1
                            $fontFamily[1] = "";
                        }
                    }

                    // If no fontFamily array exists, create one and set array 0
                    // with font value
                    if ( ! isset( $fontFamily ) ) {
                        $fontFamily    = array();
                        $fontFamily[0] = $this->value['font-family'];
                        $fontFamily[1] = "";
                    }

                    // Is selected font a Google font
                    $isGoogleFont = '0';
                    if ( isset( $this->parent->fonts['google'][ $fontFamily[0] ] ) ) {
                        $isGoogleFont = '1';
                    }

                    // If not a Google font, show all font families
                    if ( $isGoogleFont != '1' ) {
                        $fontFamily[0] = $this->value['font-family'];
                    }

                    $userFonts = '0';
                    if ( true == $this->user_fonts ) {
                        $userFonts = '1';
                    }

                    echo '<input type="hidden" class="redux-typography-font-family ' . esc_attr( $this->field['class'] ) . '" data-user-fonts="' . $userFonts . '" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[font-family]' . '" value="' . esc_attr( $this->value['font-family'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"  />';
                    echo '<input type="hidden" class="redux-typography-font-options ' . esc_attr( $this->field['class'] ) . '" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[font-options]' . '" value="' . esc_attr( $this->value['font-options'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"  />';

                    echo '<input type="hidden" class="redux-typography-google-font" value="' . $isGoogleFont . '" id="' . esc_attr( $this->field['id'] ) . '-google-font">';

                    echo '<div class="select_wrapper typography-family" style="width: 220px; margin-right: 5px;">';
                    echo '<label>' . esc_html__( 'Font Family', 'redux-framework' ) . '</label>';

                    $placeholder = esc_html__( 'Font family', 'redux-framework' );

                    $new_arr                = $this->field['select2'];
                    $new_arr['allow-clear'] = $this->field['font_family_clear'];
                    $new_data               = Redux_Functions::create_data_string( $new_arr );

                    echo '<select class=" redux-typography redux-typography-family select2-container ' . esc_attr( $this->field['class'] ) . '" id="' . esc_attr( $this->field['id'] ) . '-family" data-placeholder="' . esc_attr( $placeholder ) . '" data-id="' . esc_attr( $this->field['id'] ) . '" data-value="' . esc_attr( $fontFamily[0] ) . '"' . esc_html( $new_data ) . '>';

                    echo '</select>';
                    echo '</div>';

                    $googleSet = false;
                    if ( $this->field['google'] === true ) {

                        // Set a flag so we know to set a header style or not
                        echo '<input type="hidden" class="redux-typography-google ' . esc_attr( $this->field['class'] ) . '" id="' . esc_attr( $this->field['id'] ) . '-google" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[google]' . '" type="text" value="' . esc_attr( $this->field['google'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '" />';
                        $googleSet = true;
                    }
                }

                /* Backup Font */
                if ( $this->field['font-family'] === true && $this->field['google'] === true ) {
                    if ( false == $googleSet ) {
                        // Set a flag so we know to set a header style or not
                        echo '<input type="hidden" class="redux-typography-google ' . esc_attr( $this->field['class'] ) . '" id="' . esc_attr( $this->field['id'] ) . '-google" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[google]' . '" type="text" value="' . esc_attr( $this->field['google'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"  />';
                    }

                    if ( $this->field['font-backup'] === true ) {
                        echo '<div class="select_wrapper typography-family-backup" style="width: 220px; margin-right: 5px;">';
                        echo '<label>' . esc_html__( 'Backup Font Family', 'redux-framework' ) . '</label>';
                        echo '<select data-placeholder="' . esc_html__( 'Backup Font Family', 'redux-framework' ) . '" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[font-backup]' . '" class="redux-typography redux-typography-family-backup ' . esc_attr( $this->field['class'] ) . '" id="' . esc_attr( $this->field['id'] ) . '-family-backup" data-id="' . esc_attr( $this->field['id'] ) . '" data-value="' . esc_attr( $this->value['font-backup'] ) . '"' . esc_attr( $select2_data ) . '>';
                        echo '<option data-google="false" data-details="" value=""></option>';

                        foreach ( $this->field['fonts'] as $i => $family ) {
                            echo '<option data-google="true" value="' . esc_attr( $i ) . '"' . selected( $this->value['font-backup'], $i, false ) . '>' . esc_html( $family ) . '</option>';
                        }

                        echo '</select></div>';
                    }
                }

                /* Font Style/Weight */
                if ( $this->field['font-style'] === true || $this->field['font-weight'] === true ) {

                    echo '<div class="select_wrapper typography-style" original-title="' . esc_html__( 'Font style', 'redux-framework' ) . '">';
                    echo '<label>' . esc_html__( 'Font Weight &amp; Style', 'redux-framework' ) . '</label>';

                    $style = $this->value['font-weight'] . $this->value['font-style'];

                    echo '<input type="hidden" class="typography-font-weight" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[font-weight]' . '" value="' . esc_attr( $this->value['font-weight'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"  /> ';
                    echo '<input type="hidden" class="typography-font-style" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[font-style]' . '" value="' . esc_attr( $this->value['font-style'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"  /> ';
                    $multi = ( isset( $this->field['multi']['weight'] ) && $this->field['multi']['weight'] ) ? ' multiple="multiple"' : "";
                    echo '<select' . $multi . ' data-placeholder="' . esc_html__( 'Style', 'redux-framework' ) . '" class="redux-typography redux-typography-style select ' . esc_attr( $this->field['class'] ) . '" original-title="' . esc_html__( 'Font style', 'redux-framework' ) . '" id="' . esc_attr( $this->field['id'] ) . '_style" data-id="' . esc_attr( $this->field['id'] ) . '" data-value="' . esc_attr( $style ) . '"' . esc_attr( $select2_data ) . '>';

                    if ( empty( $this->value['subsets'] ) || empty( $this->value['font-weight'] ) ) {
                        echo '<option value=""></option>';
                    }

                    $nonGStyles = array(
                        '200' => esc_html__( 'Lighter', 'redux-framework' ),
                        '400' => esc_html__( 'Normal', 'redux-framework' ),
                        '700' => esc_html__( 'Bold', 'redux-framework' ),
                        '900' => esc_html__( 'Bolder', 'redux-framework' )
                    );

                    if ( isset( $gfonts[ $this->value['font-family'] ] ) ) {
                        foreach ( $gfonts[ $this->value['font-family'] ]['variants'] as $v ) {
                            echo '<option value="' . esc_attr( $v['id'] ) . '" ' . selected( $this->value['subsets'], $v['id'], false ) . '>' . esc_html( $v['name'] ) . '</option>';
                        }
                    } else {
                        if ( ! isset( $this->value['font-weight'] ) && isset( $this->value['subsets'] ) ) {
                            $this->value['font-weight'] = $this->value['subsets'];
                        }

                        foreach ( $nonGStyles as $i => $style ) {
                            if ( ! isset( $this->value['font-weight'] ) ) {
                                $this->value['font-weight'] = false;
                            }

                            if ( ! isset( $this->value['subsets'] ) ) {
                                $this->value['subsets'] = false;
                            }

                            echo '<option value="' . esc_attr( $i ) . '" ' . selected( $this->value['font-weight'], $i, false ) . '>' . esc_html( $style ) . '</option>';
                        }
                    }

                    echo '</select></div>';
                }

                /* Font Script */
                if ( $this->field['font-family'] == true && $this->field['subsets'] == true && $this->field['google'] == true ) {
                    echo '<div class="select_wrapper typography-script tooltip" original-title="' . esc_html__( 'Font subsets', 'redux-framework' ) . '">';
                    echo '<input type="hidden" class="typography-subsets" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[subsets]' . '" value="' . esc_attr( $this->value['subsets'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"  /> ';
                    echo '<label>' . esc_html__( 'Font Subsets', 'redux-framework' ) . '</label>';
                    $multi = ( isset( $this->field['multi']['subset'] ) && $this->field['multi']['subset'] ) ? ' multiple="multiple"' : "";
                    echo '<select' . $multi . ' data-placeholder="' . esc_html__( 'Subsets', 'redux-framework' ) . '" class="redux-typography redux-typography-subsets ' . esc_attr( $this->field['class'] ) . '" original-title="' . esc_html__( 'Font script', 'redux-framework' ) . '"  id="' . esc_attr( $this->field['id'] ) . '-subsets" data-value="' . esc_attr( $this->value['subsets'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"' . esc_attr( $select2_data ) . '>';

                    if ( empty( $this->value['subsets'] ) ) {
                        echo '<option value=""></option>';
                    }

                    if ( isset( $gfonts[ $this->value['font-family'] ] ) ) {
                        foreach ( $gfonts[ $this->value['font-family'] ]['subsets'] as $v ) {
                            echo '<option value="' . esc_attr( $v['id'] ) . '" ' . selected( $this->value['subsets'], $v['id'], false ) . '>' . esc_html( $v['name'] ) . '</option>';
                        }
                    }

                    echo '</select></div>';
                }

                /* Font Align */
                if ( $this->field['text-align'] === true ) {
                    echo '<div class="select_wrapper typography-align tooltip" original-title="' . esc_html__( 'Text Align', 'redux-framework' ) . '">';
                    echo '<label>' . esc_html__( 'Text Align', 'redux-framework' ) . '</label>';
                    echo '<select data-placeholder="' . esc_html__( 'Text Align', 'redux-framework' ) . '" class="redux-typography redux-typography-align ' . $this->field['class'] . '" original-title="' . esc_html__( 'Text Align', 'redux-framework' ) . '"  id="' . $this->field['id'] . '-align" name="' . $this->field['name'] . $this->field['name_suffix'] . '[text-align]' . '" data-value="' . $this->value['text-align'] . '" data-id="' . $this->field['id'] . '"' . esc_attr( $select2_data ) . '>';
                    echo '<option value=""></option>';

                    $align = array(
                        esc_html__( 'inherit', 'redux-framework' ),
                        esc_html__( 'left', 'redux-framework' ),
                        esc_html__( 'right', 'redux-framework' ),
                        esc_html__( 'center', 'redux-framework' ),
                        esc_html__( 'justify', 'redux-framework' ),
                        esc_html__( 'initial', 'redux-framework' )
                    );

                    foreach ( $align as $v ) {
                        echo '<option value="' . esc_attr( $v ) . '" ' . selected( $this->value['text-align'], $v, false ) . '>' . esc_html( ucfirst( $v ) ) . '</option>';
                    }

                    echo '</select></div>';
                }

                /* Text Transform */
                if ( $this->field['text-transform'] === true ) {
                    echo '<div class="select_wrapper typography-transform tooltip" original-title="' . esc_html__( 'Text Transform', 'redux-framework' ) . '">';
                    echo '<label>' . esc_html__( 'Text Transform', 'redux-framework' ) . '</label>';
                    echo '<select data-placeholder="' . esc_html__( 'Text Transform', 'redux-framework' ) . '" class="redux-typography redux-typography-transform ' . esc_attr( $this->field['class'] ) . '" original-title="' . esc_html__( 'Text Transform', 'redux-framework' ) . '"  id="' . esc_attr( $this->field['id'] ) . '-transform" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[text-transform]' . '" data-value="' . esc_attr( $this->value['text-transform'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"' . esc_attr( $select2_data ) . '>';
                    echo '<option value=""></option>';

                    $values = array(
                        esc_html__( 'none', 'redux-framework' ),
                        esc_html__( 'capitalize', 'redux-framework' ),
                        esc_html__( 'uppercase', 'redux-framework' ),
                        esc_html__( 'lowercase', 'redux-framework' ),
                        esc_html__( 'initial', 'redux-framework' ),
                        esc_html__( 'inherit', 'redux-framework' )
                    );

                    foreach ( $values as $v ) {
                        echo '<option value="' . esc_attr( $v ) . '" ' . selected( $this->value['text-transform'], $v, false ) . '>' . esc_html( ucfirst( $v ) ) . '</option>';
                    }

                    echo '</select></div>';
                }

                /* Font Variant */
                if ( $this->field['font-variant'] === true ) {
                    echo '<div class="select_wrapper typography-font-variant tooltip" original-title="' . esc_html__( 'Font Variant', 'redux-framework' ) . '">';
                    echo '<label>' . esc_html__( 'Font Variant', 'redux-framework' ) . '</label>';
                    echo '<select data-placeholder="' . esc_html__( 'Font Variant', 'redux-framework' ) . '" class="redux-typography redux-typography-font-variant ' . esc_attr( $this->field['class'] ) . '" original-title="' . esc_html__( 'Font Variant', 'redux-framework' ) . '"  id="' . esc_attr( $this->field['id'] ) . '-font-variant" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[font-variant]' . '" data-value="' . esc_attr( $this->value['font-variant'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"' . esc_attr( $select2_data ) . '>';
                    echo '<option value=""></option>';

                    $values = array(
                        esc_html__( 'inherit', 'redux-framework' ),
                        esc_html__( 'normal', 'redux-framework' ),
                        esc_html__( 'small-caps', 'redux-framework' )
                    );

                    foreach ( $values as $v ) {
                        echo '<option value="' . esc_attr( $v ) . '" ' . selected( $this->value['font-variant'], $v, false ) . '>' . esc_attr( ucfirst( $v ) ) . '</option>';
                    }

                    echo '</select></div>';
                }

                /* Text Decoration */
                if ( $this->field['text-decoration'] === true ) {
                    echo '<div class="select_wrapper typography-decoration tooltip" original-title="' . esc_html__( 'Text Decoration', 'redux-framework' ) . '">';
                    echo '<label>' . esc_html__( 'Text Decoration', 'redux-framework' ) . '</label>';
                    echo '<select data-placeholder="' . esc_html__( 'Text Decoration', 'redux-framework' ) . '" class="redux-typography redux-typography-decoration ' . esc_attr( $this->field['class'] ) . '" original-title="' . esc_html__( 'Text Decoration', 'redux-framework' ) . '"  id="' . esc_attr( $this->field['id'] ) . '-decoration" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[text-decoration]' . '" data-value="' . esc_attr( $this->value['text-decoration'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"' . esc_attr( $select2_data ) . '>';
                    echo '<option value=""></option>';

                    $values = array(
                        esc_html__( 'none', 'redux-framework' ),
                        esc_html__( 'inherit', 'redux-framework' ),
                        esc_html__( 'underline', 'redux-framework' ),
                        esc_html__( 'overline', 'redux-framework' ),
                        esc_html__( 'line-through', 'redux-framework' ),
                        esc_html__( 'blink', 'redux-framework' )
                    );

                    foreach ( $values as $v ) {
                        echo '<option value="' . esc_attr( $v ) . '" ' . selected( $this->value['text-decoration'], $v, false ) . '>' . esc_html( ucfirst( $v ) ) . '</option>';
                    }

                    echo '</select></div>';
                }

                /* Font Size */
                if ( $this->field['font-size'] === true ) {
                    echo '<div class="input_wrapper font-size redux-container-typography">';
                    echo '<label>' . esc_html__( 'Font Size', 'redux-framework' ) . '</label>';
                    echo '<div class="input-append"><input type="text" class="span2 redux-typography redux-typography-size mini typography-input ' . esc_attr( $this->field['class'] ) . '" title="' . esc_html__( 'Font Size', 'redux-framework' ) . '" placeholder="' . esc_html__( 'Size', 'redux-framework' ) . '" id="' . esc_attr( $this->field['id'] ) . '-size" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[font-size]' . '" value="' . esc_attr( str_replace( $unit, '', $this->value['font-size'] ) ) . '" data-value="' . esc_attr( str_replace( $unit, '', $this->value['font-size'] ) ) . '"><span class="add-on">' . esc_html( $unit ) . '</span></div>';
                    echo '<input type="hidden" class="typography-font-size" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[font-size]' . '" value="' . esc_attr( $this->value['font-size'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"  />';
                    echo '</div>';
                }

                /* Line Height */
                if ( $this->field['line-height'] === true ) {
                    echo '<div class="input_wrapper line-height redux-container-typography">';
                    echo '<label>' . esc_html__( 'Line Height', 'redux-framework' ) . '</label>';
                    echo '<div class="input-append"><input type="text" class="span2 redux-typography redux-typography-height mini typography-input ' . esc_attr( $this->field['class'] ) . '" title="' . esc_html__( 'Line Height', 'redux-framework' ) . '" placeholder="' . esc_html__( 'Height', 'redux-framework' ) . '" id="' . esc_attr( $this->field['id'] ) . '-height" value="' . esc_attr( str_replace( $unit, '', $this->value['line-height'] ) ) . '" data-value="' . esc_attr( str_replace( $unit, '', $this->value['line-height'] ) ) . '"><span class="add-on">' . esc_html( $unit ) . '</span></div>';
                    echo '<input type="hidden" class="typography-line-height" name="' . $this->field['name'] . $this->field['name_suffix'] . '[line-height]' . '" value="' . $this->value['line-height'] . '" data-id="' . $this->field['id'] . '"  />';
                    echo '</div>';
                }

                /* Word Spacing */
                if ( $this->field['word-spacing'] === true ) {
                    echo '<div class="input_wrapper word-spacing redux-container-typography">';
                    echo '<label>' . esc_html__( 'Word Spacing', 'redux-framework' ) . '</label>';
                    echo '<div class="input-append">';
                    echo '<input type="text" class="span2 redux-typography redux-typography-word mini typography-input ' . esc_attr( $this->field['class'] ) . '" title="' . esc_html__( 'Word Spacing', 'redux-framework' ) . '" placeholder="' . esc_html__( 'Word Spacing', 'redux-framework' ) . '" id="' . esc_attr( $this->field['id'] ) . '-word" value="' . str_replace( $unit, '', $this->value['word-spacing'] ) . '" data-value="' . esc_attr( str_replace( $unit, '', $this->value['word-spacing'] ) ) . '">';
                    echo '<span class="add-on">' . esc_html( $unit ) . '</span>';
                    echo '</div>';
                    echo '<input type="hidden" class="typography-word-spacing" name="' . $this->field['name'] . $this->field['name_suffix'] . '[word-spacing]' . '" value="' . $this->value['word-spacing'] . '" data-id="' . $this->field['id'] . '"  />';
                    echo '</div>';
                }

                /* Letter Spacing */
                if ( $this->field['letter-spacing'] === true ) {
                    echo '<div class="input_wrapper letter-spacing redux-container-typography">';
                    echo '<label>' . esc_html__( 'Letter Spacing', 'redux-framework' ) . '</label>';
                    echo '<div class="input-append">';
                    echo '<input type="text" class="span2 redux-typography redux-typography-letter mini typography-input ' . esc_attr( $this->field['class'] ) . '" title="' . esc_html__( 'Letter Spacing', 'redux-framework' ) . '" placeholder="' . esc_html__( 'Letter Spacing', 'redux-framework' ) . '" id="' . esc_attr( $this->field['id'] ) . '-letter" value="' . esc_attr( str_replace( $unit, '', $this->value['letter-spacing'] ) ) . '" data-value="' . esc_attr( str_replace( $unit, '', $this->value['letter-spacing'] ) ) . '">';
                    echo '<span class="add-on">' . esc_html( $unit ) . '</span>';
                    echo '</div>';
                    echo '<input type="hidden" class="typography-letter-spacing" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[letter-spacing]' . '" value="' . esc_attr( $this->value['letter-spacing'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '"  />';
                    echo '</div>';
                }

                echo '<div class="clearfix"></div>';

                if (ReduxCore::$_pro_loaded) {
                    echo apply_filters('redux/pro/typography/render/extra_inputs', null);
                }

                /* Font Color */
                if ( $this->field['color'] === true ) {
                    $default = "";

                    if ( empty( $this->field['default']['color'] ) && ! empty( $this->field['color'] ) ) {
                        $default = $this->value['color'];
                    } else if ( ! empty( $this->field['default']['color'] ) ) {
                        $default = $this->field['default']['color'];
                    }

                    echo '<div class="picker-wrapper">';
                    echo '<label>' . esc_html__( 'Font Color', 'redux-framework' ) . '</label>';
                    echo '<div id="' . esc_attr( $this->field['id'] ) . '_color_picker" class="colorSelector typography-color"><div style="background-color: ' . esc_attr( $this->value['color'] ) . '"></div></div>';
                    echo '<input
                        data-default-color="' . esc_attr( $default ) . '"
                        class="color-picker redux-color redux-typography-color ' . esc_attr( $this->field['class'] ) . '"
                        original-title="' . esc_html__( 'Font color', 'redux-framework' ) . '"
                        id="' . esc_attr( $this->field['id'] ) . '-color" 
                        name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[color]' . '"
                        type="text" 
                        value="' . esc_attr( $this->value['color'] ) . '"
                        data-id="' . esc_attr( $this->field['id'] ) . '"
                        data-alpha="' . $this->field['color_alpha']['color'] . '"
                        />';
                    echo '</div>';
                }

                echo '<div class="clearfix"></div>';

                /* Font Preview */
                if ( ! isset( $this->field['preview'] ) || $this->field['preview'] !== false ) {
                    if ( isset( $this->field['preview']['text'] ) ) {
                        $g_text = $this->field['preview']['text'];
                    } else {
                        $g_text = '1 2 3 4 5 6 7 8 9 0 A B C D E F G H I J K L M N O P Q R S T U V W X Y Z a b c d e f g h i j k l m n o p q r s t u v w x y z';
                    }

                    $style = '';
                    if ( isset( $this->field['preview']['always_display'] ) ) {
                        if ( true === filter_var( $this->field['preview']['always_display'], FILTER_VALIDATE_BOOLEAN ) ) {
                            if ( $isGoogleFont == true ) {
                                $this->parent->typography_preview[ $fontFamily[0] ] = array(
                                    'font-style' => array( $this->value['font-weight'] . $this->value['font-style'] ),
                                    'subset'     => array( $this->value['subsets'] )
                                );

                                $protocol = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https:" : "http:";

                                wp_deregister_style( 'redux-typography-preview' );
                                wp_dequeue_style( 'redux-typography-preview' );

                                wp_register_style( 'redux-typography-preview', $protocol . $this->make_google_web_font_link( $this->parent->typography_preview ), array(), ReduxCore::$_version );
                                wp_enqueue_style( 'redux-typography-preview' );
                            }

                            $style = 'display: block; font-family: ' . esc_attr( $this->value['font-family'] ) . '; font-weight: ' . esc_attr( $this->value['font-weight'] ) . ';';
                        }
                    }

                    if ( isset( $this->field['preview']['font-size'] ) ) {
                        $style .= 'font-size: ' . $this->field['preview']['font-size'] . ';';
                        $inUse = '1';
                    } else {
                        //$g_size = '';
                        $inUse = '0';
                    }

                    if ( Redux_Helpers::google_fonts_update_needed() && ! get_option( 'auto_update_redux_google_fonts', false ) ) {
                        $nonce = wp_create_nonce( "redux_update_google_fonts" );
                        echo '<div data-nonce="' . $nonce . '" class="redux-update-google-fonts update-message notice inline notice-warning notice-alt"><p>' . esc_html__( 'Your Google Fonts are out of date.', 'redux-framework' ) . ' <a href="#" class="update-google-fonts" data-action="automatic" aria-label="' . esc_attr__( 'Keep updated', 'redux-framework' ) . '">' . esc_html__( 'Keep updated', 'redux-framework' ) . '</a> or <a href="#" class="update-google-fonts" data-action="manual" aria-label="' . esc_attr__( 'one-time update', 'redux-framework' ) . '">' . esc_html__( 'one-time update', 'redux-framework' ) . '</a>.</p></div>';
                    }

                    echo '<p data-preview-size="' . $inUse . '" class="clear ' . esc_attr( $this->field['id'] ) . '_previewer typography-preview" ' . 'style="' . esc_attr( $style ) . '">' . esc_html( $g_text ) . '</p>';

                    if (ReduxCore::$_pro_loaded) {
                        echo apply_filters('redux/pro/typography/render/text_shadow', null);
                    }
                    
                    echo '</div>'; // end typography container
                }
            }

            /**
             * Enqueue Function.
             * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
             *
             * @since ReduxFramework 1.0.0
             */
            function enqueue() {

                $min = Redux_Functions::isMin();

                if ( ! wp_style_is( 'select2-css' ) ) {
                    wp_enqueue_style( 'select2-css' );
                }

                if ( ! wp_style_is( 'wp-color-picker' ) ) {
                    wp_enqueue_style( 'wp-color-picker' );
                }

                wp_enqueue_script(
                    'redux-webfont-js',
                    "https://ajax.googleapis.com/ajax/libs/webfont/1.5.0/webfont.js?ver=1.5.0",
                    ReduxCore::$_version,
                    true
                );

                $dep_array = array( 'jquery', 'wp-color-picker', 'select2-js', 'redux-js', 'redux-webfont-js' );

                wp_enqueue_script(
                    'redux-field-typography-js',
                    ReduxCore::$_url . "inc/fields/typography/field_typography$min.js",
                    $dep_array,
                    ReduxCore::$_version,
                    true
                );

                wp_localize_script(
                    'redux-field-typography-js',
                    'redux_ajax_script',
                    array(
                        'ajaxurl'             => esc_url(
                            admin_url( 'admin-ajax.php' )
                        ),
                        'update_google_fonts' => array(
                            'updating' => __( 'Downloading Google Fonts...', 'redux-framework' ),
                            'success'  => __( 'Updated!', 'redux-framework' ),
                            'error'    => __( 'Update Failed. <a href="#" class="update-google-fonts" data-action="%s" aria-label="Retry?">Retry?</a>', 'redux-framework' ),
                            'success'  => sprintf( wp_kses( __( 'Updated! <a href="%s">Reload the page</a> to view your updated fonts.', 'redux-framework' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'javascript:location.reload();' ) )
                        )
                    )
                );

                var_dump(ReduxCore::$_pro_loaded);
                if (ReduxCore::$_pro_loaded) {
                    do_action ('redux/pro/typography/enqueue');
                }
                
                if ( $this->parent->args['dev_mode'] ) {
                    wp_enqueue_style( 'redux-color-picker-css' );

                    wp_enqueue_style(
                        'redux-field-typography-css',
                        ReduxCore::$_url . 'inc/fields/typography/field_typography.css',
                        array(),
                        time(),
                        'all'
                    );
                }
            }

            /**
             * make_google_web_font_link Function.
             * Creates the google fonts link.
             *
             * @since ReduxFramework 3.0.0
             */
            function make_google_web_font_link( $fonts ) {
                $link    = "";
                $subsets = array();

                foreach ( $fonts as $family => $font ) {
                    if ( ! empty( $link ) ) {
                        $link .= "%7C"; // Append a new font to the string
                    }
                    $link .= $family;

                    if ( ! empty( $font['font-style'] ) || ! empty( $font['all-styles'] ) ) {
                        $link .= ':';
                        if ( ! empty( $font['all-styles'] ) ) {
                            $link .= implode( ',', $font['all-styles'] );
                        } else if ( ! empty( $font['font-style'] ) ) {
                            $link .= implode( ',', $font['font-style'] );
                        }
                    }

                    if ( ! empty( $font['subset'] ) || ! empty( $font['all-subsets'] ) ) {
                        if ( ! empty( $font['all-subsets'] ) ) {
                            foreach ( $font['all-subsets'] as $subset ) {
                                if ( ! in_array( $subset, $subsets ) ) {
                                    array_push( $subsets, $subset );
                                }
                            }
                        } else if ( ! empty( $font['subset'] ) ) {
                            foreach ( $font['subset'] as $subset ) {
                                if ( ! in_array( $subset, $subsets ) ) {
                                    array_push( $subsets, $subset );
                                }
                            }
                        }
                    }
                }

                if ( ! empty( $subsets ) ) {
                    $link .= "&subset=" . implode( ',', $subsets );
                }

                return '//fonts.googleapis.com/css?family=' . $link;
            }

            /**
             * make_google_web_font_string Function.
             * Creates the google fonts link.
             *
             * @since ReduxFramework 3.1.8
             */
            function make_google_web_font_string( $fonts ) {
                $link    = "";
                $subsets = array();

                foreach ( $fonts as $family => $font ) {
                    if ( ! empty( $link ) ) {
                        $link .= "', '"; // Append a new font to the string
                    }
                    $link .= $family;

                    if ( ! empty( $font['font-style'] ) || ! empty( $font['all-styles'] ) ) {
                        $link .= ':';
                        if ( ! empty( $font['all-styles'] ) ) {
                            $link .= implode( ',', $font['all-styles'] );
                        } else if ( ! empty( $font['font-style'] ) ) {
                            $link .= implode( ',', $font['font-style'] );
                        }
                    }

                    if ( ! empty( $font['subset'] ) || ! empty( $font['all-subsets'] ) ) {
                        if ( ! empty( $font['all-subsets'] ) ) {
                            foreach ( $font['all-subsets'] as $subset ) {
                                if ( ! in_array( $subset, $subsets ) ) {
                                    array_push( $subsets, $subset );
                                }
                            }
                        } else if ( ! empty( $font['subset'] ) ) {
                            foreach ( $font['subset'] as $subset ) {
                                if ( ! in_array( $subset, $subsets ) ) {
                                    array_push( $subsets, $subset );
                                }
                            }
                        }
                    }
                }

                if ( ! empty( $subsets ) ) {
                    $link .= "&subset=" . implode( ',', $subsets );
                }

                return "'" . $link . "'";
            }

            public function css_style( $data ) {
                $style = '';

                $font = $data;

                // Shim out old arg to new
                if ( isset( $this->field['all_styles'] ) && ! empty( $this->field['all_styles'] ) ) {
                    $this->field['all-styles'] = $this->field['all_styles'];
                    unset ( $this->field['all_styles'] );
                }

                // Check for font-backup.  If it's set, stick it on a variabhle for
                // later use.
                if ( ! empty( $font['font-family'] ) && ! empty( $font['font-backup'] ) ) {
                    $font['font-family'] = str_replace( ', ' . $font['font-backup'], '', $font['font-family'] );
                    $fontBackup          = ',' . $font['font-backup'];
                }

                $fontValueSet = false;

                if ( ! empty( $font ) ) {
                    foreach ( $font as $key => $value ) {
                        if ( ! empty( $value ) && in_array( $key, array( 'font-family', 'font-weight' ) ) ) {
                            $fontValueSet = true;
                        }
                    }
                }

                if ( ! empty( $font ) ) {
                    foreach ( $font as $key => $value ) {
                        if ( $key == 'font-options' ) {
                            continue;
                        }
                        // Check for font-family key
                        if ( 'font-family' == $key ) {

                            // Enclose font family in quotes if spaces are in the
                            // name.  This is necessary because if there are numerics
                            // in the font name, they will not render properly.
                            // Google should know better.
                            if ( strpos( $value, ' ' ) && ! strpos( $value, ',' ) ) {
                                $value = '"' . $value . '"';
                            }

                            // Ensure fontBackup isn't empty (we already option
                            // checked this earlier.  No need to do it again.
                            if ( ! empty( $fontBackup ) ) {

                                // Apply the backup font to the font-family element
                                // via the saved variable.  We do this here so it
                                // doesn't get appended to the Google stuff below.
                                $value .= $fontBackup;
                            }
                        }

                        if ( empty( $value ) && in_array( $key, array(
                                'font-weight',
                                'font-style'
                            ) ) && $fontValueSet == true
                        ) {
                            $value = "normal";
                        }

                        if ( $key == 'font-weight' && $this->field['font-weight'] == false ) {
                            continue;
                        }

                        if ( $key == 'font-style' && $this->field['font-style'] == false ) {
                            continue;
                        }


                        if ( $key == "google" || $key == "subsets" || $key == "font-backup" || empty( $value ) ) {
                            continue;
                        }

                        $pro_data = apply_filters('redux/pro/typography/output', $data, $key, $value);
                        
                        extract($pro_data);
                        
                        if ($continue) {
                            continue;
                        }
                        
                        $style .= $key . ':' . $value . ';';
                    }

                    if ( isset( $this->parent->args['async_typography'] ) && $this->parent->args['async_typography'] ) {
                        $style .= 'opacity: 1;visibility: visible;-webkit-transition: opacity 0.24s ease-in-out;-moz-transition: opacity 0.24s ease-in-out;transition: opacity 0.24s ease-in-out;';
                    }
                }

                return $style;
            }

            public function output( $style = '' ) {
                $font = $this->value;

                if ( $style != '' ) {
                    if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                        $keys                    = implode( ",", $this->field['output'] );
                        $this->parent->outputCSS .= $keys . "{" . $style . '}';

                        if ( isset( $this->parent->args['async_typography'] ) && $this->parent->args['async_typography'] ) {
                            $key_string    = "";
                            $key_string_ie = "";

                            foreach ( $this->field['output'] as $value ) {
                                $key_string    .= ".wf-loading " . $value . ',';
                                $key_string_ie .= ".ie.wf-loading " . $value . ',';
                            }

                            $this->parent->outputCSS .= $key_string . "{opacity: 0;}";
                            $this->parent->outputCSS .= $key_string_ie . "{visibility: hidden;}";
                        }
                    }

                    if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                        $keys                      = implode( ",", $this->field['compiler'] );
                        $this->parent->compilerCSS .= $keys . "{" . $style . '}';

                        if ( isset( $this->parent->args['async_typography'] ) && $this->parent->args['async_typography'] ) {
                            $key_string    = "";
                            $key_string_ie = "";

                            foreach ( $this->field['compiler'] as $value ) {
                                $key_string    .= ".wf-loading " . $value . ',';
                                $key_string_ie .= ".ie.wf-loading " . $value . ',';
                            }

                            $this->parent->compilerCSS .= $key_string . "{opacity: 0;}";
                            $this->parent->compilerCSS .= $key_string_ie . "{visibility: hidden;}";
                        }
                    }
                }

                $this->set_google_fonts( $font );
            }

            private function set_google_fonts( $font ) {
                // Google only stuff!
                if ( ! empty( $font['font-family'] ) && ! empty( $this->field['google'] ) && filter_var( $this->field['google'], FILTER_VALIDATE_BOOLEAN ) ) {

                    // Added standard font matching check to avoid output to Google fonts call - kp
                    // If no custom font array was supplied, then load it with default
                    // standard fonts.
                    if ( empty( $this->field['fonts'] ) ) {
                        $this->field['fonts'] = $this->std_fonts;
                    }

                    // Ensure the fonts array is NOT empty
                    if ( ! empty( $this->field['fonts'] ) ) {

                        //Make the font keys in the array lowercase, for case-insensitive matching
                        $lcFonts = array_change_key_case( $this->field['fonts'] );

                        // Rebuild font array with all keys stripped of spaces
                        $arr = array();
                        foreach ( $lcFonts as $key => $value ) {
                            $key         = str_replace( ', ', ',', $key );
                            $arr[ $key ] = $value;
                        }

                        $lcFonts = array_change_key_case( $this->field['custom_fonts'] );
                        foreach ( $lcFonts as $group => $fontArr ) {
                            foreach ( $fontArr as $key => $value ) {
                                $arr[ strtolower( $key ) ] = $key;
                            }
                        }

                        $lcFonts = $arr;

                        unset( $arr );

                        // lowercase chosen font for matching purposes
                        $lcFont = strtolower( $font['font-family'] );

                        // Remove spaces after commas in chosen font for mathcing purposes.
                        $lcFont = str_replace( ', ', ',', $lcFont );

                        // If the lower cased passed font-family is NOT found in the standard font array
                        // Then it's a Google font, so process it for output.
                        if ( ! array_key_exists( $lcFont, $lcFonts ) ) {
                            $family = $font['font-family'];

                            // Strip out spaces in font names and replace with with plus signs
                            // TODO?: This method doesn't respect spaces after commas, hence the reason
                            // for the std_font array keys having no spaces after commas.  This could be
                            // fixed with RegEx in the future.
                            $font['font-family'] = str_replace( ' ', '+', $font['font-family'] );

                            // Push data to parent typography variable.
                            if ( empty( $this->parent->typography[ $font['font-family'] ] ) ) {
                                $this->parent->typography[ $font['font-family'] ] = array();
                            }

                            if ( isset( $this->field['all-styles'] ) || isset( $this->field['all-subsets'] ) ) {
                                if ( ! isset( $font['font-options'] ) || empty( $font['font-options'] ) ) {
                                    $this->getGoogleArray();

                                    if ( isset( $this->parent->googleArray ) && ! empty( $this->parent->googleArray ) && isset( $this->parent->googleArray[ $family ] ) ) {
                                        $font['font-options'] = $this->parent->googleArray[ $family ];
                                    }
                                } else {
                                    $font['font-options'] = json_decode( $font['font-options'], true );
                                }
                                //print_r($font['font-options']);
                                //exit();
                            }

                            if ( isset( $font['font-options'] ) && ! empty( $font['font-options'] ) && isset( $this->field['all-styles'] ) && filter_var( $this->field['all-styles'], FILTER_VALIDATE_BOOLEAN ) ) {
                                if ( isset( $font['font-options'] ) && ! empty( $font['font-options']['variants'] ) ) {
                                    if ( ! isset( $this->parent->typography[ $font['font-family'] ]['all-styles'] ) || empty( $this->parent->typography[ $font['font-family'] ]['all-styles'] ) ) {
                                        $this->parent->typography[ $font['font-family'] ]['all-styles'] = array();
                                        foreach ( $font['font-options']['variants'] as $variant ) {
                                            $this->parent->typography[ $font['font-family'] ]['all-styles'][] = $variant['id'];
                                        }
                                    }
                                }
                            }

                            if ( isset( $font['font-options'] ) && ! empty( $font['font-options'] ) && isset( $this->field['all-subsets'] ) && $this->field['all-styles'] ) {
                                if ( isset( $font['font-options'] ) && ! empty( $font['font-options']['subsets'] ) ) {
                                    if ( ! isset( $this->parent->typography[ $font['font-family'] ]['all-subsets'] ) || empty( $this->parent->typography[ $font['font-family'] ]['all-subsets'] ) ) {
                                        $this->parent->typography[ $font['font-family'] ]['all-subsets'] = array();
                                        foreach ( $font['font-options']['subsets'] as $variant ) {
                                            $this->parent->typography[ $font['font-family'] ]['all-subsets'][] = $variant['id'];
                                        }
                                    }
                                }
                            }

                            if ( ! empty( $font['font-weight'] ) ) {
                                if ( empty( $this->parent->typography[ $font['font-family'] ]['font-weight'] ) || ! in_array( $font['font-weight'], $this->parent->typography[ $font['font-family'] ]['font-weight'] ) ) {
                                    $style = $font['font-weight'];
                                }

                                if ( ! empty( $font['font-style'] ) ) {
                                    $style .= $font['font-style'];
                                }

                                if ( empty( $this->parent->typography[ $font['font-family'] ]['font-style'] ) || ! in_array( $style, $this->parent->typography[ $font['font-family'] ]['font-style'] ) ) {
                                    $this->parent->typography[ $font['font-family'] ]['font-style'][] = $style;
                                }
                            }

                            if ( ! empty( $font['subsets'] ) ) {
                                if ( empty( $this->parent->typography[ $font['font-family'] ]['subset'] ) || ! in_array( $font['subsets'], $this->parent->typography[ $font['font-family'] ]['subset'] ) ) {
                                    $this->parent->typography[ $font['font-family'] ]['subset'][] = $font['subsets'];
                                }
                            }
                        }
                    }
                }
            }

            private function localize_std_fonts() {
                if ( false == $this->user_fonts ) {
                    if ( isset( $this->parent->fonts['std'] ) && ! empty( $this->parent->fonts['std'] ) ) {
                        return;
                    }

                    $this->parent->font_groups['std'] = array(
                        'text'     => esc_html__( 'Standard Fonts', 'redux-framework' ),
                        'children' => array(),
                    );

                    foreach ( $this->field['fonts'] as $font => $extra ) {
                        $this->parent->font_groups['std']['children'][] = array(
                            'id'          => $font,
                            'text'        => $font,
                            'data-google' => 'false',
                        );
                    }
                }

                if ( $this->field['custom_fonts'] !== false ) {
                    $this->field['custom_fonts'] = apply_filters( "redux/{$this->parent->args['opt_name']}/field/typography/custom_fonts", array() );

                    if ( ! empty( $this->field['custom_fonts'] ) ) {
                        foreach ( $this->field['custom_fonts'] as $group => $fonts ) {
                            $this->parent->font_groups['customfonts'] = array(
                                'text'     => $group,
                                'children' => array(),
                            );

                            foreach ( $fonts as $family => $v ) {
                                $this->parent->font_groups['customfonts']['children'][] = array(
                                    'id'          => $family,
                                    'text'        => $family,
                                    'data-google' => 'false',
                                );
                            }
                        }
                    }
                }

                //Typekit
                $typekit_fonts = apply_filters( "redux/{$this->parent->args['opt_name']}/field/typography/typekit_fonts", array() );

                if ( ! empty( $typekit_fonts ) ) {
                    foreach ( $typekit_fonts as $group => $fonts ) {
                        $this->parent->font_groups['typekitfonts'] = array(
                            'text'     => $group,
                            'children' => array(),
                        );

                        foreach ( $fonts as $family => $v ) {
                            $this->parent->font_groups['typekitfonts']['children'][] = array(
                                'text'        => $family,
                                'id'          => $family,
                                'data-google' => 'false',
                            );
                        }
                    }
                }
            }

            /**
             *   Construct the google array from the stored JSON/HTML
             */
            function getGoogleArray() {
                if ( ( isset( $this->parent->fonts['google'] ) && ! empty( $this->parent->fonts['google'] ) ) || isset( $this->parent->fonts['google'] ) && $this->parent->fonts['google'] == false ) {
                    return;
                }

                $fonts = Redux_Helpers::google_fonts_array( get_option( 'auto_update_redux_google_fonts', false ) );
                if ( empty( $fonts ) ) {
                    $gFile = dirname( __FILE__ ) . '/googlefonts.php';
                    $fonts = include $gFile;
                }

                if ( $fonts === true ) {
                    $this->parent->fonts['google'] = false;

                    return;
                }

                if ( isset( $fonts ) && ! empty( $fonts ) && is_array( $fonts ) && $fonts != false ) {
                    $this->parent->fonts['google'] = $fonts;
                    $this->parent->googleArray     = $fonts;

                    // optgroup
                    $this->parent->font_groups['google'] = array(
                        'text'     => esc_html__( 'Google Webfonts', 'redux-framework' ),
                        'children' => array(),
                    );

                    // options
                    foreach ( $this->parent->fonts['google'] as $font => $extra ) {
                        $this->parent->font_groups['google']['children'][] = array(
                            'id'          => $font,
                            'text'        => $font,
                            'data-google' => 'true'
                        );
                    }
                }
            }

            /**
             * get_subsets Function.
             * Clean up the Google Webfonts subsets to be human readable
             *
             * @since ReduxFramework 0.2.0
             */
            private function get_subsets( $var ) {
                $result = array();

                foreach ( $var as $v ) {
                    if ( strpos( $v, "-ext" ) ) {
                        $name = ucfirst( str_replace( "-ext", " Extended", $v ) );
                    } else {
                        $name = ucfirst( $v );
                    }

                    array_push( $result, array(
                        'id'   => $v,
                        'name' => $name
                    ) );
                }

                return array_filter( $result );
            }

            /**
             * get_variants Function.
             * Clean up the Google Webfonts variants to be human readable
             *
             * @since ReduxFramework 0.2.0
             */
            private function get_variants( $var ) {
                $result = array();
                $italic = array();

                foreach ( $var as $v ) {
                    $name = "";
                    if ( $v[0] == 1 ) {
                        $name = 'Ultra-Light 100';
                    } else if ( $v[0] == 2 ) {
                        $name = 'Light 200';
                    } else if ( $v[0] == 3 ) {
                        $name = 'Book 300';
                    } else if ( $v[0] == 4 || $v[0] == "r" || $v[0] == "i" ) {
                        $name = 'Normal 400';
                    } else if ( $v[0] == 5 ) {
                        $name = 'Medium 500';
                    } else if ( $v[0] == 6 ) {
                        $name = 'Semi-Bold 600';
                    } else if ( $v[0] == 7 ) {
                        $name = 'Bold 700';
                    } else if ( $v[0] == 8 ) {
                        $name = 'Extra-Bold 800';
                    } else if ( $v[0] == 9 ) {
                        $name = 'Ultra-Bold 900';
                    }

                    if ( $v == "regular" ) {
                        $v = "400";
                    }

                    if ( strpos( $v, "italic" ) || $v == "italic" ) {
                        $name .= " Italic";
                        $name = trim( $name );
                        if ( $v == "italic" ) {
                            $v = "400italic";
                        }
                        $italic[] = array(
                            'id'   => $v,
                            'name' => $name
                        );
                    } else {
                        $result[] = array(
                            'id'   => $v,
                            'name' => $name
                        );
                    }
                }

                foreach ( $italic as $item ) {
                    $result[] = $item;
                }

                return array_filter( $result );
            }

            public function google_fonts_update_ajax() {
                if ( ! isset( $_POST['nonce'] ) || ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'], 'redux_update_google_fonts' ) ) ) {
                    die( 'Security check' );
                }

                if ( $_POST['data'] == "automatic" ) {
                    update_option( 'auto_update_redux_google_fonts', true );
                }

                $fonts = Redux_Helpers::google_fonts_array( true );
                if ( ! empty( $fonts ) ) {
                    echo json_encode( array( 'status' => 'success', 'fonts' => $fonts ) );
                } else {
                    echo json_encode( array( 'status' => 'error' ) );
                }

                die();
            }
        }
    }