<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_ThirdParty_Fixes')) {
    
    class Redux_ThirdParty_Fixes extends Redux_Class {
        
        public function __construct($parent) {
            parent::__construct($parent);
            
            add_action( 'wp_print_scripts', array( $this, 'vc' ), 100 );
            add_action( 'admin_enqueue_scripts', array( $this, 'vc' ), 100 );
            
            $this->gt3_page_builder();
        }
        
        // Fix conflicts with Visual Composer.
        public function vc() {
            if ( redux_helpers::isFieldInUse( $this->core(), 'ace_editor' ) ) {
                wp_dequeue_script( 'wpb_ace' );
                wp_deregister_script( 'wpb_ace' );
            }
        }
        
        private function gt3_page_builder(){
            // Fix for the GT3 page builder: http://www.gt3themes.com/wordpress-gt3-page-builder-plugin/
            /** @global string $pagenow */
            if ( has_action( 'ecpt_field_options_' ) ) {
                global $pagenow;

                if ( $pagenow === 'admin.php' ) {
                    remove_action( 'admin_init', 'pb_admin_init' );
                }
            }
        }
    }
}