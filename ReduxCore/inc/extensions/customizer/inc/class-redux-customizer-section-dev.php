<?php
/**
 * Redux Customizer Section Dev Class
 *
 * @class Redux_Customizer_Section_Dev
 * @version 4.0.0
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Customizer_Section_Dev', false ) ) {

	/**
	 * Customizer section representing widget area (sidebar).
	 *
	 * @package    WordPress
	 * @subpackage Customize
	 * @since      4.1.0
	 * @see        WP_Customize_Section
	 */
	class Redux_Customizer_Section_Dev extends WP_Customize_Section {

		/**
		 * Type of this section.
		 *
		 * @since  4.1.0
		 * @access public
		 * @var string
		 */
		public $type = 'redux-rAds';

		/**
		 * Field render.
		 */
		protected function render() {
			?>
			<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="accordion-section"></li>
			<?php
		}

	}
}
