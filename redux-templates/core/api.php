<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Redux templates API class.
 *
 * @since 4.0.0
 * @package Redux Framework
 */

namespace ReduxTemplates;

use ReduxTemplates;
use WP_Patterns_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ReduxTemplates API class.
 *
 * @since 4.0.0
 */
class API {

	/**
	 * Seconds to cache the local files.
	 *
	 * @var int
	 */
	private $cache_time = 24 * 3600; // 24 hours
	/**
	 * API URL.
	 *
	 * @var string
	 */
	protected $api_base_url = 'https://api.starterblocks.io/';
	/**
	 * Default headers array.
	 *
	 * @var array
	 */
	protected $default_request_headers = array();
	/**
	 * Filesystem object instance.
	 *
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_filter( 'redux_templates_api_headers', array( $this, 'request_verify' ) );
		$this->default_request_headers = apply_filters( 'redux_templates_api_headers', $this->default_request_headers );

		add_action( 'rest_api_init', array( $this, 'register_api_hooks' ), 0 );

	}

	/**
	 * Get the filesystem.
	 *
	 * @return Filesystem
	 */
	private function get_filesystem() {
		if ( empty( $this->filesystem ) ) {
			$this->filesystem = new Filesystem();
		}

		return $this->filesystem;
	}

	/**
	 * Process the registered blocks from the library.
	 *
	 * @param array $parameters Array to be returned if no response, or to have data appended to.
	 * @return array Array of properly processed blocks and supported plugins.
	 */
	private function process_registered_blocks( $parameters ) {
		$data = $this->api_cache_fetch( array(), array(), 'library.json' );

		if ( empty( $data ) || ( ! empty( $data ) && ! isset( $data['plugins'] ) ) ) {
			return $parameters;
		}
		$supported = ReduxTemplates\SupportedPlugins::instance();
		$supported->init( $data['plugins'] );
		$plugins           = $supported::get_plugins();
		$installed_plugins = array();
		if ( ! isset( $parameters['registered_blocks'] ) ) {
			$parameters['registered_blocks'] = array();
		}

		foreach ( $plugins as $key => $value ) {
			if ( isset( $value['version'] ) ) {
				array_push( $installed_plugins, $key, true );
				$found_already = array_search( $key, $parameters['registered_blocks'], true );
				if ( false !== $found_already ) {
					unset( $parameters['registered_blocks'][ $found_already ] );
				}
				if ( isset( $value['namespace'] ) && $key !== $value['namespace'] ) {
					$found = array_search( $value['namespace'], $parameters['registered_blocks'], true );
					if ( false !== $found ) {
						unset( $parameters['registered_blocks'][ $found ] );
					}
				}
			}
		}
		$parameters['registered_blocks'] = array_merge( $installed_plugins, $parameters['registered_blocks'] );

		return $parameters;
	}

	/**
	 * Process the dependencies.
	 *
	 * @param array  $data Data array.
	 * @param string $key Key param.
	 * @return array Data array with dependencies outlined.
	 */
	private function process_dependencies( $data, $key ) {

		foreach ( $data[ $key ] as $kk => $pp ) {
			$debug = false;
			// Add debug if statement if key matches here.

			if ( isset( $pp['dependencies'] ) ) {
				foreach ( $pp['dependencies'] as $dep ) {
					if ( isset( $data['plugins'][ $dep ] ) ) {
						if ( isset( $data['plugins'][ $dep ]['no_plugin'] ) ) {
							continue;
						}
						if ( isset( $data['plugins'][ $dep ]['free_slug'] ) ) {
							if ( isset( $data['plugins'][ $data['plugins'][ $dep ]['free_slug'] ] ) ) {
								$plugin = $data['plugins'][ $data['plugins'][ $dep ]['free_slug'] ];
								if ( ! isset( $plugin['is_pro'] ) ) {
									if ( ! isset( $data[ $key ][ $kk ]['proDependenciesMissing'] ) ) {
										$data[ $key ][ $kk ]['proDependenciesMissing'] = array();
									}
									$data[ $key ][ $kk ]['proDependenciesMissing'][] = $dep;
								}
								if ( ! isset( $data[ $key ][ $kk ]['proDependencies'] ) ) {
									$data[ $key ][ $kk ]['proDependencies'] = array();
								}
								$data[ $key ][ $kk ]['proDependencies'][] = $dep;
							}
						} else {
							if ( ! isset( $data['plugins'][ $dep ]['version'] ) ) {
								if ( ! isset( $data[ $key ][ $kk ]['installDependenciesMissing'] ) ) {
									$data[ $key ][ $kk ]['installDependenciesMissing'] = array();
								}
								$data[ $key ][ $kk ]['installDependenciesMissing'][] = $dep;
							}
							if ( ! isset( $data[ $key ][ $kk ]['installDependencies'] ) ) {
								$data[ $key ][ $kk ]['installDependencies'] = array();
							}
							$data[ $key ][ $kk ]['installDependencies'][] = $dep;
						}
					}
				}
			}

			// Print the debug here if the key exists. Use `print_r( $data[ $key ][ $kk ] );exit();`.

		}

		return $data;

	}

