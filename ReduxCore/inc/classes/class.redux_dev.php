<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Dev')) {
    
    class Redux_Dev extends Redux_Class {

        public function __construct ( $parent = null ) {
            parent::__construct ( $parent );
            
            if ( $parent->args['dev_mode'] && $parent->args['update_notice'] ) {
                add_action( 'admin_init', array( $this, '_update_check' ) );
            }            
            
            $this->load($parent);
        }
        
        public function load($core) {
            if ( $core->args['dev_mode'] == true || Redux_Helpers::isLocalHost() == true ) {
                new Redux_Dashboard( $core );

                if ( ! isset ( $GLOBALS['redux_notice_check'] ) || $GLOBALS['redux_notice_check'] == 0 ) {
                    $params = array(
                        'dir_name'    => 'notice',
                        'server_file' => 'http://reduxframework.com/wp-content/uploads/redux/redux_notice.json',
                        'interval'    => 3,
                        'cookie_id'   => 'redux_blast',
                    );

                    new Redux_Newsflash( $core, $params );

                    $GLOBALS['redux_notice_check'] = 1;
                }
            }
        }
        
        public function _update_check() {
            $core = $this->core();
            
            // Only one notice per instance please
            if ( ! isset ( $GLOBALS['redux_update_check'] ) || $GLOBALS['redux_update_check'] == 0 ) {
                $msg = Redux_Functions::updateCheck( $core, ReduxCore::$_version );

                if (is_array($msg) && !empty($msg)) {
                    Redux_Admin_Notices::set_notice($msg);
                }

                $GLOBALS['redux_update_check'] = 1;
            }
        }
    }
}