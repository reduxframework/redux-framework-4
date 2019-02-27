<?php
/**
 * Redux Tracking Class
 *
 * @class   Redux_Statistics
 * @version 3.0.0
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ReduxFramework', false ) ) {
	return;
}

/**
 * Class that creates the statistics functionality for Redux, as the core class might be used in more plugins,
 * it's checked for existence first.
 * NOTE: this functionality is opt-in. Disabling the statistics in the settings or saying no when asked will cause
 * this file to not even be loaded.
 */

if ( ! class_exists( 'Redux_Statistics', false ) ) {

	/**
	 * Class Redux_Statistics
	 */
	class Redux_Statistics {

		/**
		 * Tracking options.
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * ReduxFramework object pointer.
		 *
		 * @var object
		 */
		public $parent;


		/**
		 * Refers to a single instance of this class.
		 *
		 * @var object
		 */
		private static $instance = null;

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @return Redux_Statistics A single instance of this class.
		 */
		public static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Class load.
		 *
		 * @param object $parent ReduxFramework pointer.
		 */
		public function load( $parent ) {

			$this->parent = $parent;
			// phpcs:ignore Squiz.PHP.CommentedOutCode
			/* delete_option( 'redux-framework-statistics' ); */
			$this->options = get_option( 'redux-framework-statistics' );
			$check         = wp_parse_args(
				$this->options,
				array(
					'hash'             => Redux_Helpers::get_hash(),
					'allow_statistics' => 'not_set',
					'dev_mode'         => false,
					'delay_show'       => ( time() + ( 5 ) ), // 3 days in the future.
				)
			);

			$this->options = $check;

			$this->options['dev_mode'] = $parent->args['dev_mode'];

			if ( isset( $_GET['redux_framework_disable_statistics'] ) && ! empty( $_GET['redux_framework_disable_statistics'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$this->options['allow_statistics'] = 'no';
			}

			if ( isset( $_GET['redux_framework_enable_statistics'] ) && ! empty( $_GET['redux_framework_enable_statistics'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$this->options['allow_statistics'] = 'yes';
			}

			if ( 'not_set' === $this->options['allow_statistics'] ) {
				if ( $this->options['delay_show'] <= time() ) {
					$this->options['allow_statistics'] = '';
				}
			}

			$compare = array_diff( $this->options, $check );

			if ( ! empty( $compare ) ) {
				update_option( 'redux-framework-statistics', $this->options );
			}

			add_action( 'wp_ajax_redux_allow_statistics', array( $this, 'callback' ) );

			if ( isset( $_GET['page'] ) && $this->parent->args['page_slug'] === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
				if ( empty( $this->options['allow_statistics'] ) ) {
					add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
				}
			}

			if ( isset( $this->options['allow_statistics'] ) && 'yes' === $this->options['allow_statistics'] ) {
				// The statistics checks daily, but only sends new data every 7 days.
				if ( ! wp_next_scheduled( 'redux_statistics' ) ) {
					wp_schedule_event( time(), 'daily', 'redux_statistics' );
				}
				add_action( 'redux_statistics', array( $this, 'statistics' ) );
			}

		}

		/**
		 * Enqueue scripts needed for statistics.
		 */
		public function enqueue() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );

			add_action( 'admin_print_footer_scripts', array( $this, 'request' ) );
		}

		/**
		 * Shows a popup that asks for permission to allow statistics.
		 */
		public function request() {
			$id    = '#wpadminbar';
			$nonce = wp_create_nonce( 'redux_activate_statistics' );

			$content  = '<h3>' . esc_html__( 'We Need Your Help', 'redux-framework' ) . '</h3>';
			$content .= '<p>' . esc_html__( 'Please help us improve our product by enabling anonymous usage statistics.', 'redux-framework' ) . '</p>';

			$opt_arr = array(
				'content'  => $content,
				'position' => array(
					'edge'  => 'top',
					'align' => 'center',
				),
			);

			$button2 = esc_html__( 'Allow', 'redux-framework' );

			$function2 = 'redux_store_answer("yes","' . $nonce . '")';
			$function1 = 'redux_store_answer("no","' . $nonce . '")';

			$this->print_scripts( $id, $opt_arr, esc_html__( 'Close', 'redux-framework' ), $button2, $function2, $function1 );
		}

		/**
		 * Prints the pointer script
		 *
		 * @param string      $selector         The CSS selector the pointer is attached to.
		 * @param array       $options          The options for the pointer.
		 * @param string      $button1          Text for button 1.
		 * @param string|bool $button2          Text for button 2 (or false to not show it, defaults to false).
		 * @param string      $button2_function The JavaScript function to attach to button 2.
		 * @param string      $button1_function The JavaScript function to attach to button 1.
		 */
		private function print_scripts( $selector, $options, $button1, $button2 = false, $button2_function = '', $button1_function = '' ) {
			?>
			<script type="text/javascript">
				(function( $ ) {
					var redux_pointer_options = <?php echo wp_json_encode( $options ); ?>, setup;

					function redux_store_answer( input, nonce ) {
						var redux_statistics_data = {
							action: 'redux_allow_statistics',
							nonce: nonce
						};
						$.post(
							ajaxurl, redux_statistics_data, function() {
								$( '#wp-pointer-0' ).remove();
							}
						);
					}

					redux_pointer_options = $.extend(
						redux_pointer_options, {
							buttons: function( event, t ) {
								button = $(
									'<a id="pointer-close" style="margin-left:5px" class="button-secondary">' + '<?php echo esc_html( $button1 ); ?>' + '</a>' );
								button.bind(
									'click.pointer', function() {
										t.element.pointer( 'close' );
										//console.log( 'close button' );
									}
								);
								return button;
							},
							close: function() {
							}
						}
					);

					setup = function() {
						$( '<?php echo( esc_html( $selector ) ); ?>' ).pointer( redux_pointer_options ).pointer(
							'open' );
						var ptc = $( '#pointer-close' );
						<?php if ( $button2 ) { ?>
						ptc.after(
							'<a id="pointer-primary" class="button-primary">' + '<?php echo esc_html( $button2 ); ?>' + '</a>' );
						$( '#pointer-primary' ).click(
							function() {
								<?php echo( $button2_function ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							}
						);
						ptc.click(
							function() {
								<?php if ( '' === $button1_function ) { ?>
								redux_store_answer( input, nonce );
								<?php } else { ?>
									<?php echo( $button1_function ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php } ?>
							}
						);
						<?php } elseif ( $button1 && ! $button2 ) { ?>
						ptc.click(
							function() {
								<?php if ( '' !== $button1_function ) { ?>
									<?php echo( $button1_function ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php } ?>
							}
						);
						<?php } ?>
					};

					if ( redux_pointer_options.position && redux_pointer_options.position.defer_loading ) {
						$( window ).bind( 'load.wp-pointers', setup );
						console.log( 'load' );
					} else {
						$( document ).ready( setup );
					}
				})( jQuery );
			</script>
			<?php
		}

		/**
		 * Main statistics function.
		 */
		public function statistics() {
			// Start of Metrics.
			$data = get_option( 'redux_statistics_cache' );

		    // phpcs:ignore Squiz.PHP.CommentedOutCode
			if ( empty( $data ) || ( ! empty( $data ) && time() <= $data ) ) {
				$url = 'https://api.redux.io/statistics';

				$args = array(
					'timeout' => 20,
					'headers' => Redux_Helpers::get_request_headers(),
					'body'    => Redux_Helpers::get_statistics_object(),
				);

				$request = wp_remote_post(
					$url,
					$args
				);

				// Store for a week, then push data again.
				if ( ! is_wp_error( $request ) ) {
					$time = time() + ( 7 * 24 * 60 * 60 );
					update_option( 'redux_statistics_cache', $time );
				}
			}
			exit();
		}

		/**
		 * Permit support callback.
		 */
		public function callback() {
			// Verify that the incoming request is coming with the security nonce.
			if ( check_ajax_referer( 'redux_activate_statistics', 'nonce' ) ) {
				$options                     = get_option( 'redux-framework-statistics' );
				$options['allow_statistics'] = 'yes';
				if ( update_option( 'redux-framework-statistics', $options ) ) {
					die( '1' );
				} else {
					die( '0' );
				}
			} else {
				// Send -1 if the attempt to save via Ajax was completed invalid.
				die( '-1' );
			} // end if
		}

	}

	Redux_Statistics::get_instance();
}
