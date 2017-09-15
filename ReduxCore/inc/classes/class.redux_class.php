<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Class')) {
    
    class Redux_Class {
        
        public $parent      = null;
        public $args        = array();
        public $opt_name    = '';
        
        public function __construct ($parent = null) {
            if ($parent !== null) {
                $this->parent       = $parent;

                $this->args         = $parent->args;
                $this->opt_name     = $this->args['opt_name'];
            }
        }
        
        public function core(){
            if (isset($this->opt_name) && $this->opt_name != '') {
                return Redux::instance($this->opt_name);
            }
        }
    }
}