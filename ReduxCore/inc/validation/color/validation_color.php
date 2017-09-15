<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_color' ) ) {
    
    class Redux_Validation_color extends Redux_Validate {

        /**
         * Field Validate Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 3.0.0
         */
        function validate() {
            $sanitized_value = Redux_Colors::sanitize_color($this->value);
            
            if ( $this->value != $sanitized_value ) {
                $this->field['msg'] = sprintf(
                    esc_html__( 'Sanitized value and saved as', 'redux-framework') . ' %1s ' . esc_html__( 'instead of', 'redux-framework') . ' %2s.',
                    '<code>' . $sanitized_value . '</code>',
                    '<code>' . $this->value . '</code>'
                );

                $this->warning = $this->field;
            }
            
            $this->value = $sanitized_value;
        }
    }
}