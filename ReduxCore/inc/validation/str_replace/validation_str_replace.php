<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_str_replace' ) ) {
    
    class Redux_Validation_str_replace extends Redux_Validate {

        /**
         * Field Validate Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 1.0.0
         */

        function validate() {
            $this->value = str_replace( $this->field['str']['search'], $this->field['str']['replacement'], $this->value );
            
            $this->field['current'] = $this->value;
            $this->sanitize = $this->field;
        }
    }
}