<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_css' ) ) {
    class Redux_Validation_css extends Redux_Validate {

        /**
         * Field Validation Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 3.0.0
         */
        function validate() {
            $this->field['msg'] = isset($this->field['msg']) ? $this->field['msg'] : esc_html__( 'Unsafe strings were found in your CSS and have been filtered out.', 'redux-framework' );

            $data = $this->value;

            $data = wp_filter_nohtml_kses( $data );
            $data = str_replace( '&gt;', '>', $data );
            $data = stripslashes($data);

            if ($data != $this->value) {
                $this->field['current'] = $data;
                $this->warning = $this->field;
            }

            $this->value = $data;

        }
    }
}