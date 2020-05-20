<?php

namespace StarterBlocks;

use StarterBlocks;


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Init {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'load' ) );
//        register_activation_hook( __FILE__, array( $this, 'option_data' ) );
        // Editor Load
        add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );
        // Admin Load
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
    }

    // Init Options Data Init
    public function option_data() {
        $option_data = array( 'css_save_as' => 'wp_head' );
        if ( ! get_option( 'starterblocks_options' ) ) {
            update_option( 'starterblocks_options', $option_data );
        }
    }

    public static function load() {
        new StarterBlocks\API();
        new StarterBlocks\Templates();
        new StarterBlocks\Welcome();
    }

    /**
     * Load Editor Styles and Scripts
     *
     * @since 1.0.0
     */
    public function editor_assets() {

        wp_enqueue_script(
            'starterblocks-js',
            plugins_url( 'assets/js/starterblocks.dev.js', STARTERBLOCKS_FILE ),
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
            STARTERBLOCKS_VERSION,
            true
        );

        wp_set_script_translations( 'starterblocks-js', 'starterblocks' );

        // Backend editor scripts: common vendor files.
        wp_enqueue_script(
            'starterblocks-js-vendor',
            plugins_url( 'assets/js/vendor.dev.js', STARTERBLOCKS_FILE ),
            array(),
            STARTERBLOCKS_VERSION
        );
        global $starterblocks_fs;
        $global_vars = array(
            'i18n'              => 'starterblocks',
            'plugin'            => STARTERBLOCKS_DIR_URL,
            'mokama'            => starterblocks_fs()->can_use_premium_code(),
            'icon'              => file_get_contents( STARTERBLOCKS_DIR_URL . 'assets/img/logo.svg' ),
            'version'           => STARTERBLOCKS_VERSION,
            'supported_plugins' => [], // Load the supported plugins,

        );

        if ( ! $global_vars['mokama'] ) {
            $global_vars['u'] = $starterblocks_fs->get_upgrade_url(
                ) . '&utm_source=plugin&utm_medium=modal&utm_campaign=template';
        }

        wp_localize_script(
            'starterblocks-js',
            'starterblocks',
            $global_vars
        );

    }

    /**
     * Admin Style & Script
     *
     * @since 1.0.0
     */
    public function admin_assets() {
        wp_enqueue_style(
            'starterblocks-bundle', STARTERBLOCKS_DIR_URL . 'assets/css/admin.min.css', false, STARTERBLOCKS_VERSION
        );
    }
}
