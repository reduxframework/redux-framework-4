<?php // phpcs:ignore WordPress.Files.FileName

/**
 * Initialize the Redux Template Library.
 *
 * @since 4.0.0
 * @package Redux Framework
 */

namespace ReduxTemplates;

use ReduxTemplates;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Redux Templates Init Class
 *
 * @since 4.0.0
 */
class Init {

	/**
	 * Init constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load' ) );
		if ( did_action( 'init' ) ) { // In case the devs load it at the wrong place.
			$this->load();
		}
		// Editor Load.
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );
		// Admin Load.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	/**
	 * Load everything up after init.
	 *
	 * @access public
	 * @since 4.0.0
	 */
	public static function load() {
		new ReduxTemplates\API();
		new ReduxTemplates\Templates();
	}

	/**
	 * Get local contents of a file.
	 *
	 * @param string $file_path File path.
	 * @access public
	 * @since 4.0.0
	 * @return string
	 */
	public static function get_local_file_contents( $file_path ) {
		$fs = new ReduxTemplates\Filesystem();
		return $fs->get_contents( $file_path );
	}

	/**
	 * Load Editor Styles and Scripts.
	 *
	 * @access public
	 * @since 4.0.0
	 */
	public function editor_assets() {

		wp_enqueue_script(
			'redux-templates-js',
			plugins_url( 'assets/js/redux-templates.dev.js', REDUXTEMPLATES_FILE ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			REDUXTEMPLATES_VERSION,
			true
		);

		wp_set_script_translations( 'redux-templates-js', 'redux-templates' );

		// Backend editor scripts: common vendor files.
		wp_enqueue_script(
			'redux-templates-js-vendor',
			plugins_url( 'assets/js/vendor.dev.js', REDUXTEMPLATES_FILE ),
			array(),
			REDUXTEMPLATES_VERSION,
			true
		);

		$theme_details = wp_get_theme();
		$global_vars   = array(
			'i18n'              => 'redux-framework',
			'plugin'            => REDUXTEMPLATES_DIR_URL,
			'mokama'            => ReduxTemplates\Init::mokama(),
			'icon'              => self::get_local_file_contents( REDUXTEMPLATES_DIR_PATH . 'assets/img/logo.svg' ),
			'version'           => \Redux_Core::$version,
			'theme_name'        => $theme_details->get( 'Name' ),
			'supported_plugins' => array(), // Load the supported plugins.
		);

		if ( ! $global_vars['mokama'] ) {
			// phpcs:disable Squiz.PHP.CommentedOutCode
			// delete_user_meta( get_current_user_id(), '_redux_templates_count'); // To test left.
			$count = get_user_meta( get_current_user_id(), '_redux_templates_count', true );
			if ( empty( $count ) ) {
				$count = 5;
				update_user_meta( get_current_user_id(), '_redux_templates_count', $count );
			}
			$global_vars['left'] = $count;
		}

		if ( ! $global_vars['mokama'] ) {
			$global_vars['u'] = 'https://redux.io/pricing/&utm_source=plugin&utm_medium=modal&utm_campaign=template';
		}

		wp_localize_script(
			'redux-templates-js',
			'redux_templates',
			$global_vars
		);
		wp_enqueue_style(
			'redux-fontawesome',
			REDUXTEMPLATES_DIR_URL . 'assets/css/font-awesome.min.css',
			false,
			REDUXTEMPLATES_VERSION
		);
	}

	/**
	 * Check mokama.
	 *
	 * @access public
	 * @since 4.0.0
	 */
	public static function mokama() {
		return true;
	}

	/**
	 * Admin Style & Script.
	 *
	 * @access public
	 * @since 4.0.0
	 */
	public function admin_assets() {
		wp_enqueue_style(
			'redux-templates-bundle',
			REDUXTEMPLATES_DIR_URL . 'assets/css/admin.min.css',
			false,
			REDUXTEMPLATES_VERSION
		);
	}
}
