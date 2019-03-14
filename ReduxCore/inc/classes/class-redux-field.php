<?php
/**
 * Redux Field Class
 *
 * @class Redux_Field
 * @version 4.0.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;
use Redux_Descriptor_Types as RDT;
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
		public $dir = null;

		/**
		 * Class URL.
		 *
		 * @var string
		 */
		public $url = null;

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
		 * Array of field descriptors.
		 *
		 * @var array
		 */
		public static $descriptors = array();

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

			if ( isset( Redux_Core::$wp_nonce ) && ! empty( Redux_Core::$wp_nonce ) && ( ! Redux_Core::$pro_loaded ) && Redux_Functions_Ex::metabox_boxes( $parent ) ) {
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

			$this->timestamp = Redux_Core::$version;
			if ( $parent->args['dev_mode'] ) {
				$this->timestamp .= '.' . time();
			}
		}

		/**
		 * Make base descriptor.
		 *
		 * @return Redux_Descriptor_Field
		 */
		public static function make_base_descriptor() {
			$d                                       = new Redux_Descriptor_Field( get_called_class() );
			self::$descriptors[ get_called_class() ] = $d;

			$d->add_field( 'id', __( 'Field ID' ), RDT::TEXT )->set_order( 0 )->set_required();

			$d->add_field( 'required', null, RDT::BOOL, __( 'Should the field be required' ), false )->set_order( 1 );

			$d->add_field( 'readonly', null, RDT::BOOL, __( 'Should the field be readonly' ), false )->set_order( 20 );

			$d->add_field( 'compiler', __( 'CSS Compiler' ), RDT::BOOL, __( 'Should the field be sent to the compiler' ), false )->set_order( 60 );

			$d->add_field( 'output', __( 'CSS Output' ), RDT::BOOL, '', false );

			return $d;
		}

		/**
		 * Get descriptor.
		 *
		 * @return Redux_Descriptor_Field
		 */
		public static function get_descriptor() {
			static::make_descriptor();
			$d = self::$descriptors[ get_called_class() ];



			// This part is out of opt name because it's non vendor dependant!
			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			return apply_filters( 'redux/field/' . $d->get_field_type() . '/get_descriptor', $d );
		}

		/**
		 * Build the field descriptor in this function.
		 */
		public static function make_descriptor() {
			static::make_base_descriptor();
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
