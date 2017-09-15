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
 * @subpackage  Field_slides
 * @author      Luciano "WebCaos" Ubertini
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( !class_exists ( 'ReduxFramework_slides' ) ) {

    /**
     * Main ReduxFramework_slides class
     *
     * @since       1.0.0
     */
    class ReduxFramework_slides extends Redux_Field {

        public function set_defaults() {
            $defaults = array(
                'show' => array(
                    'title' => true,
                    'description' => true,
                    'url' => true,
                ),
                'content_title' => __ ( 'Slide', 'redux-framework' )
            );

            $this->field = wp_parse_args ( $this->field, $defaults );
        }
        
        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render () {
            echo '<div class="redux-slides-accordion" data-new-content-title="' . esc_attr ( sprintf ( __ ( 'New %s', 'redux-framework' ), $this->field[ 'content_title' ] ) ) . '">';

            $x = 0;

            $multi = ( isset ( $this->field[ 'multi' ] ) && $this->field[ 'multi' ] ) ? ' multiple="multiple"' : "";

            if ( isset ( $this->value ) && is_array ( $this->value ) && !empty ( $this->value ) ) {

                $slides = $this->value;

                foreach ( $slides as $slide ) {
                    if ( empty ( $slide ) ) {
                        continue;
                    }

                    $defaults = array(
                        'title' => '',
                        'description' => '',
                        'sort' => '',
                        'url' => '',
                        'image' => '',
                        'thumb' => '',
                        'attachment_id' => '',
                        'height' => '',
                        'width' => '',
                        'select' => array(),
                    );
                    
                    $slide = wp_parse_args ( $slide, $defaults );

                    if ( empty ( $slide[ 'thumb' ] ) && !empty ( $slide[ 'attachment_id' ] ) ) {
                        $img = wp_get_attachment_image_src ( $slide[ 'attachment_id' ], 'full' );
                        $slide[ 'image' ] = $img[ 0 ];
                        $slide[ 'width' ] = $img[ 1 ];
                        $slide[ 'height' ] = $img[ 2 ];
                    }

                    echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . esc_attr($this->field[ 'id' ]) . '"><h3><span class="redux-slides-header">' . esc_html($slide[ 'title' ]) . '</span></h3><div>';

                    $hide = '';
                    if ( empty ( $slide[ 'image' ] ) ) {
                        $hide = ' hide';
                    }


                    echo '<div class="screenshot' . $hide . '">';
                    echo '<a class="of-uploaded-image" href="' . esc_url($slide[ 'image' ]) . '">';
                    echo '<img class="redux-slides-image" id="image_image_id_' . $x . '" src="' . esc_url($slide[ 'thumb' ]) . '" alt="" target="_blank" rel="external" />';
                    echo '</a>';
                    echo '</div>';

                    echo '<div class="redux_slides_add_remove">';

                    echo '<span class="button media_upload_button" id="add_' . esc_attr($x) . '">' . __ ( 'Upload', 'redux-framework' ) . '</span>';

                    $hide = '';
                    if ( empty ( $slide[ 'image' ] ) || $slide[ 'image' ] == '' ) {
                        $hide = ' hide';
                    }

                    echo '<span class="button remove-image' . $hide . '" id="reset_' . esc_attr($x) . '" rel="' . esc_attr($slide[ 'attachment_id' ]) . '">' . __ ( 'Remove', 'redux-framework' ) . '</span>';

                    echo '</div>' . "\n";

                    echo '<ul id="' . esc_attr($this->field[ 'id' ]) . '-ul" class="redux-slides-list">';

                    if ( $this->field[ 'show' ][ 'title' ] ) {
                        $title_type = "text";
                    } else {
                        $title_type = "hidden";
                    }

                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'title' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'title' ] ) : __ ( 'Title', 'redux-framework' );
                    echo '<li><input type="' . $title_type . '" id="' . esc_attr($this->field[ 'id' ]) . '-title_' . esc_attr($x) . '" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][title]' . $this->field['name_suffix']) . '" value="' . esc_attr ( $slide[ 'title' ] ) . '" placeholder="' . esc_attr($placeholder) . '" class="full-text slide-title" /></li>';

                    if ( $this->field[ 'show' ][ 'description' ] ) {
                        $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'description' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'description' ] ) : __ ( 'Description', 'redux-framework' );
                        echo '<li><textarea name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][description]' . $this->field['name_suffix']) . '" id="' .esc_attr( $this->field[ 'id' ]) . '-description_' . esc_attr($x) . '" placeholder="' . esc_attr($placeholder) . '" class="large-text" rows="6">' . esc_textarea( $slide[ 'description' ] ) . '</textarea></li>';
                    }

                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'url' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'url' ] ) : __ ( 'URL', 'redux-framework' );
                    if ( $this->field[ 'show' ][ 'url' ] ) {
                        $url_type = "text";
                    } else {
                        $url_type = "hidden";
                    }

                echo '<li><input type="' . $url_type . '" id="' . esc_attr($this->field[ 'id' ] . '-url_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][url]' . $this->field['name_suffix']) .'" value="' . esc_attr ( $slide[ 'url' ] ) . '" class="full-text" placeholder="' . esc_attr($placeholder) . '" /></li>';
                    echo '<li><input type="hidden" class="slide-sort" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][sort]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ]) . '-sort_' . esc_attr($x) . '" value="' . $slide[ 'sort' ] . '" />';
                    echo '<li><input type="hidden" class="upload-id" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][attachment_id]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ]) . '-image_id_' . esc_attr($x) . '" value="' . esc_attr($slide[ 'attachment_id' ]) . '" />';
                    echo '<input type="hidden" class="upload-thumbnail" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][thumb]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ]) . '-thumb_url_' . esc_attr($x) . '" value="' . esc_attr($slide[ 'thumb' ]) . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][image]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ]) . '-image_url_' . esc_attr($x) . '" value="' . esc_attr($slide[ 'image' ]) . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload-height" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][height]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ]) . '-image_height_' . esc_attr($x) . '" value="' . esc_attr($slide[ 'height' ]) . '" />';
                    echo '<input type="hidden" class="upload-width" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][width]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ]) . '-image_width_' . esc_attr($x) . '" value="' . esc_attr($slide[ 'width' ]) . '" /></li>';
                    echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . __ ( 'Delete', 'redux-framework' ) . '</a></li>';
                    echo '</ul></div></fieldset></div>';
                    $x ++;
                }
            }

            if ( $x == 0 ) {
                echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . esc_attr($this->field[ 'id' ]) . '"><h3><span class="redux-slides-header">' . sprintf ( __ ( 'New', 'redux-framework') . ' %s', esc_attr($this->field[ 'content_title' ]) ) . '</span></h3><div>';

                $hide = ' hide';

                echo '<div class="screenshot' . $hide . '">';
                echo '<a class="of-uploaded-image" href="">';
                echo '<img class="redux-slides-image" id="image_image_id_' . esc_attr($x) . '" src="" alt="" target="_blank" rel="external" />';
                echo '</a>';
                echo '</div>';

                //Upload controls DIV
                echo '<div class="upload_button_div">';

                //If the user has WP3.5+ show upload/remove button
                echo '<span class="button media_upload_button" id="add_' . esc_attr($x) . '">' . __ ( 'Upload', 'redux-framework' ) . '</span>';

                echo '<span class="button remove-image' . $hide . '" id="reset_' . esc_attr($x) . '" rel="' . esc_attr($this->parent->args[ 'opt_name' ] . '[' . $this->field[ 'id' ]) . '][attachment_id]">' . __ ( 'Remove', 'redux-framework' ) . '</span>';

                echo '</div>' . "\n";

                echo '<ul id="' . esc_attr($this->field[ 'id' ]) . '-ul" class="redux-slides-list">';
                
                if ( $this->field[ 'show' ][ 'title' ] ) {
                    $title_type = "text";
                } else {
                    $title_type = "hidden";
                }
                
                $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'title' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'title' ] ) : __ ( 'Title', 'redux-framework' );
                
                echo '<li><input type="' . $title_type . '" id="' . esc_attr($this->field[ 'id' ] . '-title_' . $x) . '" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][title]' . $this->field['name_suffix']) .'" value="" placeholder="' . esc_attr($placeholder) . '" class="full-text slide-title" /></li>';

                if ( $this->field[ 'show' ][ 'description' ] ) {
                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'description' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'description' ] ) : __ ( 'Description', 'redux-framework' );
                    
                    echo '<li><textarea name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][description]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ] . '-description_' . $x) . '" placeholder="' . esc_attr($placeholder) . '" class="large-text" rows="6"></textarea></li>';
                }
                
                $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'url' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'url' ] ) : __ ( 'URL', 'redux-framework' );
                
                if ( $this->field[ 'show' ][ 'url' ] ) {
                    $url_type = "text";
                } else {
                    $url_type = "hidden";
                }
                
                echo '<li><input type="' . $url_type . '" id="' . esc_attr($this->field[ 'id' ] . '-url_' . $x) . '" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][url]' . $this->field['name_suffix']) .'" value="" class="full-text" placeholder="' . esc_attr($placeholder) . '" /></li>';
                echo '<li><input type="hidden" class="slide-sort" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][sort]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ] . '-sort_' . $x) . '" value="' . esc_attr($x) . '" />';
                echo '<li><input type="hidden" class="upload-id" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][attachment_id]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ] . '-image_id_' . $x) . '" value="" />';
                echo '<input type="hidden" class="upload" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][image]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ] . '-image_url_' . $x) . '" value="" readonly="readonly" />';
                echo '<input type="hidden" class="upload-height" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][height]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ] . '-image_height_' . $x) . '" value="" />';
                echo '<input type="hidden" class="upload-width" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][width]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ] . '-image_width_' . $x) . '" value="" /></li>';
                echo '<input type="hidden" class="upload-thumbnail" name="' . esc_attr($this->field[ 'name' ] . '[' . $x . '][thumb]' . $this->field['name_suffix']) .'" id="' . esc_attr($this->field[ 'id' ] . '-thumb_url_' . $x) . '" value="" /></li>';
                echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . __ ( 'Delete', 'redux-framework' ) . '</a></li>';
                echo '</ul></div></fieldset></div>';
            }
            echo '</div><a href="javascript:void(0);" class="button redux-slides-add button-primary" rel-id="' . esc_attr($this->field[ 'id' ]) . '-ul" rel-name="' . esc_attr($this->field[ 'name' ] . '[title][]' . $this->field['name_suffix']) .'">' . sprintf ( __ ( 'Add', 'redux-framework') . ' %s', esc_html($this->field[ 'content_title' ] ) ) . '</a><br/>';
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue () {
            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            } else {
                wp_enqueue_script( 'media-upload' );
            }
                
            if ($this->parent->args['dev_mode']){
                wp_enqueue_style ('redux-field-media-css');
                
                wp_enqueue_style (
                    'redux-field-slides-css', 
                    ReduxCore::$_url . 'inc/fields/slides/field_slides.css', 
                    array(),
                    time (), 
                    'all'
                );
            }
            
            wp_enqueue_script(
                'redux-field-media-js',
                ReduxCore::$_url . 'assets/js/media/media' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                ReduxCore::$_version,
                true
            );

            wp_enqueue_script (
                'redux-field-slides-js', 
                ReduxCore::$_url . 'inc/fields/slides/field_slides' . Redux_Functions::isMin () . '.js', 
                array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'jquery-ui-sortable', 'redux-field-media-js' ),
                ReduxCore::$_version, 
                true
            );
        }
    }
}