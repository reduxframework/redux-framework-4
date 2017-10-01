<?php

    /**
     * Serves as a launcher for the select field's ajax feature
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'Redux_AJAX_Typography' ) ) {

        class Redux_AJAX_Typography extends Redux_Class {

            public function __construct( $parent ) {
                parent::__construct( $parent );
                add_action( 'wp_ajax_redux_update_google_fonts', array( $this, 'google_fonts_update' ) );
            }

            public function google_fonts_update() {
                ini_set( 'display_errors', 1 );
                ini_set( 'display_startup_errors', 1 );
                error_reporting( E_ALL );

                $field_class = 'ReduxFramework_typography';

                if ( ! class_exists( $field_class ) ) {
                    $dir = str_replace( '/classes', '', Redux_Helpers::cleanFilePath( dirname( __FILE__ ) ) );

                    $class_file = apply_filters( 'redux-typeclass-load', "{$dir}/fields/typography/field_typography.php", $field_class );
                    if ( $class_file ) {
                        /** @noinspection PhpIncludeInspection */
                        require_once( $class_file );
                    }
                }

                if ( class_exists( $field_class ) && method_exists( $field_class, 'google_fonts_update_ajax' ) ) {
                    $f = new $field_class( array(), '', $this->parent );

                    return $f->google_fonts_update_ajax();
                }

                die();
            }
        }
    }