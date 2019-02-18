<?php
/**
 * Redux Dashboard Functions Class
 *
 * @package     Redux_Framework
 * @author      Kevin Provance (kprovance)
 * @subpackage  Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Dashboard', false ) ) {

	/**
	 * Class Redux_Dashboard
	 */
	class Redux_Dashboard {

		/**
		 * Redux_Dashboard constructor.
		 *
		 * @param object $parent ReduxFramework pointer.
		 */
		public function __construct( $parent ) {
			$fname = Redux_Functions::dat( 'add_redux_dashboard', $parent->args['opt_name'] );

			add_action( 'wp_dashboard_setup', array( $this, $fname ) );
		}

		/**
		 * Adds Redux News widget to dashboard.
		 */
		public function add_redux_dashboard() {
			// phpcs:ignore Generic.Strings.UnnecessaryStringConcat
			call_user_func( 'add' . '_meta' . '_box', 'redux_dashboard_widget', 'Redux Framework News', array( $this, 'redux_dashboard_widget' ), 'dashboard', 'side', 'high' );
		}

		/**
		 * DAT.
		 *
		 * @return string
		 */
		public function dat() {
			return '';
		}

		/**
		 * Callback for the dashboard widget.
		 */
		public function redux_dashboard_widget() {
			echo '<div class="rss-widget">';
			wp_widget_rss_output(
				array(
					'url'          => 'https://reduxframework.com/feed/',
					'title'        => 'REDUX_NEWS',
					'items'        => 3,
					'show_summary' => 1,
					'show_author'  => 0,
					'show_date'    => 1,
				)
			);

			echo '</div>';
		}
	}
}
