<?php
/**
 * Color Validation
 *
 * @package     Redux Framework/Validation
 * @author      Kevin Provance (kprovance) & Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Validation_Color', false ) ) {

	/**
	 * Class Redux_Validation_Color
	 */
	class Redux_Validation_Color extends Redux_Validate {

		/**
		 * Field Validate Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since ReduxFramework 3.0.0
		 */
		public function validate() {
			$sanitized_value = Redux_Colors::sanitize_color( $this->value );

			if ( $sanitized_value !== $this->value ) {
				// translators: %1$s: santizied value.  %2$s: Old value.
				$this->field['msg'] = sprintf( esc_html__( 'Sanitized value and saved as %1$s instead of %2$s.', 'redux-framework' ), '<code>' . $sanitized_value . '</code>', '<code>' . $this->value . '</code>' );

				$this->warning = $this->field;
			}

			$this->value = $sanitized_value;
		}
	}
}
