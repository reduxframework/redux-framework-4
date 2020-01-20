<?php
/**
 * Link Color Field.
 *
 * @package     ReduxFramework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'Redux_Link_Color', false ) ) {

	/**
	 * Main Redux_link_color class
	 *
	 * @since       1.0.0
	 */
	class Redux_Link_Color extends Redux_Field {

		/*
		 * Pattern for CSS output
		 */
		public $output_formatting = array(
			'default_key_pattern' => ':key',
			'default_pattern'     => 'color:value'
		);

		/**
		 * Set field and value defaults.
		 */
		public function set_defaults() {
			$defaults = array(
				'regular' => true,
				'hover'   => true,
				'visited' => false,
				'active'  => true,
				'focus'   => false,
			);

			$this->field = wp_parse_args( $this->field, $defaults );

			$defaults = array(
				'regular' => '',
				'hover'   => '',
				'visited' => '',
				'active'  => '',
				'focus'   => '',
			);

			$this->value = wp_parse_args( $this->value, $defaults );

			foreach ( $this->value as $k => $v ) {
				if ( ! empty( $v ) && "#" !== $v[0] ) {
					$this->value[ $k ] = Redux_Colors::sanitize_hex( $v );
				}
			}

			// In case user passes no default values.
			if ( isset( $this->field['default'] ) ) {
				$this->field['default'] = wp_parse_args( $this->field['default'], $defaults );
				foreach ( $this->field['default'] as $k => $v ) {
					if ( ! empty( $v ) && "#" !== $v[0] ) {
						$this->field['default'][ $k ] = Redux_Colors::sanitize_hex( $v );
					}
				}
			} else {
				$this->field['default'] = $defaults;
			}
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings.
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			if ( true === $this->field['regular'] && false !== $this->field['default']['regular'] ) {
				echo '<span class="linkColor">';
				echo '<strong>' . esc_html__( 'Regular', 'redux-framework' ) . '</strong>&nbsp;';
				echo '<input ';
				echo 'id="' . esc_attr( $this->field['id'] ) . '-regular" ';
				echo 'name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[regular]"';
				echo 'value="' . esc_attr( $this->value['regular'] ) . '"';
				echo 'class="color-picker redux-color redux-color-regular redux-color-init ' . esc_attr(
						$this->field['class']
					) . '"';
				echo 'type="text"';
				echo 'data-default-color="' . esc_attr( $this->field['default']['regular'] ) . '"';

				if ( Redux_Core::$pro_loaded ) {
					$data = array(
						'field' => $this->field,
						'index' => 'regular',
					);

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					echo esc_html( apply_filters( 'redux/pro/render/color_alpha', $data ) );
				}

				echo '/>';
				echo '</span>';
			}

			if ( true === $this->field['hover'] && false !== $this->field['default']['hover'] ) {
				echo '<span class="linkColor">';
				echo '<strong>' . esc_html__( 'Hover', 'redux-framework' ) . '</strong>&nbsp;';
				echo '<input ';
				echo 'id="' . esc_attr( $this->field['id'] ) . '-hover"';
				echo 'name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[hover]"';
				echo 'value="' . esc_attr( $this->value['hover'] ) . '"';
				echo 'class="color-picker redux-color redux-color-hover redux-color-init ' . esc_attr(
						$this->field['class']
					) . '"';
				echo 'type="text"';
				echo 'data-default-color="' . esc_attr( $this->field['default']['hover'] ) . '"';

				if ( Redux_Core::$pro_loaded ) {
					$data = array(
						'field' => $this->field,
						'index' => 'hover',
					);

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					echo esc_html( apply_filters( 'redux/pro/render/color_alpha', $data ) );
				}

				echo '/>';
				echo '</span>';
			}

			if ( true === $this->field['visited'] && false !== $this->field['default']['visited'] ) {
				echo '<span class="linkColor">';
				echo '<strong>' . esc_html__( 'Visited', 'redux-framework' ) . '</strong>&nbsp;';
				echo '<input ';
				echo 'id="' . esc_attr( $this->field['id'] ) . '-visited"';
				echo 'name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[visited]"';
				echo 'value="' . esc_attr( $this->value['visited'] ) . '"';
				echo 'class="color-picker redux-color redux-color-visited redux-color-init ' . esc_attr(
						$this->field['class']
					) . '"';
				echo 'type="text"';
				echo 'data-default-color="' . esc_attr( $this->field['default']['visited'] ) . '"';

				if ( Redux_Core::$pro_loaded ) {
					$data = array(
						'field' => $this->field,
						'index' => 'visited',
					);

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					echo esc_html( apply_filters( 'redux/pro/render/color_alpha', $data ) );
				}

				echo '/>';
				echo '</span>';
			}

			if ( true === $this->field['active'] && false !== $this->field['default']['active'] ) {
				echo '<span class="linkColor">';
				echo '<strong>' . esc_html__( 'Active', 'redux-framework' ) . '</strong>&nbsp;';
				echo '<input ';
				echo 'id="' . esc_attr( $this->field['id'] ) . '-active"';
				echo 'name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[active]"';
				echo 'value="' . esc_attr( $this->value['active'] ) . '"';
				echo 'class="color-picker redux-color redux-color-active redux-color-init ' . esc_attr(
						$this->field['class']
					) . '"';
				echo 'type="text"';
				echo 'data-default-color="' . esc_attr( $this->field['default']['active'] ) . '"';

				if ( Redux_Core::$pro_loaded ) {
					$data = array(
						'field' => $this->field,
						'index' => 'active',
					);

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					echo esc_html( apply_filters( 'redux/pro/render/color_alpha', $data ) );
				}

				echo '/>';
				echo '</span>';
			}

			if ( true === $this->field['focus'] && false !== $this->field['default']['focus'] ) {
				echo '<span class="linkColor">';
				echo '<strong>' . esc_html__( 'Focus', 'redux-framework' ) . '</strong>&nbsp;';
				echo '<input ';
				echo 'id="' . esc_attr( $this->field['id'] ) . '-focus"';
				echo 'name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[focus]"';
				echo 'value="' . esc_attr( $this->value['focus'] ) . '"';
				echo 'class="color-picker redux-color redux-color-focus redux-color-init ' . esc_attr(
						$this->field['class']
					) . '"';
				echo 'type="text"';
				echo 'data-default-color="' . esc_attr( $this->field['default']['focus'] ) . '"';

				if ( Redux_Core::$pro_loaded ) {
					$data = array(
						'field' => $this->field,
						'index' => 'focus',
					);

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					echo esc_html( apply_filters( 'redux/pro/render/color_alpha', $data ) );
				}

				echo '/>';
				echo '</span>';
			}
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {
			wp_enqueue_style( 'wp-color-picker' );

			$dep_array = array( 'jquery', 'wp-color-picker', 'redux-js' );

			wp_enqueue_script(
				'redux-field-link-color-js',
				Redux_Core::$url . 'inc/fields/link_color/redux-link-color' . Redux_Functions::is_min() . '.js',
				$dep_array,
				$this->timestamp,
				true
			);

			if ( Redux_Core::$pro_loaded ) {
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( 'redux/pro/enqueue/color_alpha', $this->field );
			}

			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style( 'redux-color-picker-css' );

				wp_enqueue_style(
					'redux-field-link_color-js',
					Redux_Core::$url . 'inc/fields/link_color/redux-link-color.css',
					array(),
					$this->timestamp,
					'all'
				);
			}
		}

		/**
		 * Enable output_variables to be generated.
		 *
		 * @since       4.0.3
		 * @return void
		 */
		public function output_variables() {
			// No code needed, just defining the method is enough.
		}
	}
}

class_alias( 'Redux_Link_Color', 'ReduxFramework_Link_Color' );
