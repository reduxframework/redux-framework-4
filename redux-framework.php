<?php
/**
 * The Redux Framework Plugin
 *
 * A simple, truly extensible and fully responsive options framework
 * for WordPress themes and plugins. Developed with WordPress coding
 * standards and PHP best practices in mind.
 *
 * Plugin Name:     Redux Framework
 * Plugin URI:      http://wordpress.org/plugins/redux-framework
 * Github URI:      reduxframework/redux-framework-4
 * Description:     The most powerful and widely used WordPress interface builder and framework.
 * Author:          Team Redux
 * Author URI:      http://reduxframework.com
 * Version:         4.0.3
 * Text Domain:     redux-framework
 * License:         GPLv3 or later
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Provides:        ReduxFramework
 *
 * @package         ReduxFramework
 * @author          Team Redux (Dovy Paukstys <dovy@reduxframework.com> and Kevin Provance <kevin@reduxframework.com>)
 * @license         GNU General Public License, version 3
 * @copyright       2012-2019 Redux.io
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Require the main plugin class.
require_once plugin_dir_path( __FILE__ ) . 'class-redux-framework-plugin.php';

// Register hooks that are fired when the plugin is activated and deactivated, respectively.
register_activation_hook( __FILE__, array( 'Redux_Framework_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Redux_Framework_Plugin', 'deactivate' ) );

// Get plugin instance.
Redux_Framework_Plugin::instance();