	/**
	 * Get the last cache time.
	 *
	 * @param string $abs_path Absolute path to a file.
	 * @return string|bool Last modified time.
	 */
	private function get_cache_time( $abs_path ) {
		$filesystem    = $this->get_filesystem();
		$last_modified = false;
		if ( $filesystem->file_exists( $abs_path ) ) {
			$last_modified = filemtime( $abs_path );
		}

		return $last_modified;
	}

	/**
	 * Fetch from the cache if had.
	 *
	 * @param array  $parameters Absolute path to a file.
	 * @param array  $config Absolute path to a file.
	 * @param string $path URL path perform a request to a file.
	 * @param bool   $cache_only Set to only fetch from the local cache.
	 * @return array Response and possibly the template if recovered.
	 */
	public function api_cache_fetch( $parameters, $config, $path, $cache_only = false ) {
		$filesystem = $this->get_filesystem();

		if ( strpos( $path, $filesystem->cache_folder ) === false ) {
			$path = $filesystem->cache_folder . $path;
		}
		if ( ! $filesystem->file_exists( dirname( $path ) ) ) {
			$filesystem->mkdir( dirname( $path ) );
		}

		$last_modified = $this->get_cache_time( $path );
		$use_cache     = true;
		if ( isset( $parameters['no_cache'] ) ) {
			$use_cache = false;
		}
		if ( ! empty( $last_modified ) ) {
			$config['headers']['SB-Cache-Time'] = $last_modified;
			if ( time() > $last_modified + $this->cache_time ) {
				$use_cache = false;
			}
		}

		if ( $cache_only ) {
			$use_cache = true;
		}
		$data = array();
		if ( $use_cache ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors
			$data = @json_decode( $filesystem->get_contents( $path ), true );
		}
		if ( $cache_only ) {
			return $data;
		}

		if ( ! $use_cache && isset( $config['headers']['SB-Cache-Time'] ) ) {
			unset( $config['headers']['SB-Cache-Time'] );
		}

		if ( empty( $data ) ) {

			if ( isset( $parameters['registered_blocks'] ) ) {
				$config['headers']['SB-Registered-Blocks'] = implode( ',', $parameters['registered_blocks'] );
			}

			$results = $this->api_request( $config );

			if ( ! empty( $results ) ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors
				$data = @json_decode( $results, true );
				if ( isset( $data['use_cache'] ) ) {
					// phpcs:ignore WordPress.PHP.NoSilencedErrors
					$data          = @json_decode( $filesystem->get_contents( $path ), true );
					$data['cache'] = 'used';
				} else {
					if ( empty( $data ) ) {
						$data = array( 'message' => $results );
					}
					if ( isset( $data['status'] ) && 'error' === $data['status'] ) {
						wp_send_json_error( array( 'message' => $data['message'] ) );
					}
					$filesystem->put_contents( $path, wp_json_encode( $data ) );
				}
			} else {
				wp_send_json_error( array( 'message' => __( 'API fetch failure.', 'redux-framework' ) ) );
			}
		}

		if ( empty( $data ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors
			$data = @json_decode( $filesystem->get_contents( $path ), true );
			if ( $data ) {
				$data['status']  = 'error';
				$data['message'] = __( 'Fetching failed, used a cached version.', 'redux-framework' );
			} else {
				$data = array(
					'message' => 'Error Fetching',
				);
			}
		} else {
			if ( ! $use_cache ) {
				$data['cache'] = 'cleared';
			}
		}

		return $data;
	}


	/**
	 * Get library index. Support for library, collections, pages, sections all in a single request.
	 *
	 * @param \WP_REST_Request $request WP Rest request.
	 * @since 4.0.0
	 */
	public function get_index( \WP_REST_Request $request ) {

		$parameters = $request->get_params();
		$attributes = $request->get_attributes();

		if ( isset( $attributes['args']['route'] ) && ! empty( $attributes['args']['route'] ) ) {
			$type = str_replace( '/', '', $attributes['args']['route'] );
		}

		if ( empty( $type ) ) {
			wp_send_json_error( 'No type specified.' );
		}

		$config    = array(
			'path' => 'library/',
		);
		$test_path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'library.json';

		if ( file_exists( $test_path ) ) {
			$data = json_decode( ReduxTemplates\Init::get_local_file_contents( $test_path ), true );
		} else {
			$data = $this->api_cache_fetch( $parameters, $config, 'library.json' );
		}

		if ( isset( $data['plugins'] ) ) {
			$supported = ReduxTemplates\SupportedPlugins::instance();
			$supported->init( $data['plugins'] );
			$data['plugins'] = $supported::get_plugins();

			$data = $this->process_dependencies( $data, 'sections' );
			$data = $this->process_dependencies( $data, 'pages' );
		}

		if ( class_exists( 'WP_Patterns_Registry' ) ) {
			$patterns = \WP_Patterns_Registry::get_instance()->get_all_registered();
			foreach ( $patterns as $k => $p ) {
				$id                      = 'wp_block_pattern_' . $k;
				$data['sections'][ $id ] = array(
					'name'       => $p['title'],
					'categories' => array( 'WP Block Patterns' ),
					'source'     => 'wp_block_patterns',
					'id'         => $id,
				);
			}
		}

		wp_send_json_success( $data );
	}

	/**
	 * Filter an array recursively.
	 *
	 * @param array $input Array to filter.
	 * @return array Filtered array.
	 */
	private function array_filter_recursive( $input ) {
		foreach ( $input as &$value ) {
			if ( is_array( $value ) ) {
				$value = $this->array_filter_recursive( $value );
			}
		}

		return array_filter( $input );
	}

	/**

	 * Method for transmitting a template the user is sharing remotely.
	 *
	 * @param \WP_REST_Request $request WP Rest request.
	 * @since 4.0.0
	 */
	public function share_template( \WP_REST_Request $request ) {
		$parameters = $request->get_params();
		$attributes = $request->get_attributes();
		$parameters = $this->process_registered_blocks( $parameters );

		if ( empty( $parameters ) ) {
			wp_send_json_error( 'No template data found.' );
		}

		$config = array(
			'path'           => 'share/',
			'uid'            => get_current_user_id(),
			'editor_content' => isset( $parameters['editor_content'] ) ? (string) $parameters['editor_content'] : '',
			'editor_blocks'  => isset( $parameters['editor_blocks'] ) ? $parameters['editor_blocks'] : '',
			'postID'         => isset( $parameters['postID'] ) ? (string) sanitize_text_field(
				$parameters['postID']
			) : '',
			'title'          => isset( $parameters['title'] ) ? (string) sanitize_text_field(
				$parameters['title']
			) : 'The Title',
			'type'           => isset( $parameters['type'] ) ? (string) sanitize_text_field(
				$parameters['type']
			) : 'page',
			'categories'     => isset( $parameters['categories'] ) ? (string) sanitize_text_field(
				$parameters['categories']
			) : '',
			'description'    => isset( $parameters['description'] ) ? (string) sanitize_text_field(
				$parameters['description']
			) : '',
			'headers'        => array(
				'SB-Registered-Blocks' => isset( $parameters['registered_blocks'] ) ? (string) sanitize_text_field(
					implode( ',', $parameters['registered_blocks'] )
				) : '',
			),
		);

		$config = $this->array_filter_recursive( $config );

		if ( ! isset( $config['title'] ) ) {
			wp_send_json_error( array( 'messages' => 'A title is required.' ) );
		}
		if ( ! isset( $config['type'] ) ) {
			wp_send_json_error( array( 'messages' => 'A type is required.' ) );
		}

		$response = $this->api_request( $config );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors
		$data = @json_decode( $response, true );

		if ( 'success' === $data['status'] && isset( $data['url'] ) ) {
			wp_send_json_success( array( 'url' => $data['url'] ) );
		}

		wp_send_json_error( $data );

	}

	/**
	 * Run an API request.
	 *
	 * @param array $data Array related to an API request.
	 * @return string API response string.
	 */
	public function api_request( $data ) {
		$api_url = $this->api_base_url . $data['path'];

		if ( isset( $data['_locale'] ) ) {
			unset( $data['_locale'] );
		}
		$headers = array();
		if ( isset( $data['headers'] ) ) {
			$headers = $data['headers'];
			unset( $data['headers'] );
		}
		if ( isset( $data['p'] ) ) {
			$headers['SB-P'] = $data['p'];
			unset( $data['p'] );
		}
		if ( isset( $data['path'] ) ) {
			$headers['SB-Path'] = $data['path'];
			unset( $data['path'] );
		}

		$headers = wp_parse_args( $headers, $this->default_request_headers );

		$headers['Content-Type'] = 'application/json; charset=utf-8';
		$headers                 = array_filter( $headers );

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$headers['SB-User-Agent'] = (string) sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		}

		$headers['SB-SiteURL'] = get_site_url( get_current_blog_id() );

		$post_args = array(
			'timeout'     => 120,
			'body'        => wp_json_encode( $data ),
			'method'      => 'POST',
			'data_format' => 'body',
			'redirection' => 5,
			'headers'     => $headers,
		);

		$request = wp_remote_post(
			$api_url,
			$post_args
		);

		// Handle redirects.
		if (
			! is_wp_error( $request )
			&& isset( $request['http_response'] )
			&& $request['http_response'] instanceof \WP_HTTP_Requests_Response
			&& method_exists( $request['http_response'], 'get_response_object' )
			&& strpos( $request['http_response']->get_response_object()->url, 'files.starterblocks.io' ) !== false
		) {
			$request = wp_remote_get(
				$request['http_response']->get_response_object()->url,
				array( 'timeout' => 145 )
			);
		}

		if ( is_wp_error( $request ) ) {
			wp_send_json_error( array( 'messages' => $request->get_error_messages() ) );
		}

		return $request['body'];
	}

