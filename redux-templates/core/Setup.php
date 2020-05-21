<?php

namespace ReduxTemplates;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


class Setup {
    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    // Constructor
    public function __construct() {
        register_activation_hook( __FILE__, array( $this, 'option_data' ) );
    }

    // Init Options Data Init
    public function option_data() {
        $option_data = array( 'css_save_as' => 'wp_head' );
        if ( ! get_option( 'redux-templates_options' ) ) {
            update_option( 'redux-templates_options', $option_data );
        }
    }

    // PHP Error Notice
    public static function php_error_notice() {
        $message      = sprintf(
            esc_html__( 'ReduxTemplates requires PHP version %s or more.', 'redux-templates' ), '5.4'
        );
        $html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }

    // Wordpress Error Notice
    public static function wordpress_error_notice() {
        $message      = sprintf(
            esc_html__( 'ReduxTemplates requires WordPress version %s or more.', 'redux-templates' ), '4.7'
        );
        $html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
}
