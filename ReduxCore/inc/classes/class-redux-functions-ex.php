<?php
/**
 * Redux Framework Private Extended Functions Container Class
 *
 * @class       Redux_Functions_Ex
 * @package     Redux_Framework/Classes
 * @since       3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'Redux_Functions_Ex', false ) ) {

	/**
	 * Redux Functions Class
	 * Class of useful functions that can/should be shared among all Redux files.
	 *
	 * @since       3.0.0
	 */
	class Redux_Functions_Ex {

		/**
		 * Records calling function.
		 *
		 * @param string $opt_name Panel opt_name.
		 */
		public static function record_caller( $opt_name = '' ) {
			global $pagenow;

			if ( ! ( 'tools.php' === $pagenow && ! empty( $_GET['page'] ) && ( 'redux-framework' === $_GET['page'] || 'health-check' === $_GET['page'] ) ) ) {
				return;
			}

			$caller = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1]['file'];
			if ( ! empty( $caller ) && ! empty( $opt_name ) && class_exists( 'Redux_Core' ) ) {
				if ( ! isset( Redux_Core::$_callers[ $opt_name ] ) ) {
					Redux_Core::$_callers[ $opt_name ] = array();
				}

				if ( strpos( $caller, 'inc/classes/class-redux-' ) !== false ) {
					return;
				}

				if ( ! in_array( $caller, Redux_Core::$_callers[ $opt_name ], true ) ) {
					Redux_Core::$_callers[ $opt_name ][] = $caller;
				}
				if ( ! empty( self::$args[ $opt_name ]['callers'] ) && ! in_array( $caller, self::$args[ $opt_name ]['callers'], true ) ) {
					self::$args[ $opt_name ]['callers'][] = $caller;
				}
			}
		}

		/**
		 * What is this for?
		 *
		 * @var array
		 */
		public static $args;

		/**
		 * Normalize path.
		 *
		 * @param string $path Path to normalize.
		 *
		 * @return mixed|null|string|string[]
		 */
		public static function wp_normalize_path( $path = '' ) {
			if ( function_exists( 'wp_normalize_path' ) ) {
				$path = wp_normalize_path( $path );
			} else {
				// Shim for pre WP 3.9.
				$path = str_replace( '\\', '/', $path );
				$path = preg_replace( '|(?<=.)/+|', '/', $path );
				if ( ':' === substr( $path, 1, 1 ) ) {
					$path = ucfirst( $path );
				}
			}

			return $path;
		}

		/**
		 * Action to add generator tag to page HEAD.
		 */
		public static function generator() {
			add_action( 'wp_head', array( 'Redux_Functions_Ex', 'meta_tag' ) );
		}


		/**
		 * Callback for wp_head hook to add meta tag.
		 */
		public static function meta_tag() {
			echo '<meta name="framework" content="Redux ' . esc_html( Redux_Core::$_version ) . '" />';
		}

		/**
		 * Get metabox boxes.
		 *
		 * @param object $core Metabox object.
		 *
		 * @return bool
		 */
		public static function metabox_boxes( $core ) {
			if ( isset( $core->extensions['metaboxes_lite']->boxes ) && ! empty( $core->extensions['metaboxes_lite']->boxes ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Is Redux embedded inside a plugin.
		 *
		 * @param string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function is_inside_plugin( $file ) {
			$plugin_basename = plugin_basename( $file );

			if ( '/' . $plugin_basename !== $file ) {
				$slug = explode( '/', $plugin_basename );
				$slug = $slug[0];

				return array(
					'slug'     => $slug,
					'basename' => $plugin_basename,
					'path'     => self::wp_normalize_path( $file ),
					'url'      => plugins_url( $plugin_basename ),
					'real_path' => self::wp_normalize_path( dirname( realpath( $file ) ) ),
				);
			}

			return false;
		}

		/**
		 * Verify Pro Nonce.
		 *
		 * @param string $nonce Nonce.
		 *
		 * @return bool|int
		 */
		public static function pro_nonce( $nonce ) {
			return wp_verify_nonce( $nonce, 'redux_advanced_metaboxes' );
		}

		/**
		 * Is Redux embedded in a theme.
		 *
		 * @param string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function is_inside_theme( $file = '' ) {
			$theme_paths = array(
				self::wp_normalize_path( get_template_directory() )   => get_template_directory_uri(),      // parent.
				self::wp_normalize_path( get_stylesheet_directory() ) => get_stylesheet_directory_uri(),    // child.
			);

			$theme_paths = array_unique( $theme_paths );
			$file_path   = self::wp_normalize_path( $file );
			$filename    = explode( '/', $file );
			$filename    = end( $filename );

			foreach ( $theme_paths as $theme_path => $url ) {
				$real_path = self::wp_normalize_path( realpath( $theme_path ) );

				if ( strpos( $file_path, $real_path ) !== false ) {
					$slug          = explode( '/', $theme_path );
					$slug          = end( $slug );
					$relative_path = explode( $slug . '/', dirname( $file_path ) );
					$relative_path = $relative_path[1];
					$data          = array(
						'slug'      => $slug,
						'path'      => trailingslashit( trailingslashit( $theme_path ) . $relative_path ) . $filename,
						'real_path' => trailingslashit( trailingslashit( $real_path ) . $relative_path ) . $filename,
						'url'       => trailingslashit( trailingslashit( $url ) . $relative_path ) . $filename,
						'basename'  => trailingslashit( $slug ) . trailingslashit( $relative_path ) . $filename,
					);

					if ( count( $theme_paths ) > 1 ) {
						$key = array_search( $theme_path, $theme_paths, true );

						if ( false !== $key ) {
							unset( $theme_paths[ $key ] );
						}

						$data['parent_slug'] = end( explode( '/', end( $theme_paths ) ) );
						$data['parent_slug'] = end( explode( '/', end( $theme_paths ) ) );
					}

					return $data;
				}
			}

			return false;
		}
	}
}
