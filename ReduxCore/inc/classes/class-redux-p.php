<?php
/**
 * Redux P Class
 *
 * @class Redux_P
 * @version 3.0.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_P', false ) ) {

	/**
	 * Class Redux_P
	 */
	class Redux_P {

		/**
		 * Redux_P constructor.
		 */
		public function __construct() {
			add_action( 'wp_ajax_nopriv_redux_p', array( $this, 'proxy' ) );
			add_action( 'wp_ajax_redux_p', array( $this, 'proxy' ) );
		}

		/**
		 * AJAX callback.
		 */
		public function proxy() {
			if ( ! isset( $_GET['nonce'] ) || ( isset( $_GET['nonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['nonce'] ) ), 'redux-ads-nonce' ) ) ) {
				die();
			}

			/** Script: Simple PHP Proxy: Get external HTML, JSON and more!
			 * Version: 1.6, Last updated: 1/24/2009*
			 * Project Home - http://benalman.com/projects/php-simple-proxy/
			 * GitHub       - http://github.com/cowboy/php-simple-proxy/
			 * Source       - http://github.com/cowboy/php-simple-proxy/raw/master/ba-simple-proxy.php
			 * About: License
			 * Copyright (c) 2010 "Cowboy" Ben Alman,
			 * Dual licensed under the MIT and GPL licenses.
			 * http://benalman.com/about/license/
			 */

			$_GET['mode']         = 'native';
			$_GET['full_headers'] = 1;
			$_GET['full_status']  = 1;
			$_GET['send_cookies'] = 1;

			// Change these configuration options if needed, see above descriptions for info.
			$enable_jsonp    = false;
			$enable_native   = true;
			$valid_url_regex = '/.*/';

			if ( isset( $_GET['url'] ) ) {
				$url = sanitize_text_field( wp_unslash( $_GET['url'] ) );
			}

			if ( isset( $_GET['nonce'] ) ) {
				$url = str_replace( 'nonce=' . sanitize_key( wp_unslash( $_GET['nonce'] ) ) . '&', '', $url );
			}

			if ( ! $url ) {
				// Passed url not specified.
				$contents = 'ERROR: url not specified';
				$status   = array( 'http_code' => 'ERROR' );
			} elseif ( ! preg_match( $valid_url_regex, $url ) ) {
				// Passed url doesn't match $valid_url_regex.
				$contents = 'ERROR: invalid url';
				$status   = array( 'http_code' => 'ERROR' );
			} else {
				$url = urldecode( $url );

				if ( isset( $_GET['proxy'] ) ) {
					$url .= '&proxy=' . sanitize_text_field( wp_unslash( $_GET['proxy'] ) );
				}

				// Ad URL rewrite.
				if ( strpos( $url, 'http' ) === false ) {
					$url = 'http:' . $url;
				}

				if ( isset( $_GET['callback'] ) ) {
					foreach ( $_GET as $key => $value ) {
						if ( in_array(
							$key,
							array(
								'url',
								'mode',
								'full_headers',
								'full_status',
								'send_cookies',
							),
							true
						) ) {
							continue;
						}

						$url .= '&' . $key . '=' . $value;
					}
				}

				$args = array(
					'user-agent' => isset( $_GET['user_agent'] ) ? sanitize_text_field( wp_unslash( $_GET['user_agent'] ) ) : Redux_Core::$server['HTTP_USER_AGENT'],
					'method'     => 'GET',
				);

				if ( isset( $_GET['send_cookies'] ) && sanitize_text_field( wp_unslash( $_GET['send_cookies'] ) ) ) {
					$cookie = array();

					foreach ( $_COOKIE as $key => $value ) {
						$cookie[] = $key . '=' . $value;
					}
					if ( isset( $_GET['send_session'] ) && sanitize_text_field( wp_unslash( $_GET['send_session'] ) ) ) {
						$cookie[] = SID;
					}
					$args['cookies'] = $cookie;
				}
				if ( 'post' === strtolower( Redux_Core::$server['REQUEST_METHOD'] ) ) {
					$args['body']   = $_POST;
					$args['method'] = 'POST';
				}

				$response = wp_remote_request( $url, $args );

				if ( ! is_wp_error( $response ) ) {
					$status      = $response['response']['code'];
					$contents    = $response['body'];
					$core_thread = isset( $_GET['t'] ) ? sanitize_text_field( wp_unslash( $_GET['t'] ) ) : 'span.mgv1_1';
					$contents    = str_replace( 'span.mgv1_1', 'span.' . $core_thread, $contents );
				}
			}

			if ( isset( $_GET['mode'] ) && 'native' === sanitize_text_field( wp_unslash( $_GET['mode'] ) ) ) {
				if ( ! $enable_native ) {
					$contents = 'ERROR: invalid mode';
					$status   = array( 'http_code' => 'ERROR' );
				}

				if ( ! is_wp_error( $response ) && isset( $response['headers']['content-type'] ) ) {
					header( 'Content-Type: ' . $response['headers']['content-type'] );
				}
				if ( ! is_wp_error( $response ) && isset( $response['headers']['content-language'] ) ) {
					header( 'Content-Language: ' . $response['headers']['content-language'] );
				}
				if ( ! is_wp_error( $response ) && isset( $response['headers']['set-cookie'] ) ) {
					header( 'Set-Cookie: ' . $response['headers']['set-cookie'] );
				}
				if ( isset( $contents ) ) {
					print str_replace( 'ads.redux.io', 'look.redux.io', $contents ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
			} else {
				// $data will be serialized into JSON data.
				$data = array();

				// Propagate all HTTP headers into the JSON data object.
				if ( isset( $_GET['full_headers'] ) && sanitize_text_field( wp_unslash( $_GET['full_headers'] ) ) ) {
					$data['headers'] = array();
				}

				// Propagate all cURL request / response info to the JSON data object.
				if ( isset( $_GET['full_status'] ) && sanitize_text_field( wp_unslash( $_GET['full_status'] ) ) ) {
					$data['status'] = $status;
				} else {
					$data['status']              = array();
					$data['status']['http_code'] = $status['http_code'];
				}

				// Set the JSON data object contents, decoding it from JSON if possible.
				$decoded_json     = json_decode( $contents );
				$data['contents'] = str_replace( 'e(window).width()', 'window.innerWidth||e(window).width()', $decoded_json ? $decoded_json : $contents );

				// Generate appropriate content-type header.
				$is_xhr = isset( Redux_Core::$server['HTTP_X_REQUESTED_WITH'] ) ? strtolower( Redux_Core::$server['HTTP_X_REQUESTED_WITH'] ) : 'xmlhttprequest';
				header( 'Content-type: application/' . ( $is_xhr ? 'json' : 'x-javascript' ) );

				// Get JSONP callback.
				$jsonp_callback = $enable_jsonp && isset( $_GET['callback'] ) ? sanitize_text_field( wp_unslash( $_GET['callback'] ) ) : null;

				// Generate JSON/JSONP string.
				print $jsonp_callback ? "$jsonp_callback($json)" : $json;  // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
	}
}