	/**
	 * Fetch a single template.
	 *
	 * @param \WP_REST_Request $request WP Rest request.
	 * @since 4.0.0
	 */
	public function get_template( \WP_REST_Request $request ) {

		$parameters = $request->get_params();
		$attributes = $request->get_attributes();
		$parameters = $this->process_registered_blocks( $parameters );

		if ( in_array( $parameters['type'], array( 'sections', 'pages' ), true ) ) {
			$parameters['type'] = substr_replace( $parameters['type'], '', - 1 );
		}

		$config = array(
			'path'   => 'template',
			'id'     => sanitize_text_field( $parameters['id'] ),
			'type'   => (string) sanitize_text_field( $parameters['type'] ),
			'source' => isset( $parameters['source'] ) ? $parameters['source'] : '',
		);

		$response = array();
		if ( 'wp_block_patterns' === $config['source'] && class_exists( 'WP_Patterns_Registry' ) ) {
			$patterns = \WP_Patterns_Registry::get_instance()->get_all_registered();
			$id       = explode( '_', $config['id'] );
			$id       = end( $id );

			if ( isset( $patterns[ $id ] ) ) {
				$response = array( 'template' => $patterns[ $id ]['content'] );
			}
		} else {
			$cache_path             = $config['type'] . DIRECTORY_SEPARATOR . $config['id'] . '.json';
			$parameters['no_cache'] = 1;
			$response               = $this->api_cache_fetch( $parameters, $config, $cache_path );
		}

		if ( ! empty( $response ) && isset( $response['message'] ) ) {
			$response['template'] = $response['message'];
			unset( $response['message'] );
		}

		// TODO - Validate active key.
		if ( ! \Redux_Core::$pro_loaded ) {
			$count = get_user_meta( $parameters['uid'], '_redux_templates_count', true );
			if ( '' === $count ) {
				$count = 5;
			}
			$count = intval( $count ) - 1;
			if ( intval( $count ) < 0 ) {
				wp_send_json_error( array( 'message' => 'Please activate Redux.' ) );
			}
			update_user_meta( $parameters['uid'], '_redux_templates_count', $count );
			$response['left'] = $count;
		}

		wp_send_json_success( $response );
	}

