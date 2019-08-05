<?php

/**
 * Redux Framework Users API Class
 * Makes instantiating a Redux object an absolute piece of cake.
 *
 * @package     Redux_Framework
 * @author      Dovy Paukstys
 * @subpackage  Users
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'Redux_Users' ) ) {

	/**
	 * Redux Users API Class
	 * Simple API for Redux Framework
	 *
	 * @since       1.0.0
	 */
	class Redux_Users {

		public static $profiles = array();
		public static $sections = array();
		public static $fields = array();
		public static $priority = array();
		public static $errors = array();
		public static $init = array();
		public static $hasRun = false;
		public static $args = array();

		public static function load() {
			add_action( 'init', array( 'Redux_Users', '_enqueue' ), 99 );
		}

		public static function _enqueue() {
			// Check and run instances of Redux where the opt_name hasn't been run.
			global $pagenow;
			$pagenows = array( 'user-new.php', 'profile.php', 'user-edit.php' );
			if ( ! empty( Redux_Users::$sections ) && in_array( $pagenow, $pagenows ) ) {
				$instances = ReduxFrameworkInstances::get_all_instances();
				foreach ( Redux_Users::$fields as $opt_name => $fields ) {
					if ( ! isset( $instances[ $opt_name ] ) ) {
						Redux::setArgs( $opt_name, array( 'menu_type' => 'hidden' ) );
						Redux::setSections( $opt_name, array(
							array(
								'id'     => 'EXTENSION_USERS_FAKE_ID' . $opt_name,
								'fields' => $fields,
								'title'  => 'N/A'
							)
						) );
						Redux::init( $opt_name );
					} else {
						remove_action('admin_enqueue_scripts', array(ReduxFrameworkInstances::get_instance($opt_name), '_enqueue'));
					}
					//Redux::setArgs( $opt_name, array( 'output' => false ) );
					self::check_opt_name( $opt_name );
					Redux::setArgs( $opt_name, Redux_Users::$args[ $opt_name ] );
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

		public static function constructProfiles( $opt_name ) {

			$profiles = array();
			if ( ! isset( self::$profiles[ $opt_name ] ) ) {
				return $profiles;
			}

			foreach ( self::$profiles[ $opt_name ] as $profile_id => $profile ) {
				$permissions         = isset( $profile['permissions'] ) ? $profile['permissions'] : false;
				$roles               = isset( $profile['roles'] ) ? $profile['roles'] : false;
				$profile['sections'] = self::constructSections( $opt_name, $profile['id'], $permissions, $roles );
				$profiles[]          = $profile;
			}
			ksort( $profiles );

			return $profiles;
		}

		public static function constructSections( $opt_name, $profile_id, $permissions = false, $roles = false ) {
			$sections = array();
			if ( ! isset( self::$sections[ $opt_name ] ) ) {
				return $sections;
			}

			foreach ( self::$sections[ $opt_name ] as $section_id => $section ) {
				if ( $section['profile_id'] == $profile_id ) {

					self::$sections[ $opt_name ][ $section_id ]['roles'] = $section;

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

		public static function constructFields( $opt_name = "", $section_id = "", $permissions = false, $roles = false ) {
			$fields = array();
			if ( ! isset( self::$fields[ $opt_name ] ) ) {
				return $fields;
			}
			foreach ( self::$fields[ $opt_name ] as $key => $field ) {
				// Nested permissions
				self::$fields[ $opt_name ][ $key ]['permissions'] = $field['permissions'] = isset( $field['permissions'] ) ? $field['permissions'] : $permissions;
				// Nested roles permissions
				self::$fields[ $opt_name ][ $key ]['roles'] = $field['roles'] = isset( $field['roles'] ) ? $field['roles'] : $roles;

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

		public static function setArgs( $opt_name = '', $args = array() ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && is_array( $args ) && ! empty( $args ) ) {
				self::$args[ $opt_name ] = isset( self::$args[ $opt_name ] ) ? self::$args[ $opt_name ] : array();
				self::$args[ $opt_name ] = wp_parse_args( $args, self::$args[ $opt_name ] );
			}
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

						if ( isset( $section['permissions'] ) || isset( $section['roles'] ) ) {
							foreach ( $section['fields'] as $key => $field ) {
								if ( ! isset( $field['permissions'] ) && isset( $section['permissions'] ) ) {
									$section['fields'][ $key ]['permissions'] = $section['permissions'];
								}
								if ( ! isset( $field['roles'] ) && isset( $section['roles'] ) ) {
									$section['fields'][ $key ]['roles'] = $section['roles'];
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

		public static function processSectionsArray( $opt_name = "", $profile_id = "", $sections = array() ) {
			if ( ! empty( $opt_name ) && ! empty( $profile_id ) && is_array( $sections ) && ! empty( $sections ) ) {
				foreach ( $sections as $section ) {
					if ( ! is_array( $section ) ) {
						continue;
					}
					$section['profile_id'] = $profile_id;
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

		public static function setProfile( $opt_name = "", $profile = array() ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && is_array( $profile ) && ! empty( $profile ) ) {
				if ( ! isset( $profile['id'] ) ) {
					if ( isset( $profile['title'] ) ) {
						$profile['id'] = strtolower( sanitize_html_class( $profile['title'] ) );
					} else {
						$profile['id'] = "profile";
					}
					if ( isset( self::$profiles[ $opt_name ][ $profile['id'] ] ) ) {
						$orig = $profile['id'];
						$i    = 0;
						while ( isset( self::$profiles[ $opt_name ][ $profile['id'] ] ) ) {
							$profile['id'] = $orig . '_' . $i;
						}
					}
				}
				if ( isset( $profile['sections'] ) ) {
					if ( ! empty( $profile['sections'] ) && is_array( $profile['sections'] ) ) {
						if ( isset( $profile['permissions'] ) || isset( $profile['roles'] ) ) {
							foreach ( $profile['sections'] as $key => $section ) {
								if ( ! isset( $section['permissions'] ) && isset( $profile['permissions'] ) ) {
									$profile['sections'][ $key ]['permissions'] = $profile['permissions'];
								}
								if ( ! isset( $section['roles'] ) && isset( $profile['roles'] ) ) {
									$profile['sections'][ $key ]['roles'] = $profile['roles'];
								}
							}
						}
						self::processSectionsArray( $opt_name, $profile['id'], $profile['sections'] );
					}
					unset( $profile['sections'] );
				}
				self::$profiles[ $opt_name ][ $profile['id'] ] = $profile;
			} else {
				self::$errors[ $opt_name ]['profile']['empty'] = "Unable to create a profile due an empty profile array or the profile variable passed was not an array.";

				return;
			}
		}

		public static function setProfiles( $opt_name = "", $profiles = array() ) {
			if ( ! empty( $profiles ) && is_array( $profiles ) ) {
				foreach ( $profiles as $profile ) {
					Redux_Users::setProfile( $opt_name, $profile );
				}
			}
		}

		public static function getProfiles( $opt_name = "" ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && ! empty( self::$profiles[ $opt_name ] ) ) {
				return self::$profiles[ $opt_name ];
			}
		}

		public static function getBox( $opt_name = "", $key = "" ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && ! empty( $key ) && ! empty( self::$profiles[ $opt_name ] ) && isset( self::$profiles[ $opt_name ][ $key ] ) ) {
				return self::$profiles[ $opt_name ][ $key ];
			}
		}

		public static function getPriority( $opt_name, $type ) {
			$priority = self::$priority[ $opt_name ][ $type ];
			self::$priority[ $opt_name ][ $type ] += 1;

			return $priority;
		}

		public static function check_opt_name( $opt_name = "" ) {
			if ( empty( $opt_name ) || is_array( $opt_name ) ) {
				return;
			}
			if ( ! isset( self::$profiles[ $opt_name ] ) ) {
				self::$profiles[ $opt_name ] = array();
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
			if ( ! isset( Redux_Users::$fields[ $opt_name ] ) ) {
				return array();
			}
			$defaults = array();
			foreach ( Redux_Users::$fields[ $opt_name ] as $key => $field ) {
				$defaults[ $key ] = isset( $field['default'] ) ? $field['default'] : '';
			}

			return $defaults;
		}

		public static function get_user_role( $user_id = 0 ) {
			$user = ( $user_id ) ? get_userdata( $user_id ) : wp_get_current_user();

			return current( $user->roles );
		}

		public static function get_user_meta( $args = array() ) {

			$default = array(
				'key'      => '',
				'opt_name' => '',
				'user'     => ''
			);

			$args = wp_parse_args( $args, $default );
			if ( empty( $args['user'] ) ) {
				$args['user'] = get_current_user_id();
			}
			extract( $args );

			$single = ! empty( $key ) ? true : false;
			$meta   = get_user_meta( $user, $key, $single );
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
				$defaults = Redux_Users::get_field_defaults( $opt_name );

				$meta = wp_parse_args( $meta, $defaults );
			}

			return $meta;
		}
	}

	Redux_Users::load();
}