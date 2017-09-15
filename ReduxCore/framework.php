<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     Redux_Framework
 * @subpackage  Core
 * @author      Redux Framework Team
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// I am some base code!! 

require_once( dirname( __FILE__ ) . '/class.redux-core.php' );                     

//new ReduxCore;
ReduxCore::$_version = '4.0.0.1';
ReduxCore::$_path = dirname( __FILE__ );
ReduxCore::instance();

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework' ) ) {

    /**
     * Main ReduxFramework class
     *
     * @since       1.0.0
     */
    class ReduxFramework {

        public static $instance = null;
        public static $_version;
        public static $_dir;
        public static $_url;
        public static $_upload_dir;
        public static $_upload_url;

        public static function init() {

            // Backward compatibility for extensions
            self::$_version     = ReduxCore::$_version;
            self::$_dir         = ReduxCore::$_dir;
            self::$_url         = ReduxCore::$_url;
            self::$_upload_dir  = ReduxCore::$_upload_dir;
            self::$_upload_url  = ReduxCore::$_upload_url;
        }

        public $admin_notices = array();
        public $page = '';
        public $saved = false;
        public $fields = array(); // Fields by type used in the panel
        public $field_sections = array(); // Section id's by field type, then field ID
        public $current_tab = ''; // Current section to display, cookies
        public $extensions = array(); // Extensions by type used in the panel
        public $sections = array(); // Sections and fields
        public $errors = array(); // Errors
        public $warnings = array(); // Warnings
        public $sanitize = array();
        public $options = array(); // Option values
        public $options_defaults = null; // Option defaults
        public $notices = array(); // Option defaults
        public $compiler_fields = array(); // Fields that trigger the compiler hook
        public $required = array(); // Information that needs to be localized
        public $required_child = array(); // Information that needs to be localized
        public $localize_data = array(); // Information that needs to be localized
        public $fonts = array(); // Information that needs to be localized
        public $folds = array(); // The itms that need to fold.
        public $path = '';
        public $changed_values = array(); // Values that have been changed on save. Orig values.
        public $output = array(); // Fields with CSS output selectors
        public $outputCSS = null; // CSS that get auto-appended to the header
        public $compilerCSS = null; // CSS that get sent to the compiler hook
        public $customizerCSS = null; // CSS that goes to the customizer
        public $fieldsValues = array(); //all fields values in an id=>value array so we can check dependencies
        public $fieldsHidden = array(); //all fields that didn't pass the dependency test and are hidden
        public $toHide = array(); // Values to hide on page load
        public $typography = null; //values to generate google font CSS
        public $import_export = null;
        public $no_panel = array(); // Fields that are not visible in the panel
        public $hidden_perm_fields = array(); //  Hidden fields specified by 'permissions' arg.
        public $hidden_perm_sections = array(); //  Hidden sections specified by 'permissions' arg.
        public $typography_preview = array();
        public $args = array();
        public $old_opt_name = null;
        public $filesystem = null;
        public $font_groups = array();
        public $lang = "";
        public $reload_fields = array();
        public $omit_share_icons = false;
        public $omit_admin_items = false;
        public $dev_mode_forced     = false;
        public $options_class       = null;
        public $required_class      = null;
        public $output_class        = null;
        public $render_class        = null;
        public $enqueue_class       = null;
        public $transient_class     = null;
        public $wordpress_data      = null;
        public $validate_class      = null;

	/**
	 * Cloning is forbidden.
	 * @since 4.0.0
	 */
	public function __clone() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'redux=framework' ), '4.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 4.0.0
	 */
	public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'redux=framework' ), '4.0' );
	}
        
        /**
         * Class Constructor. Defines the args for the theme options class
         *
         * @since       1.0.0
         *
         * @param       array $sections   Panel sections.
         * @param       array $args       Class constructor arguments.
         *
         * @return \ReduxFramework
         */
        public function __construct( $sections = array(), $args = array() ) {
            if (ReduxCore::is_heartbeat()) {
                return;
            }

            $args = new Redux_Args($this, $args);
            $this->args = $args->get;

            ReduxCore::core_construct($this, $this->args);

            new Redux_Admin_Notices($this);

            if ( ! empty ( $this->args['opt_name'] ) ) {
                $this->filesystem = Redux_Filesystem::get_instance( $this );

                // Move to the first loop area!
                /**
                 * filter 'redux/options/{opt_name}/sections'
                 *
                 * @param  array $sections field option sections
                 */
                $this->sections = apply_filters( "redux/options/{$this->args['opt_name']}/sections", $sections );

                /**
                 * Construct hook
                 * action 'redux/construct'
                 *
                 * @param object $this ReduxFramework
                 */
                do_action( 'redux/construct', $this );

                // Internataionalization
                new Redux_I18n($this, __FILE__);

                $this->required_class = new Redux_Required($this);
                $this->transient_class = new Redux_Transients($this);
                $this->wordpress_data = new Redux_WordPress_Data($this);
                $this->validate_class = new Redux_Validation($this);

                // Register extra extensions
                new Redux_Extensions($this);
                
                // Grab database values
                $this->options_class = new Redux_Options($this);
                $this->options_class->get();

                $this->output_class = new Redux_Output($this);
                $this->render_class = new Redux_Page_Render($this);
                $this->enqueue_class = new Redux_Enqueue($this);

                new Redux_Dev($this);
                new Redux_AJAX_Save($this);
                new Redux_AJAX_Select2($this);
            }

            /**
             * Loaded hook
             * action 'redux/loaded'
             *
             * @param  object $this ReduxFramework
             */
            do_action( 'redux/loaded', $this );
        }

        // Backward compatibility for extensions.
        public function _register_settings() {
            $this->options_class->register();
        }
        
        public function _field_input($field, $v = null) {
            $this->render_class->_field_input($field, $v);
        }

        public function field_default_values($field) {
            $this->options_class->field_default_values($field);
        }

        public function set_options($value){
            $this->options_class->set($value);
        }

        public function get_options(){
            $this->options_class->get();
        }
        
        public function _default_values(){
            if (!isset($this->options_class)) {
                $this->options_class = new Redux_Options($this);
            }
            
            return $this->options_class->_default_values();
        }

        public function check_dependencies($field) {
            $this->required_class->check_dependencies($field);
        }

        public function _enqueue_output() {
            $this->output_class->enqueue();
        }

        public function _enqueue() {
            $this->enqueue_class->init();
        }

        public function get_default_values($key, $array_key = false){
            return $this->options_class->get_default_values($key, $array_key);
        }

        public function get_wordpress_data( $type = false, $args = array() ) {
            return $this->wordpress_data->get($type, $args);
        }

        public function _validate_values( $plugin_options, $options, $sections ) {
            return $this->validate_class->validate($plugin_options, $options, $sections);
        }

        public function section_menu( $k, $section, $suffix = "", $sections = array() ) {
            return $this->render_class->section_menu ($k, $section, $suffix, $sections);
        }
        
        public function get_header_html($field) {
            return $this->render_class->get_header_html($field);
        }

        /**
         * @return ReduxFramework
         */
        public function get_instance() {
            return self::$instance;
        }

        /**
         * ->get(); This is used to return and option value from the options array
         *
         * @since       1.0.0
         * @access      public
         *
         * @param       string $opt_name The option name to return
         * @param       mixed  $default  (null) The value to return if option not set
         *
         * @return      mixed
         */
        public function get( $opt_name, $default = null ) {
            return ( ! empty ( $this->options[ $opt_name ] ) ) ? $this->options[ $opt_name ] : $this->options_class->_get_default( $opt_name, $default );
        }

        /**
         * ->set(); This is used to set an arbitrary option in the options array
         *
         * @since       1.0.0
         * @access      public
         *
         * @param       string $opt_name The name of the option being added
         * @param       mixed  $value    The value of the option being added
         *
         * @return      void
         */
        public function set( $opt_name = '', $value = '' ) {
            if ( $opt_name != '' ) {
                $this->options[ $opt_name ] = $value;
                $this->options_class->set( $this->options );
            }
        }
    }

    /**
     * action 'redux/init'
     *
     * @param null
     */
    do_action( 'redux/init', ReduxFramework::init() );
}
