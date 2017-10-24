<?php

    /**
     * Register Extensions for use
     *
     * @since       3.0.0
     * @access      public
     * @return      void
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'Redux_Extensions' ) ) {

        class Redux_Extensions extends Redux_Class {

            public function __construct( $parent ) {
                parent::__construct( $parent );

                $this->load();
            }

            private function load() {
                $core = $this->core();

                $path    = ReduxCore::$_dir . '/inc/extensions/';
                $folders = scandir( $path, 1 );

                /**
                 * action 'redux/extensions/before'
                 *
                 * @param object $this ReduxFramework
                 */
                do_action( "redux/extensions/before", $core );

                /**
                 * action 'redux/extensions/{opt_name}/before'
                 *
                 * @param object $this ReduxFramework
                 */
                do_action( "redux/extensions/{$core->args['opt_name']}/before", $core );

                if ( isset( $core->old_opt_name ) && $core->old_opt_name !== null ) {
                    do_action( "redux/extensions/" . $core->old_opt_name . "/before", $core );
                }

                foreach ( $folders as $folder ) {
                    if ( $folder === '.' || $folder === '..' || ! is_dir( $path . $folder ) || substr( $folder, 0, 1 ) === '.' || substr( $folder, 0, 1 ) === '@' || substr( $folder, 0, 4 ) === '_vti' ) {
                        continue;
                    }

                    $extension_class = 'ReduxFramework_Extension_' . $folder;

                    /**
                     * filter 'redux/extension/{opt_name}/{folder}'
                     *
                     * @param        string                    extension class file path
                     * @param string $extension_class          extension class name
                     */
                    $class_file = apply_filters( "redux/extension/{$core->args['opt_name']}/$folder", "$path/$folder/extension_{$folder}.php", $extension_class );

                    if ( $class_file ) {
                        if ( file_exists( $class_file ) ) {
                            require_once $class_file;
                            $core->extensions[ $folder ] = new $extension_class ( $core );
                            try {
                                $name                                        = str_replace(
                                    array(
                                        'ReduxFramework_extension_',
                                        '_'
                                    ),
                                    array(
                                        '',
                                        '-'
                                    ),
                                    sanitize_title( $extension_class )
                                );
                                $this->upload_dir                            = ReduxFramework::$_upload_dir . $name;
                                $this->upload_url                            = ReduxFramework::$_upload_url . $name;
                                $path_info                                   = Redux_Helpers::path_info( $class_file );
                                $core->extensions[ $folder ]->_extension_dir = $core->extensions[ $folder ]->extension_dir = $path_info['realpath'];
                                $core->extensions[ $folder ]->_extension_url = $core->extensions[ $folder ]->extension_url = $path_info['url'];
                            } catch ( Exception $e ) {

                            }
                        }
                    }
                }

                /**
                 * action 'redux/extensions/{opt_name}'
                 *
                 * @param object $this ReduxFramework
                 */
                do_action( "redux/extensions/{$core->args['opt_name']}", $core );

                if ( isset( $core->old_opt_name ) && $core->old_opt_name !== null ) {
                    do_action( "redux/extensions/" . $core->old_opt_name, $core );
                }
            }
        }
    }