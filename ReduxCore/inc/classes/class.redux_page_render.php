<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Page_Render')) {
    
    class Redux_Page_Render extends Redux_Class {

        private $show_hints = false;
        
        public function __construct ($parent) {
            parent::__construct($parent);
            
            add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), $parent->args['admin_bar_priority'] );
            
            // Options page
            add_action( 'admin_menu', array( $this, 'options_page' ) );

            // Add a network menu
            if ( $parent->args['database'] == "network" && $parent->args['network_admin'] ) {
                add_action( 'network_admin_menu', array( $this, 'options_page' ) );
            }
        }
        
        /**
         * Class Options Page Function, creates main options page.
         *
         * @since       1.0.0
         * @access      public
         * @return void
         */
        public function options_page() {
            $core = $this->core();
            
            if ( $core->args['menu_type'] == 'hidden' ) {

                // No menu to add!
            } else if ( $core->args['menu_type'] == 'submenu' ) {
                $this->submenu( $core );
            } else {
                // Theme-Check notice is displayed for WP.org theme devs, informing them to NOT use this.
                $core->page = call_user_func( 'add_menu_page', $core->args['page_title'], $core->args['menu_title'], $core->args['page_permissions'], $core->args['page_slug'], array(
                    $this,
                    'generate_panel'
                ), $core->args['menu_icon'], $core->args['page_priority']
                );

                if ( true === $core->args['allow_sub_menu'] ) {
                    if ( ! isset ( $section['type'] ) || $section['type'] != 'divide' ) {
                        foreach ( $core->sections as $k => $section ) {
                            $canBeSubSection = ( $k > 0 && ( ! isset ( $core->sections[ ( $k ) ]['type'] ) || $core->sections[ ( $k ) ]['type'] != "divide" ) ) ? true : false;

                            if ( ! isset ( $section['title'] ) || ( $canBeSubSection && ( isset ( $section['subsection'] ) && $section['subsection'] == true ) ) ) {
                                continue;
                            }

                            if ( isset ( $section['submenu'] ) && $section['submenu'] == false ) {
                                continue;
                            }

                            if ( isset ( $section['customizer_only'] ) && $section['customizer_only'] == true ) {
                                continue;
                            }

                            if ( isset ( $section['hidden'] ) && $section['hidden'] == true ) {
                                continue;
                            }

                            if ( isset( $section['permissions'] ) && ! Redux_Helpers::current_user_can( $section['permissions'] ) ) {
                                continue;
                            }

                            // ONLY for non-wp.org themes OR plugins. Theme-Check alert shown if used and IS theme.
                            call_user_func( 'add_submenu_page', $core->args['page_slug'], $section['title'], $section['title'], $core->args['page_permissions'], $core->args['page_slug'] . '&tab=' . $k, '__return_null' );
                        }

                        // Remove parent submenu item instead of adding null item.
                        remove_submenu_page( $core->args['page_slug'], $core->args['page_slug'] );
                    }
                }
            }

            add_action( "load-{$core->page}", array( $this, 'load_page' ) );
        }

        /**
         * Show page help
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function load_page() {
            $core = $this->core();
            
            // Do admin head action for this page
            add_action( 'admin_head', array( $this, 'admin_head' ) );

            // Do admin footer text hook
            add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );

            $screen = get_current_screen();

            if ( is_array( $core->args['help_tabs'] ) ) {
                foreach ( $core->args['help_tabs'] as $tab ) {
                    $screen->add_help_tab( $tab );
                }
            }

            // If hint argument is set, display hint tab
            if ( true == $this->show_hints ) {
                global $current_user;

                // Users enable/disable hint choice
                $hint_status = get_user_meta( $current_user->ID, 'ignore_hints' ) ? get_user_meta( $current_user->ID, 'ignore_hints', true ) : 'true';

                // current page parameters
                $curPage = $_GET['page'];

                $curTab = '0';
                if ( isset ( $_GET['tab'] ) ) {
                    $curTab = $_GET['tab'];
                }

                // Default url values for enabling hints.
                $dismiss = 'true';
                $s       = esc_html__( 'Enable', 'redux-framework' );

                // Values for disabling hints.
                if ( 'true' == $hint_status ) {
                    $dismiss = 'false';
                    $s       = esc_html__( 'Disable', 'redux-framework' );
                }

                // Make URL
                $url = '<a class="redux_hint_status" href="?dismiss=' . $dismiss . '&amp;id=hints&amp;page=' . esc_attr( $curPage ) . '&amp;tab=' . esc_attr( $curTab ) . '">' . $s . ' hints</a>';

                $event = esc_html__( 'moving the mouse over', 'redux-framework' );
                if ( 'click' == $core->args['hints']['tip_effect']['show']['event'] ) {
                    $event = esc_html__( 'clicking', 'redux-framework' );
                }

                // Construct message
                $msg = sprintf( esc_html__( 'Hints are tooltips that popup when', 'redux-framework') . ' %s ' . esc_html__('the hint icon, offering addition information about the field in which they appear.  They can be', 'redux-framework') . ' %sd ' . esc_html__('by using the link below.', 'redux-framework' ), $event, strtolower( $s ) ) . '<br/><br/>' . $url;

                // Construct hint tab
                $tab = array(
                    'id'      => 'redux-hint-tab',
                    'title'   => esc_html__( 'Hints', 'redux-framework' ),
                    'content' => '<p>' . $msg . '</p>'
                );

                $screen->add_help_tab( $tab );
            }

            // Sidebar text
            if ( $core->args['help_sidebar'] != '' ) {

                // Specify users text from arguments
                $screen->set_help_sidebar( $core->args['help_sidebar'] );
            } else {

                // If sidebar text is empty and hints are active, display text
                // about hints.
                if ( true == $this->show_hints ) {
                    $screen->set_help_sidebar( '<p><strong>Redux Framework</strong><br/><br/>Hint Tooltip Preferences</p>' );
                }
            }

            /**
             * action 'redux/page/{opt_name}/load'
             *
             * @param object $screen WP_Screen
             */
            do_action( "redux/page/{$core->args['opt_name']}/load", $screen );
        }
        
        
        /**
         * Class Add Sub Menu Function, creates options submenu in Wordpress admin area.
         *
         * @since       3.1.9
         * @access      private
         * @return      void
         */
        private function submenu( $core ) {
            global $submenu;

            $page_parent        = $core->args['page_parent'];
            $page_title         = $core->args['page_title'];
            $menu_title         = $core->args['menu_title'];
            $page_permissions   = $core->args['page_permissions'];
            $page_slug          = $core->args['page_slug'];
            
            // Just in case. One never knows.
            $page_parent = strtolower( $page_parent );

            $test = array(
                'index.php'               => 'dashboard',
                'edit.php'                => 'posts',
                'upload.php'              => 'media',
                'link-manager.php'        => 'links',
                'edit.php?post_type=page' => 'pages',
                'edit-comments.php'       => 'comments',
                'themes.php'              => 'theme',
                'plugins.php'             => 'plugins',
                'users.php'               => 'users',
                'tools.php'               => 'management',
                'options-general.php'     => 'options',
            );

            if ( isset ( $test[ $page_parent ] ) ) {
                $function   = 'add_' . $test[ $page_parent ] . '_page';
                $core->page = $function (
                    $page_title, $menu_title, $page_permissions, $page_slug, array( $this, 'generate_panel' )
                );
            } else {
                // Network settings and Post type menus. These do not have
                // wrappers and need to be appened to using add_submenu_page.
                // Okay, since we've left the post type menu appending
                // as default, we need to validate it, so anything that
                // isn't post_type=<post_type> doesn't get through and mess
                // things up.
                $addMenu = false;
                if ( 'settings.php' != $page_parent ) {
                    // Establish the needle
                    $needle = '?post_type=';

                    // Check if it exists in the page_parent (how I miss instr)
                    $needlePos = strrpos( $page_parent, $needle );

                    // It's there, so...
                    if ( $needlePos > 0 ) {

                        // Get the post type.
                        $postType = substr( $page_parent, $needlePos + strlen( $needle ) );

                        // Ensure it exists.
                        if ( post_type_exists( $postType ) ) {
                            // Set flag to add the menu page
                            $addMenu = true;
                        }
                        // custom menu
                    } elseif ( isset ( $submenu[ $core->args['page_parent'] ] ) ) {
                        $addMenu = true;
                    } else {
                        global $menu;

                        foreach ( $menu as $menupriority => $menuitem ) {
                            $needle_menu_slug = isset ( $menuitem ) ? $menuitem[2] : false;
                            if ( $needle_menu_slug != false ) {

                                // check if the current needle menu equals page_parent
                                if ( strcasecmp( $needle_menu_slug, $page_parent ) == 0 ) {

                                    // found an empty parent menu
                                    $addMenu = true;
                                }
                            }
                        }
                    }
                } else {
                    // The page_parent was settings.php, so set menu add
                    // flag to true.
                    $addMenu = true;
                }
                // Add the submenu if it's permitted.
                if ( true == $addMenu ) {
                    // ONLY for non-wp.org themes OR plugins. Theme-Check alert shown if used and IS theme.
                    $core->page = call_user_func( 'add_submenu_page', $page_parent, $page_title, $menu_title, $page_permissions, $page_slug, array(
                        $this,
                        'generate_panel'
                    ) );
                }
            }
        }
        
        public function generate_panel() {
            $core = $this->core();
            
            $panel = new Redux_Panel ( $core );
            $panel->init();
            $core->transient_class->set();
        }
        
        /**
         * Section HTML OUTPUT.
         *
         * @since       1.0.0
         * @access      public
         *
         * @param       array $section
         *
         * @return      void
         */
        public function section_desc( $section ) {
            $core = $this->core();
            
            $id = rtrim( $section['id'], '_section' );
            $id = str_replace($core->args['opt_name'], '', $id);

            if ( isset ( $core->sections[ $id ]['desc'] ) && ! empty ( $core->sections[ $id ]['desc'] ) ) {
                echo '<div class="redux-section-desc">' . wp_kses_post($core->sections[ $id ]['desc']) . '</div>';
            }
        }
        
        /**
         * Field HTML OUTPUT.
         * Gets option from options array, then calls the specific field type class - allows extending by other devs
         *
         * @since       1.0.0
         *
         * @param array  $field
         * @param string $v
         *
         * @return      void
         */
        public function _field_input( $field, $v = null ) {
            $core = $this->core();
            
            if ( isset ( $field['callback'] ) && ( is_callable( $field['callback'] ) || ( is_string( $field['callback'] ) && function_exists( $field['callback'] ) ) ) ) {

                $value = ( isset ( $core->options[ $field['id'] ] ) ) ? $core->options[ $field['id'] ] : '';

                /**
                 * action 'redux/field/{opt_name}/{field.type}/callback/before'
                 *
                 * @param array  $field field data
                 * @param string $value field.id
                 */
                do_action_ref_array( "redux/field/{$core->args['opt_name']}/{$field['type']}/callback/before", array(
                    &$field,
                    &$value
                ) );

                /**
                 * action 'redux/field/{opt_name}/callback/before'
                 *
                 * @param array  $field field data
                 * @param string $value field.id
                 */
                do_action_ref_array( "redux/field/{$core->args['opt_name']}/callback/before", array(
                    &$field,
                    &$value
                ) );

                call_user_func( $field['callback'], $field, $value );

                /**
                 * action 'redux/field/{opt_name}/{field.type}/callback/after'
                 *
                 * @param array  $field field data
                 * @param string $value field.id
                 */
                do_action_ref_array( "redux/field/{$core->args['opt_name']}/{$field['type']}/callback/after", array(
                    &$field,
                    &$value
                ) );

                /**
                 * action 'redux/field/{opt_name}/callback/after'
                 *
                 * @param array  $field field data
                 * @param string $value field.id
                 */
                do_action_ref_array( "redux/field/{$core->args['opt_name']}/callback/after", array(
                    &$field,
                    &$value
                ) );


                return;
            }

            if ( isset ( $field['type'] ) ) {

                // If the field is set not to display in the panel
                $display = true;
                if ( isset ( $_GET['page'] ) && $_GET['page'] == $core->args['page_slug'] ) {
                    if ( isset ( $field['panel'] ) && $field['panel'] == false ) {
                        $display = false;
                    }
                }

                if ( ! $display ) {
                    return;
                }

                $field_class = "ReduxFramework_{$field['type']}";

                if ( ! class_exists( $field_class ) ) {

                    /**
                     * filter 'redux/{opt_name}/field/class/{field.type}'
                     *
                     * @param       string        field class file path
                     * @param array $field        field data
                     */
                    $core_path = ReduxCore::$_dir . "inc/fields/{$field['type']}/field_{$field['type']}.php";

                    if (ReduxCore::$_pro_loaded) {
                        $pro_path = ReduxPro::$_dir . "inc/fields/{$field['type']}/field_{$field['type']}.php";

                        if (file_exists( $pro_path ) ) {
                            $filter_path = $pro_path;
                        } else {
                            $filter_path = $core_path;
                        }
                    } else {
                        $filter_path = $core_path;
                    }
                    
                    $class_file = apply_filters( "redux/{$core->args['opt_name']}/field/class/{$field['type']}", $filter_path, $field );

                    if ( $class_file ) {
                        if ( file_exists( $class_file ) ) {
                            require_once $class_file;
                        }
                    }
                }

                if ( class_exists( $field_class ) ) {
                    $value = isset ( $core->options[ $field['id'] ] ) ? $core->options[ $field['id'] ] : '';

                    if ( $v != null ) {
                        $value = $v;
                    }

                    /**
                     * action 'redux/field/{opt_name}/{field.type}/render/before'
                     *
                     * @param array  $field field data
                     * @param string $value field id
                     */
                    do_action_ref_array( "redux/field/{$core->args['opt_name']}/{$field['type']}/render/before", array(
                        &$field,
                        &$value
                    ) );

                    /**
                     * action 'redux/field/{$this->args['opt_name']}/render/before'
                     *
                     * @param array  $field field data
                     * @param string $value field id
                     */
                    do_action_ref_array( "redux/field/{$core->args['opt_name']}/render/before", array(
                        &$field,
                        &$value
                    ) );

                    if ( ! isset ( $field['name_suffix'] ) ) {
                        $field['name_suffix'] = "";
                    }

                    $data = array(
                        'field' => $field,
                        'value' => $value,
                        'core' => $core,
                        'mode' => 'render'
                    );

                    $pro_field_loaded = Redux_Functions::load_pro_field($data);
                    
                    $render = new $field_class ( $field, $value, $core );

                    ob_start();

                    $render->render();

                    /**
                     * filter 'redux/field/{opt_name}'
                     *
                     *
                     * @param       string        rendered field markup
                     * @param array $field        field data
                     */
                    $_render = apply_filters( "redux/field/{$core->args['opt_name']}", ob_get_contents(), $field );

                    /**
                     * filter 'redux/field/{opt_name}/{field.type}/render/after'
                     *
                     * @param       string        rendered field markup
                     * @param array $field        field data
                     */
                    $_render = apply_filters( "redux/field/{$core->args['opt_name']}/{$field['type']}/render/after", $_render, $field );

                    /**
                     * filter 'redux/field/{opt_name}/render/after'
                     *
                     * @param       string        rendered field markup
                     * @param array $field        field data
                     */
                    $_render = apply_filters( "redux/field/{$core->args['opt_name']}/render/after", $_render, $field );

                    ob_end_clean();

                    //save the values into a unique array in case we need it for dependencies
                    $core->fieldsValues[ $field['id'] ] = ( isset ( $value['url'] ) && is_array( $value ) ) ? $value['url'] : $value;

                    //create default data und class string and checks the dependencies of an object
                    $class_string = '';

                    $core->required_class->check_dependencies( $field );

                    /**
                     * action 'redux/field/{opt_name}/{field.type}/fieldset/before/{opt_name}'
                     *
                     * @param array  $field field data
                     * @param string $value field id
                     */
                    do_action_ref_array( "redux/field/{$core->args['opt_name']}/{$field['type']}/fieldset/before/{$core->args['opt_name']}", array(
                        &$field,
                        &$value
                    ) );

                    /**
                     * action 'redux/field/{opt_name}/fieldset/before/{opt_name}'
                     *
                     * @param array  $field field data
                     * @param string $value field id
                     */
                    do_action_ref_array( "redux/field/{$core->args['opt_name']}/fieldset/before/{$core->args['opt_name']}", array(
                        &$field,
                        &$value
                    ) );

                    //if ( ! isset( $field['fields'] ) || empty( $field['fields'] ) ) {
                    $hidden = '';
                    if ( isset ( $field['hidden'] ) && $field['hidden'] ) {
                        $hidden = 'hidden ';
                    }

                    if ( isset( $field['full_width'] ) && $field['full_width'] == true ) {
                        $class_string .= "redux_remove_th";
                    }

                    if ( isset ( $field['fieldset_class'] ) && ! empty( $field['fieldset_class'] ) ) {
                        $class_string .= ' ' . $field['fieldset_class'];
                    }
                    
                    if (ReduxCore::$_pro_loaded) {
                        if ($pro_field_loaded) {
                            $class_string .= ' redux-pro-field-init';
                        }
                    }

                    echo '<fieldset id="' . esc_attr($core->args['opt_name'] . '-' . $field['id']) . '" class="' . esc_attr($hidden . 'redux-field-container redux-field redux-field-init redux-container-' . $field['type'] . ' ' . $class_string) . '" data-id="' . esc_attr($field['id']) . '" data-type="' . esc_attr($field['type']) . '">';
                        echo $_render;

                        if ( ! empty ( $field['desc'] ) ) {
                            $field['description'] = $field['desc'];
                        }

                        echo ( isset ( $field['description'] ) && $field['type'] != "info" && $field['type'] !== "section" && ! empty ( $field['description'] ) ) ? '<div class="description field-desc">' . wp_kses_post($field['description']) . '</div>' : '';
                    echo '</fieldset>';

                    /**
                     * action 'redux/field/{opt_name}/{field.type}/fieldset/after/{opt_name}'
                     *
                     * @param array  $field field data
                     * @param string $value field id
                     */
                    do_action_ref_array( "redux/field/{$core->args['opt_name']}/{$field['type']}/fieldset/after/{$core->args['opt_name']}", array(
                        &$field,
                        &$value
                    ) );

                    /**
                     * action 'redux/field/{opt_name}/fieldset/after/{opt_name}'
                     *
                     * @param array  $field field data
                     * @param string $value field id
                     */
                    do_action_ref_array( "redux/field/{$core->args['opt_name']}/fieldset/after/{$core->args['opt_name']}", array(
                        &$field,
                        &$value
                    ) );
                }
            }
        }
        
        /**
         * Add admin bar menu
         *
         * @since       3.1.5.16
         * @access      public
         * @global      $menu , $submenu, $wp_admin_bar
         * @return      void
         */
        public function admin_bar_menu() {
            global $menu, $submenu, $wp_admin_bar;

            $core = $this->core();
            
            $ct         = wp_get_theme();
            $theme_data = $ct;

            if ( ! is_super_admin() || ! is_admin_bar_showing() || ! $core->args['admin_bar'] || $core->args['menu_type'] == 'hidden' ) {
                return;
            }

            if ( $menu ) {
                foreach ( $menu as $menu_item ) {
                    if ( isset ( $menu_item[2] ) && $menu_item[2] === $core->args["page_slug"] ) {

                        // Fetch the title
                        $title = empty ( $core->args['admin_bar_icon'] ) ? $menu_item[0] : '<span class="ab-icon ' . esc_attr($core->args['admin_bar_icon']) . '"></span>' . esc_html($menu_item[0]);

                        $nodeargs = array(
                            'id'    => $menu_item[2],
                            'title' => $title,
                            'href'  => admin_url( 'admin.php?page=' . $menu_item[2] ),
                            'meta'  => array()
                        );
                        
                        $wp_admin_bar->add_node( $nodeargs );

                        break;
                    }
                }

                if ( isset ( $submenu[ $core->args["page_slug"] ] ) && is_array( $submenu[ $core->args["page_slug"] ] ) ) {
                    foreach ( $submenu[ $core->args["page_slug"] ] as $index => $redux_options_submenu ) {
                        $subnodeargs = array(
                            'id'     => esc_html( $core->args["page_slug"] . '_' . $index ),
                            'title'  => esc_html( $redux_options_submenu[0] ),
                            'parent' => esc_html( $core->args["page_slug"] ),
                            'href'   => esc_url( admin_url( 'admin.php?page=' . $redux_options_submenu[2] ) ),
                        );

                        $wp_admin_bar->add_node( $subnodeargs );
                    }
                }

                // Let's deal with external links
                if ( isset ( $core->args['admin_bar_links'] ) ) {
                    if ( ! $core->args['dev_mode'] && $core->omit_admin_items ) {
                        return;
                    }

                    // Group for Main Root Menu (External Group)
                    $wp_admin_bar->add_node( array(
                        'id'     => esc_html($core->args["page_slug"] . '-external'),
                        'parent' => esc_html($core->args["page_slug"]),
                        'group'  => true,
                        'meta'   => array( 'class' => 'ab-sub-secondary' )
                    ) );

                    // Add Child Menus to External Group Menu
                    foreach ( $core->args['admin_bar_links'] as $link ) {
                        if ( ! isset ( $link['id'] ) ) {
                            $link['id'] = $core->args["page_slug"] . '-sub-' . sanitize_html_class( $link['title'] );
                        }
                        
                        $externalnodeargs = array(
                            'id'     => esc_html($link['id']),
                            'title'  => esc_html($link['title']),
                            'parent' => esc_html($core->args["page_slug"] . '-external'),
                            'href'   => esc_url($link['href']),
                            'meta'   => array( 'target' => '_blank' )
                        );

                        $wp_admin_bar->add_node( $externalnodeargs );
                    }
                }
            } else {
                // Fetch the title
                $title = empty ( $core->args['admin_bar_icon'] ) ? $core->args['menu_title'] : '<span class="ab-icon ' . esc_attr($core->args['admin_bar_icon']) . '"></span>' . esc_html($core->args['menu_title']);

                $nodeargs = array(
                    'id'    => esc_html($core->args["page_slug"]),
                    'title' => $title,
                    'href'  => esc_url(admin_url( 'admin.php?page=' . $core->args["page_slug"] )),
                    'meta'  => array()
                );

                $wp_admin_bar->add_node( $nodeargs );
            }
        }
        
        /**
         * Do action redux-admin-head for options page
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function admin_head() {
            $core = $this->core();
            
            /**
             * action 'redux/page/{opt_name}/header'
             *
             * @param  object $this ReduxFramework
             */
            do_action( "redux/page/{$core->args['opt_name']}/header", $core );
        }
        
        /**
         * Return footer text
         *
         * @since       2.0.0
         * @access      public
         * @return      string $this->args['footer_credit']
         */
        public function admin_footer_text() {
            $core = $this->core();
            
            return $core->args['footer_credit'];
        }
        
        public function get_header_html( $field ) {
            global $current_user;

            $core = $this->core();
            
            // Set to empty string to avoid wanrings.
            $hint = '';
            $th   = "";

            if ( isset ( $field['title'] ) && isset ( $field['type'] ) && $field['type'] !== "info" && $field['type'] !== "section" ) {
                $default_mark = ( ! empty ( $field['default'] ) && isset ( $core->options[ $field['id'] ] ) && $core->options[ $field['id'] ] == $field['default'] && ! empty ( $core->args['default_mark'] ) && isset ( $field['default'] ) ) ? $core->args['default_mark'] : '';

                // If a hint is specified in the field, process it.
                if ( isset ( $field['hint'] ) && ! '' == $field['hint'] ) {

                    // Set show_hints flag to true, so helptab will be displayed.
                    $this->show_hints = true;

                    $hint = apply_filters( 'redux/hints/html', $hint, $field, $core->args );

                    // Get user pref for displaying hints.
                    $metaVal = get_user_meta( $current_user->ID, 'ignore_hints', true );
                    if ( 'true' == $metaVal || empty ( $metaVal ) && empty( $hint ) ) {

                        // Set hand cursor for clickable hints
                        $pointer = '';
                        if ( isset ( $core->args['hints']['tip_effect']['show']['event'] ) && 'click' == $core->args['hints']['tip_effect']['show']['event'] ) {
                            $pointer = 'pointer';
                        }

                        $size = '16px';
                        if ( 'large' == $core->args['hints']['icon_size'] ) {
                            $size = '18px';
                        }

                        // In case docs are ignored.
                        $titleParam   = isset ( $field['hint']['title'] ) ? $field['hint']['title'] : '';
                        $contentParam = isset ( $field['hint']['content'] ) ? $field['hint']['content'] : '';

                        $hint_color = isset ( $core->args['hints']['icon_color'] ) ? $core->args['hints']['icon_color'] : '#d3d3d3';

                        // Set hint html with appropriate position css
                        $hint = '<div class="redux-hint-qtip" style="float:' . esc_attr($core->args['hints']['icon_position']) . '; font-size: ' . esc_attr($size) . '; color:' . esc_attr($hint_color) . '; cursor: ' . $pointer . ';" qtip-title="' . esc_attr($titleParam) . '" qtip-content="' . wp_kses_post($contentParam) . '">&nbsp;<i class="' . ( isset( $core->args['hints']['icon'] ) ? esc_attr($core->args['hints']['icon']) : '' ) . '"></i></div>';
                    }
                }

                if ( ! empty ( $field['title'] ) ) {
                    if ( 'left' == $core->args['hints']['icon_position'] ) {
                        $th = $hint . wp_kses_post($field['title']) . $default_mark . "";
                    } else {
                        $th = wp_kses_post($field['title']) . $default_mark . "" . $hint;
                    }
                }

                if ( isset ( $field['subtitle'] ) ) {
                    $th .= '<span class="description">' . wp_kses_post($field['subtitle']) . '</span>';
                }
            }

            if ( ! empty ( $th ) ) {
                $th = '<div class="redux_field_th">' . $th . '</div>';
            }

            $filter_arr = array(
                'editor',
                'ace_editor',
                'info',
                'section',
                'repeater',
                'color_scheme',
                'social_profiles',
                'css_layout'
            );

            if ( $core->args['default_show'] == true && isset ( $field['default'] ) && isset ( $core->options[ $field['id'] ] ) && $core->options[ $field['id'] ] != $field['default'] && ! in_array( $field['type'], $filter_arr ) ) {
                $th .= $this->get_default_output_string( $field );
            }

            return $th;
        }
        
        /**
         * Return default output string for use in panel
         *
         * @since       3.1.5
         * @access      public
         * @return      string default_output
         */
        private function get_default_output_string( $field ) {
            $default_output = "";

            if ( ! isset ( $field['default'] ) ) {
                $field['default'] = "";
            }

            if ( ! is_array( $field['default'] ) ) {
                if ( ! empty ( $field['options'][ $field['default'] ] ) ) {
                    if ( ! empty ( $field['options'][ $field['default'] ]['alt'] ) ) {
                        $default_output .= $field['options'][ $field['default'] ]['alt'] . ', ';
                    } else {
                        // TODO: This serialize fix may not be the best solution. Look into it. PHP 5.4 error without serialize
                        if ( ! is_array( $field['options'][ $field['default'] ] ) ) {
                            $default_output .= $field['options'][ $field['default'] ] . ", ";
                        } else {
                            $default_output .= serialize( $field['options'][ $field['default'] ] ) . ", ";
                        }
                    }
                } else if ( ! empty ( $field['options'][ $field['default'] ] ) ) {
                    $default_output .= $field['options'][ $field['default'] ] . ", ";
                } else if ( ! empty ( $field['default'] ) ) {
                    if ( $field['type'] == 'switch' && isset ( $field['on'] ) && isset ( $field['off'] ) ) {
                        $default_output .= ( $field['default'] == 1 ? $field['on'] : $field['off'] ) . ', ';
                    } else {
                        $default_output .= $field['default'] . ', ';
                    }
                }
            } else {
                foreach ( $field['default'] as $defaultk => $defaultv ) {
                    if ( ! empty ( $field['options'][ $defaultv ]['alt'] ) ) {
                        $default_output .= $field['options'][ $defaultv ]['alt'] . ', ';
                    } else if ( ! empty ( $field['options'][ $defaultv ] ) ) {
                        $default_output .= $field['options'][ $defaultv ] . ", ";
                    } else if ( ! empty ( $field['options'][ $defaultk ] ) ) {
                        $default_output .= $field['options'][ $defaultk ] . ", ";
                    } else if ( ! empty ( $defaultv ) ) {
                        $default_output .= $defaultv . ', ';
                    }
                }
            }

            if ( ! empty ( $default_output ) ) {
                $default_output = esc_html__( 'Default', 'redux-framework' ) . ": " . substr( $default_output, 0, - 2 );
            }

            if ( ! empty ( $default_output ) ) {
                $default_output = '<span class="showDefaults">' . esc_html($default_output) . '</span><br class="default_br" />';
            }

            return $default_output;
        }
        
        /**
         * Return Section Menu HTML
         *
         * @since       3.1.5
         * @access      public
         * @return      void
         */
        public function section_menu( $k, $section, $suffix = "", $sections = array() ) {
            $core = $this->core();

            $display = true;

            $section['class'] = isset ( $section['class'] ) ? ' ' . $section['class'] : '';

            if ( isset ( $_GET['page'] ) && $_GET['page'] == $core->args['page_slug'] ) {
                if ( isset ( $section['panel'] ) && $section['panel'] == false ) {
                    $display = false;
                }
            }

            if ( ! $display ) {
                return "";
            }

            if ( empty ( $sections ) ) {
                $sections = $core->sections;
            }

            $string = "";
            if ( ( ( isset ( $core->args['icon_type'] ) && $core->args['icon_type'] == 'image' ) || ( isset ( $section['icon_type'] ) && $section['icon_type'] == 'image' ) ) || ( isset( $section['icon'] ) && strpos( $section['icon'], '/' ) !== false ) ) {
                $icon = ( ! isset ( $section['icon'] ) ) ? '' : '<img class="image_icon_type" src="' . esc_url( $section['icon'] ) . '" /> ';
            } else {
                if ( ! empty ( $section['icon_class'] ) ) {
                    $icon_class = ' ' . $section['icon_class'];
                } elseif ( ! empty ( $core->args['default_icon_class'] ) ) {
                    $icon_class = ' ' . $core->args['default_icon_class'];
                } else {
                    $icon_class = '';
                }
                $icon = ( ! isset ( $section['icon'] ) ) ? '<i class="el el-cog' . esc_attr( $icon_class ) . '"></i> ' : '<i class="' . esc_attr( $section['icon'] ) . esc_attr( $icon_class ) . '"></i> ';
            }
            if ( strpos( $icon, 'el-icon-' ) !== false ) {
                $icon = str_replace( 'el-icon-', 'el el-', $icon );
            }

            $hide_section = '';
            if ( isset ( $section['hidden'] ) ) {
                $hide_section = ( $section['hidden'] == true ) ? ' hidden ' : '';
            }

            $canBeSubSection = ( $k > 0 && ( ! isset ( $sections[ ( $k ) ]['type'] ) || $sections[ ( $k ) ]['type'] != "divide" ) ) ? true : false;

            if ( ! $canBeSubSection && isset ( $section['subsection'] ) && $section['subsection'] == true ) {
                unset ( $section['subsection'] );
            }

            if ( isset ( $section['type'] ) && $section['type'] == "divide" ) {
                $string .= '<li class="divide' . esc_attr( $section['class'] ) . '">&nbsp;</li>';
            } else if ( ! isset ( $section['subsection'] ) || $section['subsection'] != true ) {
                $subsections      = ( isset ( $sections[ ( $k + 1 ) ] ) && isset ( $sections[ ( $k + 1 ) ]['subsection'] ) && $sections[ ( $k + 1 ) ]['subsection'] == true ) ? true : false;
                $subsectionsClass = $subsections ? ' hasSubSections' : '';
                $subsectionsClass .= ( ! isset ( $section['fields'] ) || empty ( $section['fields'] ) ) ? ' empty_section' : '';
                $rotate = $core->args['flyout_submenus'] == true ? ' el-rotate' : '';
                $extra_icon = $subsections ? '<span class="extraIconSubsections"><i class="el el-chevron-down' . $rotate . '">&nbsp;</i></span>' : '';
                $string .= '<li id="' . esc_attr( $k . $suffix ) . '_section_group_li" class="redux-group-tab-link-li' . esc_attr( $hide_section ) . esc_attr( $section['class'] ) . esc_attr( $subsectionsClass ) . '">';
               // var_dump(esc_attr( $k . $suffix ));
                $string .= '<a href="javascript:void(0);" id="' . esc_attr( $k . $suffix ) . '_section_group_li_a" class="redux-group-tab-link-a" data-key="' . esc_attr( $k ) . '" data-rel="' . esc_attr( $k . $suffix ) . '">' . $extra_icon . $icon . '<span class="group_title">' . wp_kses_post( $section['title'] ) . '</span></a>';

                $nextK = $k;

                // Make sure you can make this a subsection
                if ( $subsections ) {
                    
                    $string .= '<ul id="' . esc_attr( $nextK . $suffix ) . '_section_group_li_subsections" class="subsection">';
                    $doLoop = true;

                    while ( $doLoop ) {
                        $nextK += 1;
                        $display = true;

                        if ( isset ( $_GET['page'] ) && $_GET['page'] == $core->args['page_slug'] ) {
                            if ( isset ( $sections[ $nextK ]['panel'] ) && $sections[ $nextK ]['panel'] == false ) {
                                $display = false;
                            }
                        }

                        if ( count( $sections ) < $nextK || ! isset ( $sections[ $nextK ] ) || ! isset ( $sections[ $nextK ]['subsection'] ) || $sections[ $nextK ]['subsection'] != true ) {
                            $doLoop = false;
                        } else {
                            if ( ! $display ) {
                                continue;
                            }

                            $hide_sub = '';
                            if ( isset ( $sections[ $nextK ]['hidden'] ) ) {
                                $hide_sub = ( $sections[ $nextK ]['hidden'] == true ) ? ' hidden ' : '';
                            }

                            if ( ( isset ( $core->args['icon_type'] ) && $core->args['icon_type'] == 'image' ) || ( isset ( $sections[ $nextK ]['icon_type'] ) && $sections[ $nextK ]['icon_type'] == 'image' ) ) {
                                $icon = ( ! isset ( $sections[ $nextK ]['icon'] ) ) ? '' : '<img class="image_icon_type" src="' . esc_url( $sections[ $nextK ]['icon'] ) . '" /> ';
                            } else {
                                if ( ! empty ( $sections[ $nextK ]['icon_class'] ) ) {
                                    $icon_class = ' ' . $sections[ $nextK ]['icon_class'];
                                } elseif ( ! empty ( $core->args['default_icon_class'] ) ) {
                                    $icon_class = ' ' . $core->args['default_icon_class'];
                                } else {
                                    $icon_class = '';
                                }
                                $icon = ( ! isset ( $sections[ $nextK ]['icon'] ) ) ? '' : '<i class="' . esc_attr( $sections[ $nextK ]['icon'] ) . esc_attr( $icon_class ) . '"></i> ';
                            }
                            if ( strpos( $icon, 'el-icon-' ) !== false ) {
                                $icon = str_replace( 'el-icon-', 'el el-', $icon );
                            }

                            $sections[ $nextK ]['class'] = isset($sections[ $nextK ]['class']) ? $sections[ $nextK ]['class'] : '';
                            $section[ $nextK ]['class'] = isset ( $section[ $nextK ]['class'] ) ? $section[ $nextK ]['class'] : $sections[ $nextK ]['class'];
                            $string .= '<li id="' . esc_attr( $nextK . $suffix ) . '_section_group_li" class="redux-group-tab-link-li ' . esc_attr( $hide_sub ) . esc_attr( $section[ $nextK ]['class'] ) . ( $icon ? ' hasIcon' : '' ) . '">';
                            $string .= '<a href="javascript:void(0);" id="' . esc_attr( $nextK . $suffix ) . '_section_group_li_a" class="redux-group-tab-link-a" data-key="' . esc_attr( $nextK ) . '" data-rel="' . esc_attr( $nextK . $suffix ) . '">' . $icon . '<span class="group_title">' . wp_kses_post( $sections[ $nextK ]['title'] ) . '</span></a>';
                            $string .= '</li>';
                        }
                    }

                    $string .= '</ul>';
                }

                $string .= '</li>';
            }

            return $string;
        }
    }
}