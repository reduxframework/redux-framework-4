<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_js' ) ) {
    
    class Redux_Validation_js extends Redux_Validate {

        /**
         * Field Validation Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'Javascript has been successfully escaped.', 'redux-framework' );
            
            $js = esc_js($this->value);
            
            if ($js != $this->value) {
                $this->field['current'] = $js;
                $this->warning = $this->field;
            }
            
            $this->value = $js;
            
        }
    }
}