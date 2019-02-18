<?php
/**
 * Redux Select2 AJAX Class
 *
 * @class   Redux_AJAX_Select2
 * @version 4.0.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_AJAX_Select2', false ) ) {

	/**
	 * Class Redux_AJAX_Select2
	 */
	class Redux_AJAX_Select2 extends Redux_Class {

		/**
		 * Redux_AJAX_Select2 constructor.
		 *
		 * @param object $parent ReduxFramework object pointer.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			add_action( "wp_ajax_redux_{$parent->args['opt_name']}_select2", array( $this, 'ajax' ) );
		}

		/**
		 * AJAX callback for select2 match search.
		 */
		public function ajax() {
			$core = $this->core();

			if ( isset( $_REQUEST['nonce'] ) && isset( $_REQUEST['action'] ) ) {
				if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) ) ) {
					wp_send_json_error( esc_html__( 'Invalid security credential.  Please reload the page and try again.', 'redux-framework' ) );
				}

				if ( ! Redux_Helpers::current_user_can( $this->parent->args['page_permissions'] ) ) {
					wp_send_json_error( esc_html__( 'Invalid user capability.  Please reload the page and try again.', 'redux-framework' ) );
				}

				$return = array();

				if ( isset( $_REQUEST['data'] ) ) {
					$return = $core->wordpress_data->get( sanitize_text_field( wp_unslash( $_REQUEST['data'] ) ) );

					if ( is_array( $return ) && ! empty( $_REQUEST['action'] ) ) {
						if ( isset( $_REQUEST['q'] ) ) {
							$criteria = sanitize_text_field( wp_unslash( $_REQUEST['q'] ) );

							$search_arr = preg_grep( "/^{$criteria}(\w+)/i", array_values( $return ) );

							$to_json = array();

							foreach ( $search_arr as $id => $val ) {
								$to_json[] = array(
									'id'   => $id,
									'text' => $val,
								);
							}

							wp_send_json_success( $to_json );
						}
					}
				}
			}
		}
	}
}
