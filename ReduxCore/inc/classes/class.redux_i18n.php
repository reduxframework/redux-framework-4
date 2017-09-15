<?php

/**
 * Load the plugin text domain for translation.
 *
 * @since    3.0.5
 */

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_I18n')) {
    
    class Redux_I18n extends Redux_Class {

        public function __construct ($parent, $file) {
            parent::__construct($parent);
            
            $this->load($file);
        }
        
        private function load($file) {
            $core = $this->core();
            
            /**
             * Locale for text domain
             * filter 'redux/textdomain/basepath/{opt_name}'
             *
             * @param string     The locale of the blog or from the 'locale' hook
             * @param string     'redux-framework'  text domain
             */

            $basename = basename( $file );
            $basepath = plugin_basename( $file );
            $basepath = str_replace( $basename, '', $basepath );

            $basepath = apply_filters( "redux/textdomain/basepath/{$core->args['opt_name']}", $basepath );

            $loaded = load_plugin_textdomain( 'redux-framework', false, $basepath . 'languages');

            if ( !$loaded ){
                $loaded = load_muplugin_textdomain( 'redux-framework', $basepath . 'languages' );
            }

            if ( !$loaded ){
                $loaded = load_theme_textdomain( 'redux-framework', $basepath . 'languages' );
            }

            if ( ! $loaded ) {
                $locale = apply_filters( 'redux/locale', get_locale(), 'redux-framework' );
                $mofile = dirname( __FILE__ ) . '/languages/redux-framework-' . $locale . '.mo';
                load_textdomain( 'redux-framework', $mofile );
            }
        }
    }
}