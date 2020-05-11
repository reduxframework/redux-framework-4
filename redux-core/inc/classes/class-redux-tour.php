<?php
/**
 * Redux Tour Class
 *
 * @class Redux_Tour
 * @version 3.0.0
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ReduxFramework', false ) ) {
	return;
}

/**
 * Class that creates the tracking functionality for Redux, as the core class might be used in more plugins,
 * it's checked for existence first.
 *
 * NOTE: this functionality is opt-in. Disabling the tracking in the settings or saying no when asked will cause
 * this file to not even be loaded.
 */

if ( ! class_exists( 'Redux_Tour', false ) ) {

	/**
	 * Class Redux_Tour
	 */
	class Redux_Tour {

		/**
		 *  Options.
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
		 * @return Redux_Tracking A single instance of this class.
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

			if ( isset( $_GET['page'] ) && $parent->args['page_slug'] === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
				if ( true === $this->parent->args['dev_mode'] || 'redux_demo' === $this->parent->args['page_slug'] ) {
					$check = get_user_meta( get_current_user_id(), 'redux_tour' );
					if ( empty( $check ) ) {
						add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
					}
				}
			}

			add_action( 'wp_ajax_redux_tour', array( $this, 'callback' ) );
		}

		/**
		 * Enqueue scripts for newsletter opt-in
		 */
		public function enqueue() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );
			add_action( 'admin_print_footer_scripts', array( $this, 'tour' ) );
		}

		/**
		 * Shows a popup that asks for permission to allow tracking.
		 */
		public function tour() {
			$id    = '#wpadminbar';
			$nonce = wp_create_nonce( 'redux_tour' );

			$content = '<h3>' . esc_html__( 'Welcome to the Redux Demo Panel', 'redux-framework' ) . '</h3>';

			// translators: %1$s: Docs URL.
			$content .= '<p><strong>' . esc_html__( 'Getting Started', 'redux-framework' ) . '</strong><br>' . sprintf( esc_html__( 'This panel demonstrates the many features of Redux.  Before digging in, we suggest you get up to speed by reviewing %1$s', 'redux-framework' ), '<a href="//docs.reduxframework.com/redux-framework/getting-started/" target="_blank">' . esc_html__( 'our documentation', 'redux-framework' ) . '</a>' );

			// translators: %1$s: Builder URL.
			$content .= '<p><strong>' . esc_html__( 'Redux Generator', 'redux-framework' ) . '</strong><br>' . sprintf( esc_html__( 'Want to get a head start? Use the %1$s. It will create a customized boilerplate theme or a standalone admin folder complete with all things Redux (with the help of Underscores and TGM). Save yourself a headache and try it today.', 'redux-framework' ), '<a href="//generate.reduxframework.com/" target="_blank">' . esc_html__( 'Redux Generator', 'redux-framework' ) . '</a>' );

			// translators: %1$s: Extensions URL.
			$content .= '<p><strong>' . esc_html__( 'Redux Extensions', 'redux-framework' ) . '</strong><br>' . sprintf( esc_html__( 'Did you know we have extensions, which greatly enhance the features of Redux?  Visit our %1$s to learn more!', 'redux-framework' ), '<a href="//reduxframework.com/extensions/" target="_blank">' . esc_html__( 'extensions directory', 'redux-framework' ) . '</a>' );

			// translators: %1$s: Review URL.  %2$s: Donation URL.
			$content .= '<p><strong>' . esc_html__( 'Like Redux?', 'redux-framework' ) . '</strong><br>' . sprintf( esc_html__( 'If so, please %1$s and consider making a %2$s to keep development of Redux moving forward.', 'redux-framework' ), '<a target="_blank" href="//wordpress.org/support/view/plugin-reviews/redux-framework">' . esc_html__( 'leave us a favorable review on WordPress.org', 'redux-framework' ) . '</a>', '<a href="//www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=N5AD7TSH8YA5U" target="_blank">' . esc_html__( 'donation', 'redux-framework' ) . '</a>' );
			$content .= '<p><strong>' . esc_html__( 'Newsletter', 'redux-framework' ) . '</strong><br>' . esc_html__( 'If you\'d like to keep up to with all things Redux, please subscribe to our newsletter', 'redux-framework' ) . ':</p>';
			$content .= '<form action="https://news.redux.io/subscribe" method="POST" target="_blank" accept-charset="utf-8" class="validate">
                                <p style="text-align: center;">
                                    <label for="email">' . esc_html__( 'Email Address', 'redux-framework' ) . '</label>
                                    <input type="email" name="email" class="required email" id="email"/>
                                    <input type="hidden" name="list" value="9K1qDRvB8Ux0DqpEoQSEPA"/>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="submit" class="button button-primary" name="submit" value="' . esc_html__( 'Subscribe', 'redux-framework' ) . '" id="submit"/>
                                    </p>
                            </form>';
			$opt_arr  = array(
				'content'      => $content,
				'position'     => array(
					'edge'  => 'top',
					'align' => 'center',
				),
				'pointerWidth' => 450,
			);

			$function1 = 'redux_tour("' . $nonce . '")';

			$this->print_scripts( $id, $opt_arr, esc_html__( 'Close', 'redux-framework' ), $function1 );
		}

		/**
		 * Prints the pointer script
		 *
		 * @param string $selector         The CSS selector the pointer is attached to.
		 * @param array  $options          The options for the pointer.
		 * @param string $button1          Text for button 1.
		 * @param string $button1_function The JavaScript function to attach to button 1.
		 */
		private function print_scripts( $selector, $options, $button1, $button1_function = '' ) {
			?>
			<script type="text/javascript">
				(function( $ ) {
					var redux_tour_pointer_options = <?php echo wp_json_encode( $options ); ?>, setup;

					function redux_tour( nonce ) {
						var redux_tracking_data = {
							action: 'redux_tour',
							nonce: nonce
						};
						$.post(
							ajaxurl, redux_tracking_data, function() {
								$( '#wp-pointer-0' ).remove();
							}
						);
					}

					redux_tour_pointer_options = $.extend(
						redux_tour_pointer_options,
						{
							buttons: function( event, t ) {
								button = $( '<a id="pointer-close" style="margin-left:5px" class="button-secondary">' + '<?php echo esc_html( $button1 ); ?>' + '</a>' );
								button.bind(
									'click.pointer',
									function() {
										t.element.pointer( 'close' );
									}
								);

								return button;
							},
							close: function() {}
						}
					);

					setup = function() {
						$( '<?php echo( esc_html( $selector ) ); ?>' ).pointer( redux_tour_pointer_options ).pointer( 'open' );
						var ptc = $( '#pointer-close' );
						ptc.click(
							function() {
								<?php if ( '' !== $button1_function ) { ?>
									<?php echo( $button1_function ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php } ?>
							}
						);
					};

					if ( redux_tour_pointer_options.position && redux_tour_pointer_options.position.defer_loading ) {
						$( window ).bind( 'load.wp-pointers', setup );
					} else {
						$( document ).ready( setup );
					}
				})( jQuery );
			</script>
			<?php
		}

		/**
		 * Permit support callback.
		 */
		public function callback() {
			// Verify that the incoming request is coming with the security nonce.
			if ( check_ajax_referer( 'redux_tour', 'nonce' ) ) {
				if ( update_user_meta( get_current_user_id(), 'redux_tour', time() ) ) {
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

	Redux_Tour::get_instance();
}
