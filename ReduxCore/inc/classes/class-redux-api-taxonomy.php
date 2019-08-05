<?php

/**
 * Redux Framework Taxonomy API Class
 * Makes instantiating a Redux object an absolute piece of cake.
 *
 * @package     Redux_Framework
 * @author      Dovy Paukstys
 * @subpackage  Taxonomy
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'Redux_Taxonomy' ) ) {

	/**
	 * Redux Taxonomy API Class
	 * Simple API for Redux Framework
	 *
	 * @since       1.0.0
	 */
	class Redux_Taxonomy {

		public static $terms = array();
		public static $sections = array();
		public static $fields = array();
		public static $priority = array();
		public static $errors = array();
		public static $init = array();
		public static $hasRun = false;
		public static $args = array();

		public static function load() {
			add_action( 'init', array( 'Redux_Taxonomy', '_enqueue' ), 99 );
		}

		public static function _enqueue() {

			// Check and run instances of Redux where the opt_name hasn't been run.
			global $pagenow;
			$pagenows = array( 'edit-tags.php', 'term.php', 'admin-ajax.php' );
			if ( ! empty( Redux_Taxonomy::$sections ) && in_array( $pagenow, $pagenows ) ) {
				$instances = ReduxFrameworkInstances::get_all_instances();
				foreach ( Redux_Taxonomy::$fields as $opt_name => $fields ) {
					if ( ! isset( $instances[ $opt_name ] ) ) {
						Redux::setArgs( $opt_name, array( 'menu_type' => 'hidden' ) );
						Redux::setSections( $opt_name, array(
							array(
								'id'     => 'EXTENSION_TAXONOMY_FAKE_ID' . $opt_name,
								'fields' => $fields,
								'title'  => 'N/A'
							)
						) );
						Redux::init( $opt_name );
					} else {
						remove_action( 'admin_enqueue_scripts', array(
							ReduxFrameworkInstances::get_instance( $opt_name ),
							'_enqueue'
						) );
					}
					self::check_opt_name( $opt_name );
					//Redux::setArgs( $opt_name, array( 'output' => false ) );
					Redux::setArgs( $opt_name, Redux_Taxonomy::$args[ $opt_name ] );
				}
				$instances = ReduxFrameworkInstances::get_all_instances();
				add_action( 'admin_enqueue_scripts', array( $instances[ $opt_name ], '_enqueue' ), 1 );
			}
		}

		public static function constructArgs( $opt_name ) {
			$args             = self::$args[ $opt_name ];
			$args['opt_name'] = $opt_name;
			if ( ! isset( $args['menu_title'] ) ) {
				$args['menu_title'] = ucfirst( $opt_name ) . ' Options';
			}
			if ( ! isset( $args['page_title'] ) ) {
				$args['page_title'] = ucfirst( $opt_name ) . ' Options';
			}
			if ( ! isset( $args['page_slug'] ) ) {
				$args['page_slug'] = $opt_name . '_options';
			}

			return $args;
		}

		public static function constructTerms( $opt_name ) {

			$terms = array();
			if ( ! isset( self::$terms[ $opt_name ] ) ) {
				return $terms;
			}

			foreach ( self::$terms[ $opt_name ] as $term_id => $term ) {
				$permissions      = isset( $term['permissions'] ) ? $term['permissions'] : false;
				$add_visibility   = isset( $term['add_visibility'] ) ? $term['add_visibility'] : false;
				$term['sections'] = self::constructSections( $opt_name, $term['id'], $permissions, $add_visibility );
				$terms[]          = $term;
			}
			ksort( $terms );

			return $terms;
		}

		public static function constructSections( $opt_name, $term_id, $permissions = false, $add_visibility = false ) {
			$sections = array();
			if ( ! isset( self::$sections[ $opt_name ] ) ) {
				return $sections;
			}

			foreach ( self::$sections[ $opt_name ] as $section_id => $section ) {
				if ( $section['term_id'] == $term_id ) {

					self::$sections[ $opt_name ][ $section_id ]['add_visibility'] = $section;

					$p = $section['priority'];
					while ( isset( $sections[ $p ] ) ) {
						echo $p ++;
					}
					$section['fields'] = self::constructFields( $opt_name, $section_id );
					$sections[ $p ]    = $section;
				}
			}
			ksort( $sections );

			return $sections;
		}

		public static function constructFields( $opt_name = "", $section_id = "", $permissions = false, $add_visibility = false ) {
			$fields = array();
			if ( ! isset( self::$fields[ $opt_name ] ) ) {
				return $fields;
			}
			foreach ( self::$fields[ $opt_name ] as $key => $field ) {
				// Nested permissions
				self::$fields[ $opt_name ][ $key ]['permissions'] = $field['permissions'] = isset( $field['permissions'] ) ? $field['permissions'] : $permissions;
				// Nested add_visibility permissions
				self::$fields[ $opt_name ][ $key ]['add_visibility'] = $field['add_visibility'] = isset( $field['add_visibility'] ) ? $field['add_visibility'] : $add_visibility;

				if ( $field['section_id'] == $section_id ) {
					$p = $field['priority'];
					while ( isset( $fields[ $p ] ) ) {
						echo $p ++;
					}
					$fields[ $p ] = $field;
				}
			}
			ksort( $fields );

			return $fields;
		}

		public static function getSection( $opt_name = '', $id = '' ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && ! empty( $id ) ) {
				if ( ! isset( self::$sections[ $opt_name ][ $id ] ) ) {
					$id = strtolower( sanitize_html_class( $id ) );
				}

				return isset( self::$sections[ $opt_name ][ $id ] ) ? self::$sections[ $opt_name ][ $id ] : false;
			}

			return false;
		}

		public static function setSection( $opt_name = '', $section = array() ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && is_array( $section ) && ! empty( $section ) ) {
				if ( ! isset( $section['id'] ) ) {
					if ( isset( $section['title'] ) ) {
						$section['id'] = strtolower( sanitize_html_class( $section['title'] ) );
					} else {
						$section['id'] = "section";
					}
					if ( isset( self::$sections[ $opt_name ][ $section['id'] ] ) ) {
						$orig = $section['id'];
						$i    = 0;
						while ( isset( self::$sections[ $opt_name ][ $section['id'] ] ) ) {
							$section['id'] = $orig . '_' . $i;
						}
					}
				}
				if ( ! isset( $section['priority'] ) ) {
					$section['priority'] = self::getPriority( $opt_name, 'sections' );
				}

				if ( isset( $section['fields'] ) ) {
					if ( ! empty( $section['fields'] ) && is_array( $section['fields'] ) ) {

						if ( isset( $section['permissions'] ) || isset( $section['add_visibility'] ) ) {
							foreach ( $section['fields'] as $key => $field ) {
								if ( ! isset( $field['permissions'] ) && isset( $section['permissions'] ) ) {
									$section['fields'][ $key ]['permissions'] = $section['permissions'];
								}
								if ( ! isset( $field['add_visibility'] ) && isset( $section['add_visibility'] ) ) {
									$section['fields'][ $key ]['add_visibility'] = $section['add_visibility'];
								}
							}
						}

						self::processFieldsArray( $opt_name, $section['id'], $section['fields'] );
					}
					unset( $section['fields'] );
				}

				self::$sections[ $opt_name ][ $section['id'] ] = $section;
			} else {
				self::$errors[ $opt_name ]['section']['empty'] = "Unable to create a section due an empty section array or the section variable passed was not an array.";

				return;
			}
		}

		public static function processSectionsArray( $opt_name = "", $term_id = "", $sections = array() ) {
			if ( ! empty( $opt_name ) && ! empty( $term_id ) && is_array( $sections ) && ! empty( $sections ) ) {
				foreach ( $sections as $section ) {
					if ( ! is_array( $section ) ) {
						continue;
					}
					$section['term_id'] = $term_id;
					if ( ! isset( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
						$section['fields'] = array();
					}


					self::setSection( $opt_name, $section );
				}
			}
		}

		public static function processFieldsArray( $opt_name = "", $section_id = "", $fields = array() ) {
			if ( ! empty( $opt_name ) && ! empty( $section_id ) && is_array( $fields ) && ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					if ( ! is_array( $field ) ) {
						continue;
					}
					$field['section_id'] = $section_id;
					self::setField( $opt_name, $field );
				}
			}
		}

		public static function getField( $opt_name = '', $id = '' ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && ! empty( $id ) ) {
				return isset( self::$fields[ $opt_name ][ $id ] ) ? self::$fields[ $opt_name ][ $id ] : false;
			}

			return false;
		}

		public static function setField( $opt_name = '', $field = array() ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && is_array( $field ) && ! empty( $field ) ) {
				if ( ! isset( $field['priority'] ) ) {
					$field['priority'] = self::getPriority( $opt_name, 'fields' );
				}
				self::$fields[ $opt_name ][ $field['id'] ] = $field;
			}
		}

		public static function setArgs( $opt_name = '', $args = array() ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && is_array( $args ) && ! empty( $args ) ) {
				self::$args[ $opt_name ] = isset( self::$args[ $opt_name ] ) ? self::$args[ $opt_name ] : array();
				self::$args[ $opt_name ] = wp_parse_args( $args, self::$args[ $opt_name ] );
			}
		}

		public static function setTerm( $opt_name = "", $term = array() ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && is_array( $term ) && ! empty( $term ) ) {
				if ( ! isset( $term['id'] ) ) {
					if ( isset( $term['title'] ) ) {
						$term['id'] = strtolower( sanitize_html_class( $term['title'] ) );
					} else {
						$term['id'] = "term";
					}
					if ( isset( self::$terms[ $opt_name ][ $term['id'] ] ) ) {
						$orig = $term['id'];
						$i    = 0;
						while ( isset( self::$terms[ $opt_name ][ $term['id'] ] ) ) {
							$term['id'] = $orig . '_' . $i;
						}
					}
				}
				if ( isset( $term['sections'] ) ) {
					if ( ! empty( $term['sections'] ) && is_array( $term['sections'] ) ) {
						if ( isset( $term['permissions'] ) || isset( $term['add_visibility'] ) ) {
							foreach ( $term['sections'] as $key => $section ) {
								if ( ! isset( $section['permissions'] ) && isset( $term['permissions'] ) ) {
									$term['sections'][ $key ]['permissions'] = $term['permissions'];
								}
								if ( ! isset( $section['add_visibility'] ) && isset( $term['add_visibility'] ) ) {
									$term['sections'][ $key ]['add_visibility'] = $term['add_visibility'];
								}
							}
						}
						self::processSectionsArray( $opt_name, $term['id'], $term['sections'] );
					}
					unset( $term['sections'] );
				}
				self::$terms[ $opt_name ][ $term['id'] ] = $term;
			} else {
				self::$errors[ $opt_name ]['term']['empty'] = "Unable to create a term due an empty term array or the term variable passed was not an array.";

				return;
			}
		}

		public static function setTerms( $opt_name = "", $terms = array() ) {
			if ( ! empty( $terms ) && is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					Redux_Taxonomy::setTerm( $opt_name, $term );
				}
			}
		}

		public static function getTerms( $opt_name = "" ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && ! empty( self::$terms[ $opt_name ] ) ) {
				return self::$terms[ $opt_name ];
			}
		}

		public static function getBox( $opt_name = "", $key = "" ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && ! empty( $key ) && ! empty( self::$terms[ $opt_name ] ) && isset( self::$terms[ $opt_name ][ $key ] ) ) {
				return self::$terms[ $opt_name ][ $key ];
			}
		}

		public static function getPriority( $opt_name, $type ) {
			$priority                             = self::$priority[ $opt_name ][ $type ];
			self::$priority[ $opt_name ][ $type ] += 1;

			return $priority;
		}

		public static function check_opt_name( $opt_name = "" ) {
			if ( empty( $opt_name ) || is_array( $opt_name ) ) {
				return;
			}
			if ( ! isset( self::$terms[ $opt_name ] ) ) {
				self::$terms[ $opt_name ] = array();
			}
			if ( ! isset( self::$priority[ $opt_name ] ) ) {
				self::$priority[ $opt_name ]['args'] = 1;
			}
			if ( ! isset( self::$sections[ $opt_name ] ) ) {
				self::$sections[ $opt_name ]             = array();
				self::$priority[ $opt_name ]['sections'] = 1;
			}
			if ( ! isset( self::$fields[ $opt_name ] ) ) {
				self::$fields[ $opt_name ]             = array();
				self::$priority[ $opt_name ]['fields'] = 1;
			}
			if ( ! isset( self::$errors[ $opt_name ] ) ) {
				self::$errors[ $opt_name ] = array();
			}
			if ( ! isset( self::$init[ $opt_name ] ) ) {
				self::$init[ $opt_name ] = false;
			}
			if ( ! isset( self::$args[ $opt_name ] ) ) {
				self::$args[ $opt_name ] = false;
			}
		}

		public static function get_field_defaults( $opt_name ) {
			if ( empty( $opt_name ) || is_array( $opt_name ) ) {

				return;
			}
			if ( ! isset( Redux_Taxonomy::$fields[ $opt_name ] ) ) {
				return array();
			}
			$defaults = array();
			foreach ( Redux_Taxonomy::$fields[ $opt_name ] as $key => $field ) {
				$defaults[ $key ] = isset( $field['default'] ) ? $field['default'] : '';
			}

			return $defaults;
		}

		public static function get_term_meta( $args = array() ) {

			$default = array(
				'key'      => '',
				'opt_name' => '',
				'taxonomy' => ''
			);

			$args = wp_parse_args( $args, $default );
			extract( $args );

			if ( empty( $taxonomy ) ) {
				return array();
			}
			$single = ! empty( $key ) ? true : false;

			$meta = get_term_meta( $taxonomy, $key, $single );
			//var_dump($meta);
			if ( $single ) {
				// Do nothing
			} else if ( ! empty( $meta ) ) {
				foreach ( $meta as $key => $value ) {
					if ( is_array( $value ) ) {
						$value        = $value[0];
						$meta[ $key ] = maybe_unserialize( $value );
					} else {
						$meta[ $key ] = maybe_unserialize( $value );
					}
				}
			}

			if ( ! empty( $opt_name ) ) {
				$defaults = Redux_Taxonomy::get_field_defaults( $opt_name );

				if ( $single ) {
					$default_value = '';

					if ( isset( $defaults[ $key ] ) ) {
						$default_value = $defaults[ $key ];
					}

					if ( is_array( $meta ) ) {
						if ( is_array( $default_value ) ) {
							$meta = wp_parse_args( $meta, $default_value );
						}
					} else {
						if ( $meta == '' && $default_value != '' ) {
							$meta = $default_value;
						}
					}
				} else {
					$meta = wp_parse_args( $meta, $defaults );
				}
			}

			return $meta;
		}
	}

	Redux_Taxonomy::load();
}