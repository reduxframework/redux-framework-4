<?php
/*
 * Plugin Name:       StarterBlocks
 * Plugin URI:        https://starterblocks.io/
 * Description:       StarterBlocks lets you build full pages with Gutenberg. Import nearly 1,000+ full page layouts and designs! True page builder experience.
 * Version: 		  1.0.7
 * Author:            Redux.io, Dovy Paukstys
 * Author URI:        https://redux.io/
 * Text Domain:       starterblocks
 * Requires at least: 5.0
 * Tested up to:      5.3
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

require_once __DIR__ . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( function_exists( 'starterblocks_fs' ) ) {
    sta_fs()->set_basename( true, __FILE__ );
} else {
    if ( ! function_exists( 'starterblocks_fs' ) ) {

        // Create a helper function for easy SDK access.
        function starterblocks_fs() {
            global $starterblocks_fs;

            if ( ! isset( $starterblocks_fs ) ) {
                // Activate multisite network integration.
//				if ( ! defined( 'WP_FS__PRODUCT_5632_MULTISITE' ) ) {
//					define( 'WP_FS__PRODUCT_5632_MULTISITE', true );
//				}

                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/core/freemius/start.php';

                $starterblocks_fs = fs_dynamic_init(
                    array(
                        'id'                  => '5632',
                        'slug'                => 'starterblocks',
                        'type'                => 'plugin',
                        'public_key'          => 'pk_d1cff5ec542f0e8f2446afbcfca5f',
                        'is_premium'          => false,
                        // If your plugin is a serviceware, set this option to false.
                        'has_premium_version' => false,
                        'has_addons'          => false,
                        'has_paid_plans'      => true,
                        'trial'               => array(
                            'days'               => 14,
                            'is_require_payment' => true,
                        ),
                        'has_affiliation'     => 'selected',
                        'menu'                => array(
                            'slug'       => 'starterblocks',
                            'first-path' => 'admin.php?page=starterblocks',
                            'account'    => true,
                            'pricing'    => true,
                            'contact'    => true,
                            'support'    => true,
                        ),
                    )
                );
            }

            return $starterblocks_fs;
        }

        // Init Freemius.
        starterblocks_fs();
        // Signal that SDK was initiated.
        do_action( 'starterblocks_fs_loaded' );

//
//		$fs_options = FS_Options::instance( WP_FS__ACCOUNTS_OPTION_NAME, true );
//		$modules = fs_get_entities( $fs_options->get_option( WP_FS__MODULE_TYPE_PLUGIN . 's' ), FS_Plugin::get_class_name() );
//		print_r($modules);
////			$fs_options = FS_Options::instance( WP_FS__ACCOUNTS_OPTION_NAME, true );
////		print_r($fs_options->sites);
//
//		exit();

    }

// Define Version
    define( 'STARTERBLOCKS_VERSION', '1.0.7' );

// Define File DIR
    define( 'STARTERBLOCKS_FILE', __FILE__ );

// Define Dir URL
    define( 'STARTERBLOCKS_DIR_URL', plugin_dir_url( __FILE__ ) );

// Define Physical Path
    define( 'STARTERBLOCKS_DIR_PATH', plugin_dir_path( __FILE__ ) );

// Version Check & Include Core
    if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
        add_action( 'admin_notices', 'StarterBlocks\Notices\Notices::php_error_notice' ); // PHP Version Check
    } elseif ( ! version_compare( get_bloginfo( 'version' ), '4.5', '>=' ) ) {
        add_action(
            'admin_notices', 'StarterBlocks\Notices\Notices::wordpress_error_notice'
        ); // WordPress Version Check
    } else {
//        $class = 'StarterBlocks\Init\Init';
        new StarterBlocks\Init();
    }
}

