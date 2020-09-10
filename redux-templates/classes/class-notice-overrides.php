<?php // phpcs:ignore WordPress.Files.FileName

/**
 * Notice overrides for Redux Pro block plugins.
 *
 * @since   4.0.0
 * @package Redux Framework
 */

namespace ReduxTemplates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Redux Templates Notice Overrides Class
 *
 * @since 4.1.19
 */
class Notice_Overrides {

	/**
	 * ReduxTemplates Notice_Overrides.
	 *
	 * @since 4.1.19
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'filter_notices' ), 0 );
	}

	/**
	 * Filter out any notices before they're displayed.
	 *
	 * @since 4.0.0
	 */
	public function filter_notices() {
		if ( \Redux_Helpers::mokama() ) {
			// Filter Qubely Pro update notice.
			foreach ( $GLOBALS['wp_filter']['admin_notices']->callbacks[10] as $key => $value ) {
				if ( 'show_invalid_license_notice' === $value['function'][1] ) {
					if ( 'qubely-pro' === $value['function'][0]->plugin_slug ) {
						unset( $GLOBALS['wp_filter']['admin_notices']->callbacks[10][ $key ] );
						break;
					}
				}
			}
		}
	}
}
