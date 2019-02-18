<?php
/**
 * Redux Field Class
 *
 * @class Redux_Field
 * @version 4.0.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Field', false ) ) {

	/**
	 * Class Redux_Field
	 */
	abstract class Redux_Field {

		/**
		 * CSS styling per field output/compiler.
		 *
		 * @var string
		 */
		public $style = null;

		/**
		 * Class dir.
		 *
		 * @var string
		 */
		public $_dir = null;

		/**
		 * Class URL.
		 *
		 * @var string
		 */
		public $_url = null;

		/**
		 * Timestamp for ver append in dev_mode
		 *
		 * @var string
		 */
		public $timestamp = null;

		/**
		 * ReduxFramework object pointer.
		 *
		 * @var ReduxFramework
		 */
		public $parent;

		/**
		 * Field values.
		 *
		 * @var string|array
		 */
		public $value;

		/**
		 * Redux_Field constructor.
		 *
		 * @param array  $field Field array.
		 * @param string $value Field values.
		 * @param null   $parent ReduxFramework object pointer.
		 *
		 * @throws ReflectionException Comment.
		 */
		public function __construct( $field = array(), $value = null, $parent = null ) {

			if ( isset( Redux_Core::$wp_nonce ) && ! empty( Redux_Core::$wp_nonce ) && ( ! Redux_Core::$_pro_loaded ) && Redux_Functions_Ex::metabox_boxes( $parent ) ) {
				if ( ! in_array( md5( $field['type'] ), Redux_Helpers::nonces(), true ) ) {
					return;
				}
			}

			$this->parent = $parent;
			$this->field  = $field;
			$this->value  = $value;

			$this->select2_config = array(
				'width'      => 'resolve',
				'allowClear' => false,
				'theme'      => 'default',
			);

			$this->set_defaults();

			$class_name = get_class( $this );
			$reflector  = new ReflectionClass( $class_name );
			$path       = $reflector->getFilename();
			$path_info  = Redux_Helpers::path_info( $path );
			$this->_dir = trailingslashit( dirname( $path_info['real_path'] ) );
			$this->_url = trailingslashit( dirname( $path_info['url'] ) );

			$this->timestamp = Redux_Core::$_version;
			if ( $parent->args['dev_mode'] ) {
				$this->timestamp .= '.' . time();
			}
		}

		/**
		 * Retrive dirname.
		 *
		 * @return string
		 */
		protected function get_dir() {
			return $this->_dir;
		}

		/**
		 * Media query compiler for Redux Pro,
		 *
		 * @param string $style_data CSS string.
		 */
		public function media_query( $style_data = '' ) {
			$query_arr = $this->field['media_query'];
			$css       = '';

			if ( isset( $query_arr['queries'] ) ) {
				foreach ( $query_arr['queries'] as $idx => $query ) {
					$rule      = isset( $query['rule'] ) ? $query['rule'] : '';
					$selectors = isset( $query['selectors'] ) ? $query['selectors'] : array();

					if ( ! is_array( $selectors ) && '' !== $selectors ) {
						$selectors = array( $selectors );
					}

					if ( '' !== $rule && ! empty( $selectors ) ) {
						$selectors = implode( ',', $selectors );

						$css .= '@media ' . $rule . '{';
						$css .= $selectors . '{' . $style_data . '}';
						$css .= '}';
					}
				}
			} else {
				return;
			}

			if ( isset( $query_arr['output'] ) && $query_arr['output'] ) {
				$this->parent->outputCSS .= $css;
			}

			if ( isset( $query_arr['compiler'] ) && $query_arr['compiler'] ) {
				$this->parent->compilerCSS .= $css;
			}
		}

		/**
		 * CSS for field output, if set.
		 *
		 * @param string $style CSS string.
		 */
		public function output( $style = '' ) {
			if ( '' !== $style ) {
				if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
					$keys                     = implode( ',', $this->field['output'] );
					$this->parent->outputCSS .= $keys . '{' . $style . '}';
				}

				if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
					$keys                       = implode( ',', $this->field['compiler'] );
					$this->parent->compilerCSS .= $keys . '{' . $style . '}';
				}
			}
		}

		/**
		 * Unused for now.
		 *
		 * @param string $data CSS data.
		 */
		public function css_style( $data ) {

		}

		/**
		 * Unused for now.
		 */
		public function set_defaults() {

		}

		/**
		 * Unused for now.
		 */
		public function render() {

		}

		/**
		 * Unused for now.
		 */
		public function enqueue() {

		}

		/**
		 * Unused for now.
		 *
		 * @param array  $field Field array.
		 * @param string $value Value array.
		 */
		public function localize( $field, $value = '' ) {

		}
	}
}
