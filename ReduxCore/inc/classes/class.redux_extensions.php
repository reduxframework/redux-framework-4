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
                            $obj = new $extension_class ( $core );

                            //$class_name = get_class( $this );
                            //$reflector  = new ReflectionClass( $class_name );
                            //$path       = $reflector->getFilename();
                            //$path_info  = Redux_Helpers::path_info( $path );
                            //$this->_dir = dirname( $path_info['realpath'] );
                            //$this->_url = dirname( $path_info['url'] );
                            //

                            try {
                                $name             = str_replace( '_', '-', sanitize_title( $folder ) );
                                $this->upload_dir = ReduxFramework::$_upload_dir . $name;
                                $this->upload_url = ReduxFramework::$_upload_url . $name;
                                $path_info        = Redux_Helpers::path_info( dirname( $class_file ) );

                                $obj->_extension_dir = trailingslashit( $path_info['realpath'] );
                                $obj->_extension_url = trailingslashit( $path_info['url'] );
                                $obj->extension_url  = $obj->_extension_url;
                                $obj->extension_dir  = $obj->_extension_dir;

                                //if ($folder == "accordion") {
                                //    print_r(
                                    //    array(
                                    //        'info'       => $path_info,
                                    //        'url'        => $obj->_extension_url,
                                    //        'url2'       => $path_info['url'],
                                    //        'dir'        => $obj->_extension_dir,
                                    //        'upload_dir' => $this->upload_dir,
                                    //        'upload_url' => $this->upload_url,
                                    //
                                    //        'wp_upload_dir' => wp_upload_dir()['baseurl']
                                    //    )
                                    //);
                                    //exit();
                                //}



                            } catch ( Exception $e ) {
                                echo "HERE!!!";
                                print( $e );
                            }

                            $core->extensions[ $folder ] = $obj;


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