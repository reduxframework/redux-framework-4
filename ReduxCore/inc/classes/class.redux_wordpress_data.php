<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_WordPress_Data')) {
    
    class Redux_WordPress_Data extends Redux_Class {

        private $wp_data = null;
        
        public function get($type = false, $args = array()) {
            $core = $this->core();
            
            $data = "";

            /**
             * filter 'redux/options/{opt_name}/data/{type}'
             *
             * @param string $data
             */
            $data = apply_filters( "redux/options/{$core->args['opt_name']}/data/$type", $data );

            $argsKey = "";
            foreach ( $args as $key => $value ) {
                if ( ! is_array( $value ) ) {
                    $argsKey .= $value . "-";
                } else {
                    $argsKey .= implode( "-", $value );
                }
            }

            if ( empty ( $data ) && isset ( $this->wp_data[ $type . $argsKey ] ) ) {
                $data = $this->wp_data[ $type . $argsKey ];
            }

            if ( empty ( $data ) && ! empty ( $type ) ) {

                /**
                 * Use data from Wordpress to populate options array
                 * */
                if ( ! empty ( $type ) && empty ( $data ) ) {
                    if ( empty ( $args ) ) {
                        $args = array();
                    }

                    $data = array();
                    $args = wp_parse_args( $args, array() );

                    if ( $type == "categories" || $type == "category" ) {
                        $cats = get_categories( $args );
                        if ( ! empty ( $cats ) ) {
                            foreach ( $cats as $cat ) {
                                $data[ $cat->term_id ] = $cat->name;
                            }
                        }
                    } else if ( $type == "menus" || $type == "menu" ) {
                        $menus = wp_get_nav_menus( $args );
                        if ( ! empty ( $menus ) ) {
                            foreach ( $menus as $item ) {
                                $data[ $item->term_id ] = $item->name;
                            }
                        }
                    } else if ( $type == "pages" || $type == "page" ) {
                        if ( ! isset ( $args['posts_per_page'] ) ) {
                            $args['posts_per_page'] = 20;
                        }
                        $pages = get_pages( $args );
                        if ( ! empty ( $pages ) ) {
                            foreach ( $pages as $page ) {
                                $data[ $page->ID ] = $page->post_title;
                            }
                        }
                    } else if ( $type == "terms" || $type == "term" ) {
                        $taxonomies = $args['taxonomies'];
                        unset ( $args['taxonomies'] );
                        $terms = get_terms( $taxonomies, $args ); // this will get nothing
                        if ( ! empty ( $terms ) && ! is_a( $terms, 'WP_Error' ) ) {
                            foreach ( $terms as $term ) {
                                $data[ $term->term_id ] = $term->name;
                            }
                        }
                    } else if ( $type == "taxonomy" || $type == "taxonomies" ) {
                        $taxonomies = get_taxonomies( $args );
                        if ( ! empty ( $taxonomies ) ) {
                            foreach ( $taxonomies as $key => $taxonomy ) {
                                $data[ $key ] = $taxonomy;
                            }
                        }
                    } else if ( $type == "posts" || $type == "post" ) {
                        $posts = get_posts( $args );
                        if ( ! empty ( $posts ) ) {
                            foreach ( $posts as $post ) {
                                $data[ $post->ID ] = $post->post_title;
                            }
                        }
                    } else if ( $type == "post_type" || $type == "post_types" ) {
                        global $wp_post_types;

                        $defaults   = array(
                            'public'              => true,
                            'exclude_from_search' => false,
                        );
                        $args       = wp_parse_args( $args, $defaults );
                        $output     = 'names';
                        $operator   = 'and';
                        $post_types = get_post_types( $args, $output, $operator );

                        ksort( $post_types );

                        foreach ( $post_types as $name => $title ) {
                            if ( isset ( $wp_post_types[ $name ]->labels->menu_name ) ) {
                                $data[ $name ] = $wp_post_types[ $name ]->labels->menu_name;
                            } else {
                                $data[ $name ] = ucfirst( $name );
                            }
                        }
                    } else if ( $type == "tags" || $type == "tag" ) { // NOT WORKING!
                        $tags = get_tags( $args );
                        if ( ! empty ( $tags ) ) {
                            foreach ( $tags as $tag ) {
                                $data[ $tag->term_id ] = $tag->name;
                            }
                        }
                    } else if ( $type == "menu_location" || $type == "menu_locations" ) {
                        global $_wp_registered_nav_menus;

                        foreach ( $_wp_registered_nav_menus as $k => $v ) {
                            $data[ $k ] = $v;
                        }
                    } else if ( $type == "image_size" || $type == "image_sizes" ) {
                        global $_wp_additional_image_sizes;

                        foreach ( $_wp_additional_image_sizes as $size_name => $size_attrs ) {
                            $data[ $size_name ] = $size_name . ' - ' . $size_attrs['width'] . ' x ' . $size_attrs['height'];
                        }
                    } else if ( $type == "elusive-icons" || $type == "elusive-icon" || $type == "elusive" ||
                                $type == "font-icon" || $type == "font-icons" || $type == "icons"
                    ) {

                        /**
                         * filter 'redux/font-icons'
                         *
                         * @deprecated
                         *
                         * @param array $font_icons array of elusive icon classes
                         */
                        $font_icons = apply_filters( 'redux/font-icons', array() );

                        /**
                         * filter 'redux/{opt_name}/field/font/icons'
                         *
                         * @deprecated
                         *
                         * @param array $font_icons array of elusive icon classes
                         */
                        $font_icons = apply_filters( "redux/{$core->args['opt_name']}/field/font/icons", $font_icons );

                        foreach ( $font_icons as $k ) {
                            $data[ $k ] = $k;
                        }
                    } else if ( $type == "roles" ) {
                        /** @global WP_Roles $wp_roles */
                        global $wp_roles;

                        $data = $wp_roles->get_names();
                    } else if ( $type == "sidebars" || $type == "sidebar" ) {
                        /** @global array $wp_registered_sidebars */
                        global $wp_registered_sidebars;

                        foreach ( $wp_registered_sidebars as $key => $value ) {
                            $data[ $key ] = $value['name'];
                        }
                    } else if ( $type == "capabilities" ) {
                        /** @global WP_Roles $wp_roles */
                        global $wp_roles;

                        foreach ( $wp_roles->roles as $role ) {
                            foreach ( $role['capabilities'] as $key => $cap ) {
                                $data[ $key ] = ucwords( str_replace( '_', ' ', $key ) );
                            }
                        }
                    } else if ( $type == "callback" ) {
                        if ( ! is_array( $args ) ) {
                            $args = array( $args );
                        }
                        $data = call_user_func( $args[0] );
                    } else if ( $type == "users" || $type == "users" ) {
                        $users = get_users( $args );
                        if ( ! empty ( $users ) ) {
                            foreach ( $users as $user ) {
                                $data[ $user->ID ] = $user->display_name;
                            }
                        }
                    }
                }

                $this->wp_data[ $type . $argsKey ] = $data;
            }

            return $data;
        }
    }
}