	/**
	 * Fetch a single template.
	 *
	 * @param array $data Data array.
	 * @since 4.0.0
	 * @return array
	 */
	public function request_verify( $data = array() ) {
		$config = array(
			'SB-Version'   => REDUXTEMPLATES_VERSION,
			'SB-Multisite' => is_multisite(),
		);

		// TODO - Update this with the EDD key or developer key.
		$config['SB-API-Key'] = \Redux_Helpers::get_hash();

		if ( ! empty( \Redux_Core::$pro_loaded ) && \Redux_Core::$pro_loaded ) {
			$config['SB-Pro'] = \Redux_Core::$pro_loaded;
		}
		$data = wp_parse_args( $data, $config );

		return $data;
	}


	/**
	 * Get all saved blocks (reusable blocks).
	 *
	 * @param \WP_REST_Request $request WP Rest request.
	 * @since 4.0.0
	 */
	public function get_saved_blocks( \WP_REST_Request $request ) {
		$args      = array(
			'post_type'   => 'wp_block',
			'post_status' => 'publish',
		);
		$r         = wp_parse_args( null, $args );
		$get_posts = new \WP_Query();
		$wp_blocks = $get_posts->query( $r );
		wp_send_json_success( $wp_blocks );
	}

	/**
	 * Delete a single saved (reusable) block
	 *
	 * @param \WP_REST_Request $request WP Rest request.
	 * @since 4.0.0
	 */
	public function delete_saved_block( \WP_REST_Request $request ) {
		$parameters = $request->get_params();
		$attributes = $request->get_attributes();
		if ( ! isset( $parameters['block_id'] ) ) {
			return wp_send_json_error(
				array(
					'status'  => 'error',
					'message' => 'Missing block_id.',
				)
			);
		}

		$block_id      = (int) sanitize_text_field( wp_unslash( $parameters['block_id'] ) );
		$deleted_block = wp_delete_post( $block_id );

		wp_send_json_success( $deleted_block );
	}

