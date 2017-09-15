<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_no_html' ) ) {
    class Redux_Validation_no_html extends Redux_Validate {

        /**
         * Validate Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'You must not enter any HTML in this field.  All HTML has been removed.', 'redux-framework' );

            $newvalue = strip_tags( $this->value );

            if ( $this->value != $newvalue ) {
                $this->field['current'] = $newvalue;
                $this->warning = $this->field;
            }

            $this->value = $newvalue;
        }
    }
}