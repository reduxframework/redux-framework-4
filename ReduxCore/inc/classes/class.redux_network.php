<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Network')) {
    
    class Redux_Network extends Redux_Class {

        public function __construct ($parent) {
            parent::__construct($parent);
            
            if ( $parent->args['database'] == "network" && $parent->args['network_admin'] ) {
                add_action( 'network_admin_edit_redux_' . $parent->args['opt_name'], array( $this, 'save_network_page' ), 10, 0 );

                add_action( 'admin_bar_menu', array( $this, 'network_admin_bar' ), 999 );
            }
        }
        
        public function network_admin_bar( $wp_admin_bar ) {
            $core = $this->core();
            
            $args = array(
                'id'     => $core->args['opt_name'] . '_network_admin',
                'title'  => $core->args['menu_title'],
                'parent' => 'network-admin',
                'href'   => network_admin_url( 'settings.php' ) . '?page=' . $core->args['page_slug'],
                'meta'   => array( 'class' => 'redux-network-admin' )
            );

            $wp_admin_bar->add_node( $args );
        }

        public function save_network_page() {
            $core = $this->core();
            
            $data = $core->options_class->_validate_options( $_POST[ $core->args['opt_name'] ] );

            if ( ! empty ( $data ) ) {
                $core->options_class->set( $data );
            }

            wp_redirect( add_query_arg( array(
                'page'    => $core->args['page_slug'],
                'updated' => 'true'
            ), network_admin_url( 'settings.php' ) ) );

            exit ();
        }
    }
}