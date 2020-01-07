<?php
/**
 * Redux Framework Private Extended Functions Container Class
 *
 * @class       Redux_Functions_Ex
 * @since       3.0.0
 * @package     Redux_Framework/Classes
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
		 * What is this for?
		 *
		 * @var array
		 */
		public static $args;

		/**
		 * Parses the string into variables without the max_input_vars limitation.
		 *
		 * @since   3.5.7.11
		 *
		 * @param     string $string String of data.
		 *
		 * @return  array|false $result
		 * @author  harunbasic
		 * @access  private
		 */
		public static function parse_str( $string ) {
			if ( '' === $string ) {
				return false;
			}

			$result = array();
			$pairs  = explode( '&', $string );

			foreach ( $pairs as $key => $pair ) {
				// use the original parse_str() on each element.
				parse_str( $pair, $params );

				$k = key( $params );

				if ( ! isset( $result[ $k ] ) ) {
					$result += $params;
				} elseif ( is_array( $result[ $k ] ) && is_array( $params[ $k ] ) ) {
					$result[ $k ] = self::array_merge_recursive_distinct( $result[ $k ], $params[ $k ] );
				}
			}

			return $result;
		}

		/**
		 * Merge arrays without converting values with duplicate keys to arrays as array_merge_recursive does.
		 * As seen here http://php.net/manual/en/function.array-merge-recursive.php#92195
		 *
		 * @since   3.5.7.11
		 *
		 * @param     array $array1 array one.
		 * @param     array $array2 array two.
		 *
		 * @return  array $merged
		 * @author  harunbasic
		 * @access  private
		 */
		public static function array_merge_recursive_distinct( array $array1, array $array2 ) {
			$merged = $array1;

			foreach ( $array2 as $key => $value ) {

				if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
					$merged[ $key ] = self::array_merge_recursive_distinct( $merged[ $key ], $value );
				} elseif ( is_numeric( $key ) && isset( $merged[ $key ] ) ) {
					$merged[] = $value;
				} else {
					$merged[ $key ] = $value;
				}
			}

			return $merged;
		}

		/**
		 * Records calling function.
		 *
		 * @param     string $opt_name Panel opt_name.
		 */
		public static function record_caller( $opt_name = '' ) {
			global $pagenow;

			// phpcs:ignore WordPress.Security.NonceVerification
			if ( ! ( 'tools.php' === $pagenow && isset( $_GET['page'] ) && ( 'redux-framework' === $_GET['page'] || 'health-check' === $_GET['page'] ) ) ) {
				return;
			}

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions
			$caller = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1]['file'];

			if ( ! empty( $caller ) && ! empty( $opt_name ) && class_exists( 'Redux_Core' ) ) {
				if ( ! isset( Redux_Core::$callers[ $opt_name ] ) ) {
					Redux_Core::$callers[ $opt_name ] = array();
				}

				if ( strpos( $caller, 'class-redux-' ) !== false ) {
					return;
				}

				if ( ! in_array( $caller, Redux_Core::$callers[ $opt_name ], true ) ) {
					Redux_Core::$callers[ $opt_name ][] = $caller;
				}

				if ( ! empty( self::$args[ $opt_name ]['callers'] ) && ! in_array( $caller, self::$args[ $opt_name ]['callers'], true ) ) {
					self::$args[ $opt_name ]['callers'][] = $caller;
				}
			}
		}

		/**
		 * Normalize path.
		 *
		 * @param     string $path Path to normalize.
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
			echo '<meta name="framework" content="Redux ' . esc_html( Redux_Core::$version ) . '" />';
		}

		/**
		 * Get metabox boxes.
		 *
		 * @param     object $core Metabox object.
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
		 * @param     string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function is_inside_plugin( $file ) {
			$file            = self::wp_normalize_path( $file );
			$plugin_basename = self::wp_normalize_path( plugin_basename( $file ) );

			if ( $plugin_basename !== $file ) {
				$slug = explode( '/', $plugin_basename );
				$slug = $slug[0];

				$data             = array(
					'slug'      => $slug,
					'basename'  => $plugin_basename,
					'path'      => self::wp_normalize_path( $file ),
					'url'       => plugins_url( $plugin_basename ),
					'real_path' => self::wp_normalize_path( dirname( realpath( $file ) ) ),
				);
				$data['realpath'] = $data['real_path'];  // Shim for old extensions.

				return $data;
			}

			return false;
		}

		/**
		 * Verify Pro Nonce.
		 *
		 * @param     string $nonce Nonce.
		 *
		 * @return bool|int
		 */
		public static function pro_nonce( $nonce ) {
			return wp_verify_nonce( $nonce, 'redux_advanced_metaboxes' );
		}

		/**
		 * Is Redux embedded in a theme.
		 *
		 * @param     string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function is_inside_theme( $file = '' ) {
			$theme_paths = array(
				self::wp_normalize_path( get_template_directory() )   => get_template_directory_uri(),
				// parent.
				self::wp_normalize_path( get_stylesheet_directory() ) => get_stylesheet_directory_uri(),
				// child.
			);

			$theme_paths = array_unique( $theme_paths );
			$file_path   = self::wp_normalize_path( $file );

			$filename = explode( '\\', $file );

			end( $filename );

			$filename = prev( $filename );

			foreach ( $theme_paths as $theme_path => $url ) {
				$real_path = self::wp_normalize_path( realpath( $theme_path ) );

				if ( empty( $real_path ) ) {
					continue;
				}

				if ( ! empty( $real_path ) && strpos( $file_path, $real_path ) !== false ) {
					$slug             = explode( '/', $theme_path );
					$slug             = end( $slug );
					$relative_path    = explode( $slug . '/', dirname( $file_path ) );
					$relative_path    = $relative_path[1];
					$data             = array(
						'slug'      => $slug,
						'path'      => trailingslashit( trailingslashit( $theme_path ) . $relative_path ) . $filename,
						'real_path' => trailingslashit( trailingslashit( $real_path ) . $relative_path ) . $filename,
						'url'       => trailingslashit( trailingslashit( $url ) . $relative_path ) . $filename,
						'basename'  => trailingslashit( $slug ) . trailingslashit( $relative_path ) . $filename,
					);
					$data['realpath'] = $data['real_path'];  // Shim for old extensions.

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

		/**
		 * Used to fix 3.x and 4 compatibility for extensions
		 *
		 * @param     object $parent The extension parent object.
		 * @param     string $path - Path of the file.
		 * @param     string $ext_class - Extension class name.
		 * @param     string $new_class_name - New dynamic class name.
		 *
		 * @return object - Extended field class.
		 */
		public static function extension_compatibility( $parent, $path, $ext_class, $new_class_name, $name ) {
			$upload_dir = ReduxFramework::$_upload_dir . '/compatibility/';
			if ( ! file_exists( $upload_dir . $ext_class . '.php' ) ) {
				if ( ! is_dir( $upload_dir ) ) {
					$parent->filesystem->execute( 'mkdir', $upload_dir );
					$parent->filesystem->execute( 'put_contents', $upload_dir . 'index.php', array( 'content' => '<?php // Silence is golden.' ) );
				}
				if ( ! class_exists( $ext_class ) ) {
					require_once $path;
				}
				if ( ! file_exists( $upload_dir . $new_class_name . '.php' ) ) {
					$class_file = '<?php' . PHP_EOL . PHP_EOL .
						'class {{ext_class}} extends Redux_Extension_Abstract {' . PHP_EOL .
						'    private $c;' . PHP_EOL .
						'    public function __construct( $parent, $path, $ext_class ) {' . PHP_EOL .
						'        $this->c = $parent->extensions[\'' . $name . '\'];' . PHP_EOL .
						'        // Add all the params of the Abstract to this instance.' . PHP_EOL .
						'        foreach( get_object_vars( $this->c ) as $key => $value ) {' . PHP_EOL .
						'            $this->$key = $value;' . PHP_EOL .
						'        }' . PHP_EOL .
						'        parent::__construct( $parent, $path );' . PHP_EOL .
						'    }' . PHP_EOL .
						'    // fake "extends Redux_Extension_Abstract\" using magic function' . PHP_EOL .
						'    public function __call( $method, $args ) {' . PHP_EOL .
						'        $this->c->$method( $args[0] );' . PHP_EOL .
						'    }' . PHP_EOL .
						'}' . PHP_EOL;
					$template   = str_replace( '{{ext_class}}', $new_class_name, $class_file );
					$parent->filesystem->execute( 'put_contents', $upload_dir . $new_class_name . '.php', array( 'content' => $template ) );
				}
				if ( file_exists( $upload_dir . $new_class_name . '.php' ) ) {
					include_once $upload_dir . $new_class_name . '.php';
				}

				return new $new_class_name( $parent, $path, $ext_class );
			}
		}

		/**
		 * Used to deep merge two arrays.
		 *
		 * @param     array $a First array to deep merge.
		 * @param     array $b Second array to deep merge.
		 *
		 * @return    array - Deep merge of the two arrays.
		 */
		public static function nested_wp_parse_args( &$a, $b ) {
			$a      = (array) $a;
			$b      = (array) $b;
			$result = $b;
			foreach ( $a as $k => &$v ) {
				if ( is_array( $v ) && isset( $result[ $k ] ) ) {
					$result[ $k ] = self::nested_wp_parse_args( $v, $result[ $k ] );
				} else {
					$result[ $k ] = $v;
				}
			}

			return $result;
		}

	}
}
