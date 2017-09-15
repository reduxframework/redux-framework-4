<?php

    /**
     * Serves as a launcher for the select field's ajax feature
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'Redux_AJAX_Select2' ) ) {

        class Redux_AJAX_Select2 extends Redux_Class {

            public function __construct( $parent ) {
                parent::__construct( $parent );
                add_action( "wp_ajax_redux_{$this->args['opt_name']}_select2", array( $this, "ajax" ) );
            }

            public function ajax() {

                if ( ! wp_verify_nonce( $_REQUEST['nonce'], $_REQUEST['action'] ) ) {
                    echo json_encode( array(
                        'error' => esc_html__( 'Invalid security credential.  Please reload the page and try again.', 'redux-framework' ),
                    ) );
                    die();
                }

                if ( ! Redux_Helpers::current_user_can( $this->parent->args['page_permissions'] ) ) {
                    echo json_encode( array(
                        'error' => esc_html__( 'Invalid user capability.  Please reload the page and try again.', 'redux-framework' ),
                    ) );
                    die();
                }

                $field_class = 'ReduxFramework_select';

                if ( ! class_exists( $field_class ) ) {
                    $dir = str_replace('/classes','',Redux_Helpers::cleanFilePath( dirname( __FILE__ ) ));

                    $class_file = apply_filters( 'redux-typeclass-load', "{$dir}/fields/select/field_select.php", $field_class );
                    if ( $class_file ) {
                        /** @noinspection PhpIncludeInspection */
                        require_once( $class_file );
                    }
                }

                if ( class_exists( $field_class ) && method_exists( $field_class, 'ajax_callback' ) ) {
                    $f = new $field_class( array(), '', $this->parent );
                    $f->ajax_callback();
                }

                die ();
            }
        }
    }