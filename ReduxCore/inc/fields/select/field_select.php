<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_select' ) ) {

    class ReduxFramework_select extends Redux_Field {

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        public function render() {
            $sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : "";

            if ( ! empty( $sortable ) ) { // Dummy proofing  :P
                $this->field['multi'] = true;
            }

            if ( ! empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
                if ( empty( $this->field['args'] ) ) {
                    $this->field['args'] = array();
                }

                if ( $this->field['data'] == "elusive-icons" || $this->field['data'] == "elusive-icon" || $this->field['data'] == "elusive" ) {
                    $icons_file = ReduxCore::$_dir . 'inc/fields/select/elusive-icons.php';

                    /**
                     * filter 'redux-font-icons-file}'
                     *
                     * @param  array $icon_file File for the icons
                     */
                    $icons_file = apply_filters( 'redux-font-icons-file', $icons_file );

                    /**
                     * filter 'redux/{opt_name}/field/font/icons/file'
                     *
                     * @param  array $icon_file File for the icons
                     */
                    $icons_file = apply_filters( "redux/{$this->parent->args['opt_name']}/field/font/icons/file", $icons_file );

                    if ( file_exists( $icons_file ) ) {
                        require_once $icons_file;
                    }
                }

                $this->field['options'] = $this->parent->wordpress_data->get( $this->field['data'], $this->field['args'] );
            }

            if ( ! empty( $this->field['data'] ) && ( $this->field['data'] == "elusive-icons" || $this->field['data'] == "elusive-icon" || $this->field['data'] == "elusive" ) ) {
                $this->field['class'] .= " font-icons";
            }

            if ( ! empty( $this->field['options'] ) || ( isset( $this->field['ajax'] ) && $this->field['ajax'] ) ) {
                $multi = ( isset( $this->field['multi'] ) && $this->field['multi'] ) ? ' multiple="multiple"' : "";

                if ( ! empty( $this->field['width'] ) ) {
                    $width = ' style="' . esc_attr($this->field['width']) . '"';
                } else {
                    $width = ' style="width: 40%;"';
                }

                $nameBrackets = "";
                if ( ! empty( $multi ) ) {
                    $nameBrackets = "[]";
                }

                $placeholder = ( isset( $this->field['placeholder'] ) ) ? esc_attr( $this->field['placeholder'] ) : esc_html__( 'Select an item', 'redux-framework' );

                $select2_width = 'resolve';
                if ($multi != '') {
                    $select2_width = '100%';
                }

                $select2_default = array(
                    'width'         => $select2_width,
                    'allowClear'    => true,
                    'theme'         => 'classic'
                );
                if ( isset( $this->field['ajax'] ) && $this->field['ajax'] ) {
                    $select2_default['ajax'] = true;
                    //$select2_default['escapeMarkup']       = "<div class='select2-result-repository clearfix'><div class='select2-result-repository__avatar'><img src='\" + repo.owner.avatar_url + \"' /></div><div class='select2-result-repository__meta'><div class='select2-result-repository__title'>\" + repo.full_name + \"</div>";
                    $select2_default['minimumInputLength'] = 1;
                    $select2_default['ajax_url']           = "?action=redux_{$this->parent->args['opt_name']}_select2";
                    $select2_default['nonce'] = wp_create_nonce( "redux_{$this->parent->args['opt_name']}_select2" );
                    $select2_default['data'] = $this->field['data'];
                }

                if ( isset( $this->field['select2'] ) ) {
                    $this->field['select2'] = wp_parse_args($this->field['select2'], $select2_default);
                } else {
                    $this->field['select2'] = $select2_default;
                }

                $this->field['select2'] = Redux_Functions::sanitize_camel_case_array_keys($this->field['select2']);

                $select2_data = Redux_Functions::create_data_string($this->field['select2']);

                if ( isset( $this->field['multi'] ) && $this->field['multi'] && isset( $this->field['sortable'] ) && $this->field['sortable'] && ! empty( $this->value ) && is_array( $this->value ) ) {
                    $origOption             = $this->field['options'];
                    $this->field['options'] = array();

                    foreach ( $this->value as $value ) {
                        $this->field['options'][ $value ] = $origOption[ $value ];
                    }

                    if ( count( $this->field['options'] ) < count( $origOption ) ) {
                        foreach ( $origOption as $key => $value ) {
                            if ( ! in_array( $key, $this->field['options'] ) ) {
                                $this->field['options'][ $key ] = $value;
                            }
                        }
                    }
                }

                $sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : "";

                echo '<select ' . $multi . ' id="' . esc_attr($this->field['id']) . '-select" data-placeholder="' . esc_attr($placeholder) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . $nameBrackets . '" class="redux-select-item ' . esc_attr($this->field['class']) . $sortable . '"' . $width . ' rows="6"' . esc_attr($select2_data) . '>';
                echo '<option></option>';

                foreach ( $this->field['options'] as $k => $v ) {

                    if (is_array($v)) {
                        echo '<optgroup label="' . esc_attr($k) . '">';

                        foreach($v as $opt => $val) {
                            $this->make_option($opt, $val, $k);
                        }

                        echo '</optgroup>';

                        continue;
                    }

                    $this->make_option($k, $v);
                }
                //foreach

                echo '</select>';
            } else {
                echo '<strong>' . esc_html__( 'No items of this type were found.', 'redux-framework' ) . '</strong>';
            }
        } //function

        private function make_option($id, $value, $group_name = '') {
            if ( is_array( $this->value ) ) {
                $selected = ( is_array( $this->value ) && in_array( $id, $this->value ) ) ? ' selected="selected"' : '';
            } else {
                $selected = selected( $this->value, $id, false );
            }

            echo '<option value="' . esc_attr($id) . '"' . $selected . '>' . esc_attr($value) . '</option>';
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 1.0.0
         */
        public function enqueue() {


            wp_enqueue_style( 'select2-css' );
            if (isset($this->field['sortable']) && $this->field['sortable']) {
                wp_enqueue_script('jquery-ui-sortable');
            }

            wp_enqueue_script(
                'redux-field-select-js',
                ReduxCore::$_url . 'inc/fields/select/field_select' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'select2-js', 'redux-js' ),
                ReduxCore::$_version,
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-select-css',
                    ReduxCore::$_url . 'inc/fields/select/field_select.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }

        function ajax_callback() {

            $options = $this->parent->wordpress_data->get( $_REQUEST['data'] );

            echo json_encode($options);
          //
          //  print_r($_REQUEST);
          //  print_r($options);
          //  //return $options;
          //
          //  echo "here2";
          //  exit();
          //
          //if (!isset($this->field['ajax'])) {
          //  $this->field['ajax'] = true;
          //}
          //print_r($this->field);
          //
          //print_r($_REQUEST);


          die();
        }
    }
}