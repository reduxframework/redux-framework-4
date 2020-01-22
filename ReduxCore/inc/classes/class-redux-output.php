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
		 * @param     object     $parent ReduxFramewor pointer.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );

			// Output dynamic CSS.
			// Frontend: Maybe enqueue dynamic CSS and Google fonts.
			if ( empty( $this->args['output_location'] ) || in_array(
					'frontend', $this->args['output_location'], true
				) ) {
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

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			do_action( "redux/output/{$this->parent->args['opt_name']}/construct", $this );
			// Useful for adding different locations for CSS output.
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
								 * @param     string        field class file
								 * @param     array     $field field config data
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
								$class_file = apply_filters(
									"redux/{$core->args['opt_name']}/field/class/{$field['type']}", $filter_path, $field
								);

								if ( $class_file && file_exists( $class_file ) && ( ! class_exists( $field_class ) ) ) {
									require_once $class_file;

									$field_class = Redux_Functions::class_exists_ex( $field_classes );
								}
							}

							$field['default'] = isset( $field['default'] ) ? $field['default'] : '';
							$value            = isset( $core->options[ $field['id'] ] ) ? $core->options[ $field['id'] ] : $field['default'];

							$style_data = '';
							$data       = array(
								'field' => $field,
								'value' => $value,
								'core'  => $core,
								'mode'  => 'output',
							);

							Redux_Functions::load_pro_field( $data );
							$field_object = new $field_class( $field, $value, $core );

							if ( ! empty( $core->options[ $field['id'] ] ) && class_exists(
									$field_class
								) && $this->can_output_css(
									$core, $field
								) ) {
								// phpcs:ignore WordPress.NamingConventions.ValidHookName
								$field = apply_filters( "redux/field/{$core->args['opt_name']}/output_css", $field );

								if ( ! empty( $field['output'] ) && ! is_array( $field['output'] ) ) {
									$field['output'] = array( $field['output'] );
								}

								if ( ( ( isset( $field['css'] ) && ! empty( $field['css'] ) ) || ( isset( $field['output'] ) && ! empty( $field['output'] ) ) || ( isset( $field['compiler'] ) && ! empty( $field['compiler'] ) ) || isset( $field['media_query'] ) && ! empty( $field['media_query'] ) || 'typography' === $field['type'] || 'icon_select' === $field['type'] ) ) {
									$style_data = "";
									if ( method_exists(
											 $field_object, 'css_style'
										 ) && ! empty( $field_object->value ) ) {
										$style_data = $field_object->css_style( $field_object->value );
									}
								}


								if ( ! empty( $style_data ) ) {
									if ( ( ( isset( $field['output'] ) && ! empty( $field['output'] ) ) || ( isset( $field['compiler'] ) && ! empty( $field['compiler'] ) ) || 'typography' === $field['type'] || 'icon_select' === $field['type'] ) ) {
										$field_object->output( $style_data );
									}
									if ( isset( $field['media_query'] ) && ! empty( $field['media_query'] ) ) {
										$field_object->media_query( $style_data );
									}
								}
							}

							// phpcs:ignore WordPress.NamingConventions.ValidHookName
							do_action(
								"redux/field/{$core->args['opt_name']}/output_loop", $core, $field, $value, $style_data
							);
							// phpcs:ignore WordPress.NamingConventions.ValidHookName
							do_action(
								"redux/field/{$core->args['opt_name']}/output_loop/{$field['type']}", $core, $field,
								$value, $style_data
							);

							if ( method_exists( $field_class, 'output_variables' ) && $this->can_output_css(
									$core, $field
								) ) {
								$css               = Redux_Output::parse_css( $field['output'], $style_data );
								$passed_style_data = $field_object->output_variables( $css );
								$this->output_variables( $core, $section, $field, $value, $passed_style_data );
							}
						}
					}
				}
			}

			// For use like in the customizer. Stops the output, but passes the CSS in the variable for the compiler.
			if ( isset( $core->no_output ) ) {
				return;
			}

			if ( ! empty( $core->typography ) && ! empty( $core->typography ) && filter_var(
					$core->args['output'], FILTER_VALIDATE_BOOLEAN
				) ) {
				$version    = ! empty( $core->transients['last_save'] ) ? $core->transients['last_save'] : '';
				$typography = new Redux_Typography( null, null, $core );

				if ( $core->args['async_typography'] && ! empty( $core->typography ) ) {
					$families = array();

					foreach ( $core->typography as $key => $value ) {
						$families[] = $key;
					}
					?>
                    <script>
                        if (typeof WebFontConfig === "undefined") {
                            WebFontConfig = {};
                        }

                        WebFontConfig['google'] = {
                            families: [<?php echo $typography->make_google_web_font_string(
								$core->typography
							); // phpcs:ignore WordPress.Security.EscapeOutput ?>]
                        };

                        (function (d) {
                            var wf = d.createElement('script');
                            var s = d.scripts[0];
                            wf.src = '//' + 'ajax' + '.googleapis' + '.com/ajax/libs/webfont/1.6.26/webfont.js';
                            wf.async = true;
                            s.parentNode.insertBefore(wf, s);
                        })(document);
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
		 * Function to output output_variables to the dynamic output.
		 *
		 * @since       4.0.3
		 * @access      public
		 *
		 * @param     array     $core ReduxFramework core pointer.
		 * @param     array     $section Section containing this field.
		 * @param     array     $field Field object.
		 * @param     array     $value Current value of field.
		 * @param     string     $style_data CSS output string to append to the root output variable.
		 *
		 * @return      void
		 */
		private function output_variables( $core = array(), $section = array(), $field = array(), $value = array(), $style_data = '' ) {
			// Let's allow section overrides please.
			if ( isset( $section['output_variables'] ) && ! isset( $field['output_variables'] ) ) {
				$field['output_variables'] = $section['output_variables'];
			}
			if ( isset( $section['output_variables_prefix'] ) && ! isset( $field['output_variables_prefix'] ) ) {
				$field['output_variables_prefix'] = $section['output_variables_prefix'];
			}

			if ( isset( $field['output_variables'] ) && $field['output_variables'] ) {

				$output_variables_prefix = $core->args['output_variables_prefix'];
				if ( isset( $field['output_variables_prefix'] ) && ! empty( $field['output_variables_prefix'] ) ) {
					$output_variables_prefix = $field['output_variables_prefix'];
				} elseif ( isset( $section['output_variables_prefix'] ) && ! empty( $section['output_variables_prefix'] ) ) {
					$output_variables_prefix = $section['output_variables_prefix'];
				}

				if ( is_array( $value ) ) {
					$val_pieces = array_filter( $value, 'strlen' );
					// We don't need to show the Google boolean.
					if ( 'typography' === $field['type'] && isset( $val_pieces['google'] ) ) {
						unset( $val_pieces['google'] );
					}

					foreach ( $val_pieces as $val_key => $val_val ) {
						$val_key                            = $output_variables_prefix . sanitize_title_with_dashes(
								$field['id']
							) . '-' . $val_key;
						$core->output_variables[ $val_key ] = $val_val;
						if ( ! empty( $style_data ) ) {
							$val_key                            = $output_variables_prefix . sanitize_title_with_dashes(
									$field['id']
								);
							$core->output_variables[ $val_key ] = $style_data;
						}
					}
				} else {
					if ( ! empty( $style_data ) ) {
						$val_key                            = $output_variables_prefix . sanitize_title_with_dashes(
								$field['id']
							);
						$core->output_variables[ $val_key ] = $style_data;
					} else {
						$val_key                            = $output_variables_prefix . sanitize_title_with_dashes(
								$field['id']
							);
						$core->output_variables[ $val_key ] = $value;
					}
				}
			}
		}

		public static function replace_css_variables( $source_string, $field_object = array(), $allow_global = true ) {
			$re = '/value[^\s"\'),]+|"([^"]*)"|\'([^\']*)\'/';
			preg_match_all( $re, $source_string, $matches, PREG_SET_ORDER, 0 );
			$output_format = $source_string;

			foreach ( $matches as $key_match ) {
				$key_match = $key_match[0];
				if ( $key_match === 'value' ) {
					continue;
				}
				$key_test = substr( $key_match, strlen( 'value-' ) );

				if ( isset( $field_object->value[ $key_test ] ) ) {
					$output_format = str_replace( $key_match, $field_object->value[ $key_test ], $output_format );
				} elseif ( $allow_global == true && isset( $field_object->parent->options[ $key_test ] ) ) {
					$output_format = str_replace(
						$key_match, $field_object->parent->options[ $key_test ], $output_format
					);
				} else {
					$output_format = str_replace( $key_match, '', $output_format );
				}
			}

			return $output_format;
		}

		public static function css_style( $field, $field_object ) {

			// This allows us to override any arg-based methods for this field type
			if ( method_exists( $field_object, 'output_formatting_properties' ) ) {
				$field_object->output_formatting_properties();
			}
			$output_formatting = $field_object->output_formatting;
			$value_keys        = array();
			if ( isset( $field_object->value ) && ! empty( $field_object->value ) ) {
				if ( is_array( $field_object->value ) ) {
					$field_object->value = array_filter( $field_object->value );
					if ( isset( $output_formatting['default_pattern'] ) ) {
						$output_formatting['default_pattern_replaced'] = self::replace_css_variables(
							$output_formatting['default_pattern'],
							$field_object,
							false
						);
					}

					foreach ( $field_object->value as $key => $value ) {
						if ( empty( $value ) || ! $value ) {
							continue;
						}
						// We don't want CSS output for excluded keys, they'll be used elsewhere.
						if ( isset(
								 $output_formatting['excludes']
							 ) && ! empty(
							$output_formatting['excludes']
							) && in_array(
								 $key, $output_formatting['excludes']
							 ) ) {
							continue;
						}
						if ( isset( $output_formatting['value_transforms'] ) && isset( $output_formatting['value_transforms'][ $key ] ) ) {
							$value = str_replace(
								'value', $value,
								$output_formatting['value_transforms'][ $key ]
							);
						}
						if ( isset( $output_formatting['key_transforms'] ) && isset( $output_formatting['key_transforms'][ $key ] ) ) {
							$key = str_replace(
								'key', $key,
								$output_formatting['key_transforms'][ $key ]
							);
						}
						if ( isset( $output_formatting['default_key_pattern'] ) ) {
							$key = str_replace(
								'key', $key,
								$output_formatting['default_key_pattern']
							);
						}
						// Do this AFTER value replacements!!!
						if ( isset( $output_formatting['default_pattern'] ) ) {
							$value = str_replace(
								'value', $value,
								$output_formatting['default_pattern_replaced']
							);
							if ( isset(
									 $field_object->value['units']
								 ) && strpos(
										  $value,
										  $field_object->value['units']
									  ) !== false ) {
								$value = str_replace(
									' ' . $field_object->value['units'], $field_object->value['units'], $value
								);
							}
						}

						if ( isset( $output_formatting['merge_key'] ) && count(
																			 array_unique(
																				 array_values( $field_object->value )
																			 )
																		 ) === 1 ) {

							$value_keys[ $output_formatting['merge_key'] ] = $value;
							break;
						}
						$value_keys[ $key ] = $value;
					}

					if ( isset(
							 $output_formatting['merge_key']
						 ) && count(
								  array_unique( array_values( $value_keys ) )
							  ) === 1 ) {
						$value_keys = array( $output_formatting['merge_key'] => min( $value_keys ) );
					}
					if ( count( $value_keys ) === 1 && isset( $output_formatting['default_key'] ) ) {
						if ( isset( $output_formatting['default_pattern_replaced'] ) ) {
							$value_keys = array( $output_formatting['default_key'] => $output_formatting['default_pattern_replaced'] );
						} else {
							$key        = min( array_keys( $value_keys ) );
							$value_keys = array( $output_formatting['default_key'] => $value_keys[ $key ] );
						}
					}
				} else {
					$value = $field_object->value;
					if ( isset( $output_formatting['default_key'] ) ) {
						$value_keys = array( $output_formatting['default_key'] => $value );
					} else {
						$value_keys = array( $value );
					}
				}
			}


			return $value_keys;

		}

		/**
		 * Parse CSS from output/compiler array
		 *
		 * @since       3.2.8
		 * @access      private
		 *
		 * @param     array     $css_array CSS data.
		 * @param     string     $style CSS style.
		 * @param     string     $value CSS values.
		 *
		 * @return string CSS string
		 */
		public static function parse_css( $selector_array = array(), $style = false ) {

			// Something wrong happened.
			if ( empty( $selector_array ) ) {
				return '';
			}

			$append_to_selector = false;
			foreach ( $style as $k => $v ) {
				if ( false !== strpos( $k, ':' ) ) {
					$append_to_selector = true;
				}
			}
			$style_string = "";
			foreach ( $style as $k => $v ) {
				$style_string .= $k . ':' . $v . ';';
			}
			if ( ! $append_to_selector ) {
				# Single Selectors that can be compressed
				if ( 0 === min( array_keys( $selector_array ) ) ) {
					$keys = implode( ',', $selector_array );
					$css  = $keys . '{' . $style_string . '}';

					return $css;
				}
			}

			$css = '';
			foreach ( $selector_array as $element => $selector ) {
				$style_string_loop = $style_string;

				if ( ! is_numeric( $element ) ) {
					if ( 1 === count( $style ) ) {
						$style_string_loop = $element . ':' . min( array_values( $style ) ) . ';';
					}
					$element = $selector;
				}

				if ( $append_to_selector ) {
					foreach ( $style as $k => $v ) {
						if ( ':' === $k[0] ) {
							$css .= $element . $k . '{' . $v . ';}';
						} else {
							$css .= $element . '{' . $style_string_loop . '}';
						}
					}
				} else {
					$css .= $selector . '{' . $style_string_loop . '}';
				}
			}


			return $css;
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

			if ( false === $core->args['output'] && false === $core->args['compiler'] && empty( $core->output_variables ) ) {
				return;
			}

			if ( isset( $core->no_output ) ) {
				return;
			}

			if ( ! empty( $core->output_variables ) ) {
				$root_css = ':root{';
				foreach ( $core->output_variables as $key => $value ) {
					$root_css .= "{$key}:{$value};";
				}
				$root_css .= '}';
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName, WordPress.Security.EscapeOutput
				$core->outputCSS = $root_css . $core->outputCSS;
			}

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName
			if ( ! empty( $core->outputCSS ) && ( true === $core->args['output_tag'] || ( isset( $_POST['customized'] ) && isset( $_POST['nonce'] ) && wp_verify_nonce(
							sanitize_text_field( wp_unslash( $_POST['nonce'] ) ),
							'preview-customize_' . wp_get_theme()->get_stylesheet()
						) ) ) ) {
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName, WordPress.Security.EscapeOutput
				echo '<style type="text/css" id="' . esc_attr(
						$core->args['opt_name']
					) . '-dynamic-css" title="dynamic-css" class="redux-options-output">' . $core->outputCSS . '</style>';
			}
		}

		/**
		 * Can Output CSS
		 * Check if a field meets its requirements before outputting to CSS
		 *
		 * @param     object     $core ReduxFramework core pointer.
		 * @param     array     $field Field array.
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
						$return       = $core->required_class->compare_value_dependencies(
							$parent_value, $check_value, $operation
						);
					} elseif ( is_array( $field['required'][0] ) ) {
						foreach ( $field['required'] as $required ) {
							if ( ! is_array( $required[0] ) && 3 === count( $required ) ) {
								$parent_value = $GLOBALS[ $core->args['global_variable'] ][ $required[0] ];
								$check_value  = $required[2];
								$operation    = $required[1];
								$return       = $core->required_class->compare_value_dependencies(
									$parent_value, $check_value, $operation
								);
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
