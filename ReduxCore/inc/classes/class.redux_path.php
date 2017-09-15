<?php

    /**
     * Loader for Redux Includes
     *
     * @since    4.0.0
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'Redux_Path' ) ) {

        class Redux_Path {
            public static function init() {

            }

            public static function get_path( $relative_path ) {
                $path = ReduxCore::$_path . $relative_path;

                if ( ReduxCore::$_pro_loaded ) {
                    $pro_path = ReduxPro::$_dir . $relative_path;

                    if ( file_exists( $pro_path ) ) {
                        $path = $pro_path;
                    }
                }

                return $path;
            }

            public static function require_class( $relative_path ) {
                $path = self::get_path( $relative_path );

                if ( file_exists( $path ) ) {
                    require_once( $path );
                }
            }
        }

        Redux_Path::init();
    }