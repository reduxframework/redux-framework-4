<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_html' ) ) {
    
    class Redux_Validation_html extends Redux_Validate {

        /**
         * Field Render Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'Disallowed tags as defined by WordPress have been detected and removed.', 'redux-framework' );
            
            $html = wp_kses_post( $this->value );
            
            if ($html != $this->value) {
                $this->field['current'] = $html;
                $this->warning = $this->field;
            }
            
            $this->value = $html;
        }
    }
}