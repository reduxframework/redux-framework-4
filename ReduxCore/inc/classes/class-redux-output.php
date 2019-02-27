<?php
/**
 * Redux Output Class
 *
 * @class Redux_Output
 * @version 3.0.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Output', false ) ) {

	/**
	 * Class Redux_Output
	 */
	class Redux_Output extends Redux_Class {

		/**
		 * Redux_Output constructor.
		 *
		 * @param object $parent ReduxFramewor pointer.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );

			// Output dynamic CSS.
			// Frontend: Maybe enqueue dynamic CSS and Google fonts.
			if ( empty( $this->args['output_location'] ) || in_array( 'frontend', $this->args['output_location'], true ) ) {
				add_action( 'wp_head', array( $this, 'output_css' ), 150 );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 150 );
			}

			// Login page: Maybe enqueue dynamic CSS and Google fonts.
			if ( in_array( 'login', $this->args['output_location'], true ) ) {
				add_action( 'login_head', array( $this, 'output_css' ), 150 );
				add_action( 'login_enqueue_scripts', array( $this, 'enqueue' ), 150 );
			}

			// Admin area: Maybe enqueue dynamic CSS and Google fonts.
			if ( in_array( 'admin', $this->args['output_location'], true ) ) {
				add_action( 'admin_head', array( $this, 'output_css' ), 150 );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 150 );
			}
		}

		/**
		 * Enqueue CSS and Google fonts for front end
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {
			$core = $this->core();

			if ( false === $core->args['output'] && false === $core->args['compiler'] ) {
				return;
			}

			foreach ( $core->sections as $k => $section ) {
				if ( isset( $section['type'] ) && ( 'divide' === $section['type'] ) ) {
					continue;
				}

				if ( isset( $section['fields'] ) ) {
					foreach ( $section['fields'] as $fieldk => $field ) {
						if ( isset( $field['type'] ) && 'callback' !== $field['type'] ) {
							$field_classes = array( 'Redux_' . $field['type'], 'ReduxFramework_' . $field['type'] );

							$field_class = Redux_Functions::class_exists_ex( $field_classes );

							if ( false === $field_class ) {
								if ( ! isset( $field['compiler'] ) ) {
									$field['compiler'] = '';
								}

								/**
								 * Field class file
								 * filter 'redux/{opt_name}/field/class/{field.type}
								 *
								 * @param       string        field class file
								 * @param array $field field config data
								 */
								$field_type = str_replace( '_', '-', $field['type'] );
								$core_path  = Redux_Core::$dir . "inc/fields/{$field['type']}/class-redux-{$field_type}.php";

								if ( ! file_exists( $core_path ) ) {
									$core_path = Redux_Core::$dir . "inc/fields/{$field['type']}/field_{$field['type']}.php";
								}

								if ( Redux_Core::$pro_loaded ) {
									$pro_path = Redux_Pro::$dir . "core/inc/fields/{$field['type']}/class-redux-{$field_type}.php";

									if ( file_exists( $pro_path ) ) {
										$filter_path = $pro_path;
									} else {
										$filter_path = $core_path;
									}
								} else {
									$filter_path = $core_path;
								}

								// phpcs:ignore WordPress.NamingConventions.ValidHookName
								$class_file = apply_filters( "redux/{$core->args['opt_name']}/field/class/{$field['type']}", $filter_path, $field );

								if ( $class_file && file_exists( $class_file ) && ( ! class_exists( $field_class ) || ! class_exists( $field_class_shim ) ) ) {
									require_once $class_file;

									$field_class = Redux_Functions::class_exists_ex( $field_classes );
								}
							}

							if ( ! empty( $core->options[ $field['id'] ] ) && class_exists( $field_class ) && method_exists( $field_class, 'output' ) && $this->can_output_css( $core, $field ) ) {

								// phpcs:ignore WordPress.NamingConventions.ValidHookName
								$field = apply_filters( "redux/field/{$core->args['opt_name']}/output_css", $field );

								if ( ! empty( $field['output'] ) && ! is_array( $field['output'] ) ) {
									$field['output'] = array( $field['output'] );
								}

								$value = isset( $core->options[ $field['id'] ] ) ? $core->options[ $field['id'] ] : '';

								$data = array(
									'field' => $field,
									'value' => $value,
									'core'  => $core,
									'mode'  => 'output',
								);

								Redux_Functions::load_pro_field( $data );

								$enqueue = new $field_class( $field, $value, $core );

								$style_data = '';

								if ( ( ( isset( $field['output'] ) && ! empty( $field['output'] ) ) || ( isset( $field['compiler'] ) && ! empty( $field['compiler'] ) ) || isset( $field['media_query'] ) && ! empty( $field['media_query'] ) || 'typography' === $field['type'] || 'icon_select' === $field['type'] ) ) {
									if ( method_exists( $enqueue, 'css_style' ) ) {
										$style_data = $enqueue->css_style( $enqueue->value );
									}
								}

								if ( ( ( isset( $field['output'] ) && ! empty( $field['output'] ) ) || ( isset( $field['compiler'] ) && ! empty( $field['compiler'] ) ) || 'typography' === $field['type'] || 'icon_select' === $field['type'] ) ) {
									$enqueue->output( $style_data );
								}

								if ( isset( $field['media_query'] ) && ! empty( $field['media_query'] ) ) {
									$enqueue->media_query( $style_data );
								}
							}
						}
					}
				}
			}

			// For use like in the customizer. Stops the output, but passes the CSS in the variable for the compiler.
			if ( isset( $core->no_output ) ) {
				return;
			}

			if ( ! empty( $core->typography ) && ! empty( $core->typography ) && filter_var( $core->args['output'], FILTER_VALIDATE_BOOLEAN ) ) {
				$version    = ! empty( $core->transients['last_save'] ) ? $core->transients['last_save'] : '';
				$typography = new Redux_Typography( null, null, $core );

				if ( $core->args['async_typography'] && ! empty( $core->typography ) ) {
					$families = array();

					foreach ( $core->typography as $key => $value ) {
						$families[] = $key;
					}
					?>
					<script>
						if ( typeof WebFontConfig === "undefined" ) {
							WebFontConfig = {};
						}

						WebFontConfig['google'] = {families: [<?php echo $typography->make_google_web_font_string( $core->typography ); // phpcs:ignore WordPress.Security.EscapeOutput ?>]};

						(function( d ) {
							var wf = d.createElement( 'script' );
							var s = d.scripts[0];
							wf.src = '//' + 'ajax' + '.googleapis' + '.com/ajax/libs/webfont/1.6.26/webfont.js';
							wf.async = true;
							s.parentNode.insertBefore( wf, s );
						})( document );
					</script>
					<?php
				} elseif ( ! $core->args['disable_google_fonts_link'] ) {
					$protocol = ( ! empty( Redux_Core::$server['HTTPS'] ) && 'off' !== Redux_Core::$server['HTTPS'] || 443 === Redux_Core::$server['SERVER_PORT'] ) ? 'https:' : 'http:';

					wp_enqueue_style(
						'redux-google-fonts-' . $core->args['opt_name'],
						$protocol . $typography->make_google_web_font_link( $core->typography ),
						array(),
						$version,
						'all'
					);
				}
			}
		}

		/**
		 * Output dynamic CSS at bottom of HEAD
		 *
		 * @since       3.2.8
		 * @access      public
		 * @return      void
		 */
		public function output_css() {
			$core = $this->core();

			if ( false === $core->args['output'] && false === $core->args['compiler'] ) {
				return;
			}

			if ( isset( $core->no_output ) ) {
				return;
			}

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName
			if ( ! empty( $core->outputCSS ) && ( true === $core->args['output_tag'] || ( isset( $_POST['customized'] ) && isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'preview-customize_' . wp_get_theme()->get_stylesheet() ) ) ) ) {

				// phpcs:ignore WordPress.NamingConventions.ValidVariableName, WordPress.Security.EscapeOutput
				echo '<style type="text/css" id="' . esc_attr( $core->args['opt_name'] ) . '-dynamic-css" title="dynamic-css" class="redux-options-output">' . $core->outputCSS . '</style>';
			}
		}

		/**
		 * Can Output CSS
		 * Check if a field meets its requirements before outputting to CSS
		 *
		 * @param object $core ReduxFramework core pointer.
		 * @param array  $field Field array.
		 *
		 * @return bool
		 */
		private function can_output_css( $core, $field ) {
			$return = true;

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$field = apply_filters( "redux/field/{$core->args['opt_name']}/_can_output_css", $field );
			if ( isset( $field['force_output'] ) && true === $field['force_output'] ) {
				return $return;
			}

			if ( ! empty( $field['required'] ) ) {
				if ( isset( $field['required'][0] ) ) {
					if ( ! is_array( $field['required'][0] ) && 3 === count( $field['required'] ) ) {
						$parent_value = isset( $GLOBALS[ $core->args['global_variable'] ][ $field['required'][0] ] ) ? $GLOBALS[ $core->args['global_variable'] ][ $field['required'][0] ] : '';
						$check_value  = $field['required'][2];
						$operation    = $field['required'][1];
						$return       = $core->required_class->compare_value_dependencies( $parent_value, $check_value, $operation );
					} elseif ( is_array( $field['required'][0] ) ) {
						foreach ( $field['required'] as $required ) {
							if ( ! is_array( $required[0] ) && 3 === count( $required ) ) {
								$parent_value = $GLOBALS[ $core->args['global_variable'] ][ $required[0] ];
								$check_value  = $required[2];
								$operation    = $required[1];
								$return       = $core->required_class->compare_value_dependencies( $parent_value, $check_value, $operation );
							}
							if ( ! $return ) {
								return $return;
							}
						}
					}
				}
			}

			return $return;
		}

	}

}
