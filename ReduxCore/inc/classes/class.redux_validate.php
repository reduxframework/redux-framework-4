<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Validate')) {
    
    class Redux_Validate {
        
        public function __construct ($parent, $field, $value, $current) {
                $this->parent       = $parent;
                $this->field        = $field;
                $this->value        = $value;
                $this->current      = $current;

                $this->validate();           
        }
    }
}