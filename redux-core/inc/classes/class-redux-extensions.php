<?php
/**
 * Register Extensions for use
 *
 * @package Redux Framework/Classes
 * @since       3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Extensions', false ) ) {

	/**
	 * Class Redux_Extensions
	 */
	class Redux_Extensions extends Redux_Class {

		/**
		 * Redux_Extensions constructor.
		 *
		 * @param object $parent ReduxFramework object pointer.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );

			$this->load();
		}

		/**
		 * Class load functions.
		 */
		private function load() {
			$core = $this->core();

			$max = 1;

			if ( Redux_Core::$pro_loaded ) {
				$max = 2;
			}

			for ( $i = 1; $i <= $max; $i ++ ) {
				$path = Redux_Core::$dir . 'inc/extensions/';

				if ( 2 === $i ) {
					$path = Redux_Pro::$dir . 'core/inc/extensions/';
				}

				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				$path = apply_filters( 'redux/' . $core->args['opt_name'] . '/extensions/dir', $path );

				/**
				 * Action 'redux/extensions/before'
				 *
				 * @param object $this ReduxFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( 'redux/extensions/before', $core );

				/**
				 * Action 'redux/extensions/{opt_name}/before'
				 *
				 * @param object $this ReduxFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( "redux/extensions/{$core->args['opt_name']}/before", $core );

				if ( isset( $core->old_opt_name ) && null !== $core->old_opt_name ) {
					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( 'redux/extensions/' . $core->old_opt_name . '/before', $core );
				}

				require_once Redux_Core::$dir . 'inc/classes/class-redux-extension-abstract.php';

				$path = untrailingslashit( $path );

				Redux::set_extensions( $core->args['opt_name'], $path, true );

				/**
				 * Action 'redux/extensions/{opt_name}'
				 *
				 * @param object $this ReduxFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( "redux/extensions/{$core->args['opt_name']}", $core );

				if ( isset( $core->old_opt_name ) && null !== $core->old_opt_name ) {
					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( 'redux/extensions/' . $core->old_opt_name, $core );
				}
			}
		}
	}
}
