<?php

namespace StarterBlocks;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


class Notices {

    // PHP Error Notice
    public static function php_error_notice() {
        $message      = sprintf(
            esc_html__( 'StarterBlocks requires PHP version %s or more.', 'starterblocks' ), '5.4'
        );
        $html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }

    // Wordpress Error Notice
    public static function wordpress_error_notice() {
        $message      = sprintf(
            esc_html__( 'StarterBlocks requires WordPress version %s or more.', 'starterblocks' ), '4.7'
        );
        $html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
}

