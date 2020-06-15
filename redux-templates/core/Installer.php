<?php // phpcs:ignore WordPress.Files.FileName

/**
 * Installer class which installs and/or activates block plugins.
 *
 * @since 4.0.0
 * @package Redux Framework
 */

namespace ReduxTemplates;

use ReduxTemplates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/misc.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

class InstallerMuter extends \WP_Upgrader_Skin {
	public function feedback( $string, ...$args ) {
		/* no output */ }
}

class Installer {

	public static function run( $slug ) {
		$pluginDir = WP_PLUGIN_DIR . '/' . $slug;

		/*
		 * Don't try installing plugins that already exist (wastes time downloading files that
		 * won't be used
		 */

		$status = array();
		if ( ! is_dir( $pluginDir ) ) {

			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $slug,
					'fields' => array(
						'short_description' => false,
						'sections'          => false,
						'requires'          => false,
						'rating'            => false,
						'ratings'           => false,
						'downloaded'        => false,
						'last_updated'      => false,
						'added'             => false,
						'tags'              => false,
						'compatibility'     => false,
						'homepage'          => false,
						'donate_link'       => false,
					),
				)
			);

			ob_start();

			$skin     = new InstallerMuter( array( 'api' => $api ) );
			$upgrader = new \Plugin_Upgrader( $skin );
			$install  = $upgrader->install( $api->download_link );

			ob_end_clean();

			if ( $install !== true ) {
				$status['error'] = 'Install process failed for ' . $slug . '.';

				if ( ! empty( $install ) ) {
					ob_start();
					\var_dump( $install );
					$result = ob_get_clean();

					$status['var_dump'] = $result;
				} else {
					$status['error'] .= ' ' . $upgrader->skin->options['api']->errors['plugins_api_failed'][0];
				}

				return $status;
			}
			$status['install'] = 'success';
		}

		/*
		 * The install results don't indicate what the main plugin file is, so we just try to
		 * activate based on the slug. It may fail, in which case the plugin will have to be activated
		 * manually from the admin screen.
		 */
		$pluginPath  = false;
		$pluginCheck = false;
		if ( file_exists( $pluginDir . '/' . $slug . '.php' ) ) {
			$pluginPath  = $pluginDir . '/' . $slug . '.php';
			$pluginCheck = $slug . '/' . $slug . '.php';
		} elseif ( file_exists( $pluginDir . '/plugin.php' ) ) {
			$pluginPath  = $pluginDir . '/' . $slug . '.php';
			$pluginCheck = $slug . '/plugin.php';
		} else {
			$split        = explode( '-', $slug );
			$new_filename = '';
			foreach ( $split as $s ) {
				if ( ! empty( $s ) ) {
					$new_filename .= $s[0];
				}
			}
			$pluginPath  = $pluginDir . '/' . $new_filename . '.php';
			$pluginCheck = $slug . '/' . $new_filename . '.php';
		}

		if ( ! empty( $pluginPath ) ) {
			if ( is_plugin_active( $pluginCheck ) && ! isset( $status['install'] ) ) {
				$status['activate'] = 'active';
			} else {
				activate_plugin( $pluginCheck );
				$status['activate'] = 'success';
			}
		} else {
			$status['error'] = 'Error: Plugin file not activated (' . $slug . '). The plugin file could not be found.';
		}

		return $status;

	}
}
