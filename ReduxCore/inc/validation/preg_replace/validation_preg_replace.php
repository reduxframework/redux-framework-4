<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_preg_replace' ) ) {
    
    class Redux_Validation_preg_replace extends Redux_Validate {

        /**
         * Field Validate Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->value = preg_replace_callback($this->field['preg']['pattern'], function($matches){return $this->field['preg']['replacement'];}, $this->value);
            $this->field['current'] = $this->value;
            
            $this->sanitize = $this->field;
        }
    }
}