	/**
	 * Method used to register all rest endpoint hooks.
	 *
	 * @since 4.0.0
	 */
	public function register_api_hooks() {

		$hooks = array(
			'library'            => array(
				'callback' => 'get_index',
			),
			'pages'              => array(
				'callback' => 'get_index',
			),
			'sections'           => array(
				'callback' => 'get_index',
			),
			'collections'        => array(
				'callback' => 'get_index',
			),
			'feedback'           => array(
				'callback' => 'send_feedback',
			),
			'suggestion'         => array(
				'callback' => 'send_suggestion',
			),
			'template'           => array(
				'callback' => 'get_template',
			),
			'share'              => array(
				'method'   => 'POST',
				'callback' => 'share_template',
			),
			'get_saved_blocks'   => array(
				'callback' => 'get_saved_blocks',
			),
			'delete_saved_block' => array(
				'method'   => 'POST',
				'callback' => 'delete_saved_block',
			),

			'plugin-install'     => array(
				'method'   => 'GET',
				'callback' => 'plugin_install',
			),
		);

		foreach ( $hooks as $route => $data ) {
			$methods = array( 'GET', 'POST' );
			if ( isset( $data['method'] ) ) {
				$methods = explode( ',', $data['method'] );
			}

			foreach ( $methods as $method ) {
				register_rest_route(
					'redux/v1/templates',
					$route,
					array(
						'methods'             => $method,
						'callback'            => array( $this, $data['callback'] ),
						'permission_callback' => function () {
							return current_user_can( 'edit_posts' );
						},
						'args'                => array(
							'route' => $route,
						),
					)
				);
			}
		}

	}

	/**
	 * Install plugin endpoint.
	 *
	 * @param \WP_REST_Request $request WP Rest request.
	 * @since 4.0.0
	 */
	public function plugin_install( \WP_REST_Request $request ) {
		$data = $request->get_params();
		if ( empty( $data['slug'] ) ) {
			wp_send_json_error(
				array(
					'error' => __( 'Slug not specified.', 'redux-framework' ),
				)
			);
		}

		$slug   = (string) sanitize_text_field( $data['slug'] );
		$status = ReduxTemplates\Installer::run( $slug );
		if ( isset( $status['error'] ) ) {
			wp_send_json_error( $status );
		}
		wp_send_json_success( $status );
	}

}
