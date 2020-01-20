<?php
/**
 * Redux Field Class
 *
 * @class Redux_Field
 * @version 4.0.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

use Redux_Descriptor_Types as RDT; // TODO Require instead!

if ( ! class_exists( 'Redux_Field', false ) ) {

	/**
	 * Class Redux_Field
	 */
	abstract class Redux_Field {

		/**
		 * Array of descriptors.
		 *
		 * @var Redux_Descriptor[]
		 */
		public static $descriptors = array();

		/**
		 * Make base descriptor.
		 *
		 * @return Redux_Descriptor
		 */
		public static function make_base_descriptor() {
			$d                                       = new Redux_Descriptor( get_called_class() );
			self::$descriptors[ get_called_class() ] = $d;

			$d->add_field( 'id', __( 'Field ID', 'redux-framework' ), RDT::TEXT )->set_order( 0 )->set_required();
			$d->add_field( 'title', __( 'Title', 'redux-framework' ), RDT::TEXT, '' )->set_order( 1 );
			$d->add_field( 'subtitle', __( 'Subtitle', 'redux-framework' ), RDT::TEXT, '' )->set_order( 2 );
			$d->add_field( 'desc', __( 'Description', 'redux-framework' ), RDT::TEXT, '' )->set_order( 3 );
			$d->add_field( 'class', __( 'Class', 'redux-framework' ), RDT::TEXT, '' )->set_order( 3 );
			$d->add_field( 'compiler', __( 'Compiler', 'redux-framework' ), RDT::BOOL, '', false )->set_order( 60 );
			$d->add_field( 'default', __( 'Default' ), RDT::OPTIONS, '', false )->set_order( 60 );
			$d->add_field( 'disabled', __( 'Disabled', 'redux-framework' ), RDT::BOOL, '', false )->set_order( 60 );
			$d->add_field( 'hint', __( 'Hint', 'redux-framework' ), RDT::OPTIONS, '', false )->set_order( 60 );
			$d->add_field( 'hint', __( 'Permissions', 'redux-framework' ), RDT::OPTIONS, '', false )->set_order( 60 );
			$d->add_field( 'required', __( 'Required', 'redux-framework' ), RDT::BOOL, '', false )->set_order( 60 );

			return $d;
		}

		/**
		 * Renders an attribute array into an html attributes string.
		 *
		 * @param     array     $attributes HTML attributes.
		 *
		 * @return string
		 */
		public static function render_attributes( $attributes = array() ) {
			$output = '';

			if ( empty( $attributes ) ) {
				return $output;
			}

			foreach ( $attributes as $key => $value ) {
				if ( false === $value || '' === $value ) {
					continue;
				}

				if ( is_array( $value ) ) {
					$value = wp_json_encode( $value );
				}

				$output .= sprintf( true === $value ? ' %s' : ' %s="%s"', $key, esc_attr( $value ) );
			}

			return $output;
		}

		/**
		 * Get descriptor.
		 *
		 * @return Redux_Descriptor
		 */
		public static function get_descriptor() {
			if ( ! isset( static::$descriptors[ get_called_class() ] ) ) {
				static::make_descriptor();
			}

			$d = self::$descriptors[ get_called_class() ];

			static::make_descriptor();

			// This part is out of opt name because it's non vendor dependant!
			return apply_filters(
				'redux/field/' . $d->get_field_type() . '/get_descriptor', $d
			); // phpcs:ignore WordPress.NamingConventions.ValidHookName
		}

		/**
		 * Build the field descriptor in this function.
		 */
		public static function make_descriptor() {
			static::make_base_descriptor();
		}


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
		 * Redux_Field constructor.
		 *
		 * @param     array     $field Field array.
		 * @param     string     $value Field values.
		 * @param     null     $parent ReduxFramework object pointer.
		 *
		 * @throws ReflectionException Comment.
		 */
		public function __construct( $field = array(), $value = null, $parent = null ) {
			/*
			TODO - Fix me!
			*/
			if ( isset( Redux_Core::$wp_nonce ) && ! empty( Redux_Core::$wp_nonce ) && ( ! Redux_Core::$pro_loaded ) && Redux_Functions_Ex::metabox_boxes(
					$parent
				) ) {
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
			$this->dir  = trailingslashit( dirname( $path_info['real_path'] ) );
			$this->url  = trailingslashit( dirname( $path_info['url'] ) );

			$this->timestamp = Redux_Core::$version;
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
			return $this->dir;
		}

		/**
		 * Media query compiler for Redux Pro,
		 *
		 * @param     string     $style_data CSS string.
		 */
		public function media_query( $style_data = '' ) {
			if ( is_customize_preview() ) {
				return;
			}

			$query_arr = $this->field['media_query'];
			$css       = '';

			if ( isset( $query_arr['queries'] ) ) {

				foreach ( $query_arr['queries'] as $idx => $query ) {
					echo "words";
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
		 * @param     string     $style CSS string.
		 */
		public function output( $style_data = '' ) {

			if ( is_customize_preview() ) {
				return;
			}


			if ( ! empty( $style_data ) ) {
				if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
					$css                     = Redux_Functions::parse_css( $this->field['output'], $style_data );
//					if (!in_array($this->field['type'], array('spacing', 'border'))) {
//						echo PHP_EOL.'===> '.$this->field['id'].' => '.$this->field['type'].PHP_EOL;
//						print_r($style_data);
//						echo PHP_EOL;
//						print_r($this->value);
//						echo PHP_EOL;
//						print_r($this->field['output']);
//						echo PHP_EOL.$css.PHP_EOL;
//					}


					$this->parent->outputCSS .= $css;
				}

				if ( ! empty( $this->field['compiler'] ) ) {
					if ( is_array( $this->field['compiler'] ) ) {
						$css = Redux_Functions::parse_css( $this->field['compiler'], $style_data );
					} elseif ( ! empty( $field['output'] ) ) {
						$css = Redux_Functions::parse_css( $this->field['compiler'], $style_data );
					}

					if ( ! empty( $css ) ) {
						$this->parent->compilerCSS .= $css;
					}
				}
			}
		}

		/**
		 * Unused for now.
		 *
		 * @param     string     $data CSS data.
		 *
		 * @return array All style data
		 */
		public function css_style( $data ) {

			return Redux_Output::css_style( $this->field, $this );

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
		 * @param     array     $field Field array.
		 * @param     string     $value Value array.
		 */
		public function localize( $field, $value = '' ) {

		}
	}
}
