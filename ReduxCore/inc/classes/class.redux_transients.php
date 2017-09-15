<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Transients')) {
    
    class Redux_Transients extends Redux_Class {

        public function get() {
            $core = $this->core();
            
            if ( ! isset ( $core->transients ) ) {
                $core->transients       = get_option( $core->args['opt_name'] . '-transients', array() );
                $core->transients_check = $core->transients;
            }
        }

        public function set() {
            $core = $this->core();
            
            if ( ! isset ( $core->transients ) || ! isset ( $core->transients_check ) || $core->transients != $core->transients_check ) {
                update_option( $core->args['opt_name'] . '-transients', $core->transients );
                $core->transients_check = $core->transients;
            }
        }
        
    }
}