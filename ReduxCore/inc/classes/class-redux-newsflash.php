<?php
/**
 * Redux Newsflash Class
 * Added by KP on March 31, 2015.  So, if something is buggered, it's probably my bad!  ;-)
 *
 * @class Redux_Newsflash
 * @version 3.5.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Newsflash', false ) ) {

	/**
	 * Class Redux_Newsflash
	 */
	class Redux_Newsflash {

		/**
		 * Redux Framework object pointer.
		 *
		 * @var object
		 */
		private $parent = null;

		/**
		 * Notice data to display.
		 *
		 * @var mixed|string|void
		 */
		private $notice_data = '';

		/**
		 * URL of news file on the server.
		 *
		 * @var string
		 */
		private $server_file = '';

		/**
		 * Interval to check.
		 *
		 * @var int
		 */
		private $interval = 3;

		/**
		 * Unique cookie ID.
		 *
		 * @var string
		 */
		private $cookie_id = '';

		/**
		 * Redux_Newsflash constructor.
		 *
		 * @param object $parent ReduxFramework pointer.
		 * @param array  $params Notice args.
		 */
		public function __construct( $parent, $params ) {
			// set parent object.
			$this->parent = $parent;

			if ( ! is_admin() ) {
				return;
			}

			$this->server_file = $params['server_file'];
			$this->interval    = isset( $params['interval'] ) ? $params['interval'] : 3;
			$this->cookie_id   = isset( $params['cookie_id'] ) ? $params['cookie_id'] : $parent->args['opt_name'] . '_blast';

			$this->notice_data = get_option( 'r_notice_data', '' );

			$fname = Redux_Functions::bub( 'get_notice_json', $parent->args['opt_name'] );
			$mname = Redux_Functions::yo( 'display_message', $parent->args['opt_name'] );

			// if notice data is empty.
			if ( empty( $this->notice_data ) ) {
				// get notice data from server and create cache data.
				$this->$fname();
			} else {
				// check expiry time.
				if ( ! isset( $_COOKIE[ $this->cookie_id ] ) ) {
					// expired!  get notice data from server.
					$this->$fname();
				}
			}

			// set the admin notice msg.
			$this->$mname();
		}

		/**
		 * Bub!
		 */
		private function bub() {
			$this->notice_data = '';
		}

		/**
		 * Retrive news data.
		 */
		private function get_notice_json() {

			// get notice data from server.
			// phpcs:ignore WordPress.PHP.NoSilencedErrors
			$data = @wp_remote_get( $this->server_file, array( 'sslverify' => false ) );
			if ( isset( $data ) && ! empty( $data ) && ! is_wp_error( $data ) && 200 === $data['response']['code'] ) {
				$data = $data['body'];

				// if some data exists.
				if ( '' !== $data || ! empty( $data ) ) {
					if ( ! empty( $this->notice_data ) ) {
						if ( 0 === strcmp( $data, $this->notice_data ) ) {
							// set new cookie for interval value.
							Redux_Functions::set_cookie( $this->cookie_id, time(), time() + ( 86400 * $this->interval ), '/' );

							// bail out.
							return;
						}
					}

					update_option( 'r_notice_data', $data );
					$this->notice_data = $data;

					// set cookie for three day expiry.
					setcookie( $this->cookie_id, time(), time() + ( 86400 * $this->interval ), '/' );

					// set unique key for dismiss meta key.
					update_option( $this->cookie_id, time() );
				}
			}
		}

		/**
		 * Display notice on admin page.
		 */
		private function display_message() {
			// Notice data exists?
			if ( ! empty( $this->notice_data ) ) {
				// decode json string.
				$data = (array) json_decode( $this->notice_data );
				// must be array and not empty.
				if ( is_array( $data ) && ! empty( $data ) ) {

					// No message means nothing to display.
					if ( ! isset( $data['message'] ) || '' === $data['message'] || empty( $data['message'] ) ) {
						return;
					}

					// validate data.
					$data['type']  = isset( $data['type'] ) && '' !== $data['type'] ? $data['type'] : 'updated';
					$data['title'] = isset( $data['title'] ) && '' !== $data['title'] ? $data['title'] : '';

					if ( 'redux-message' === $data['type'] ) {
						$data['type'] = 'updated redux-message';
					}

					$data['color'] = isset( $data['color'] ) ? $data['color'] : '#00A2E3';

					// get unique meta key.
					$key = get_option( $this->cookie_id );

					// set admin notice array.
					$notice_data = array(
						'parent'  => $this->parent,
						'type'    => $data['type'],
						'msg'     => $data['title'] . $data['message'],
						'id'      => $this->cookie_id . '_' . $key,
						'dismiss' => true,
						'color'   => $data['color'],
					);

					Redux_Admin_Notices::set_notice( $notice_data );
				}
			}
		}
	}
}
