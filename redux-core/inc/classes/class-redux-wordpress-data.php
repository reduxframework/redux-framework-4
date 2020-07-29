<?php
/**
 * Redux WordPress Data Class
 *
 * @class Redux_WordPress_Data
 * @version 3.0.0
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_WordPress_Data', false ) ) {

	/**
	 * Class Redux_WordPress_Data
	 */
	class Redux_WordPress_Data extends Redux_Class {

		/**
		 * Holds WordPress data.
		 *
		 * @var null
		 */
		private $wp_data = null;

		/**
		 * Redux_WordPress_Data constructor.
		 *
		 * @param mixed $parent ReduxFramework pointer or opt_name.
		 */
		public function __construct( $parent = null ) {
			if ( is_string( $parent ) ) {
				$this->opt_name = $parent;
			} else {
				parent::__construct( $parent );
			}
		}

		/**
		 * Maybe translate.
		 *
		 * @param string $value Value.
		 * @param string $post_type Post type.
		 */
		private function maybe_translate( &$value, $post_type ) {
			if ( has_filter( 'wpml_object_id' ) ) {
				if ( Redux_Helpers::is_integer( $value ) ) {
					$value = apply_filters( 'wpml_object_id', $value, $post_type, true );
				} elseif ( is_array( $value ) ) {
					$value = array_map(
						function ( $val ) use ( $post_type ) {
							return apply_filters( 'wpml_object_id', $val, $post_type, true );
						},
						$value
					);
				}
			}
		}

		/**
		 * Get the data.
		 *
		 * @param bool       $type Type.
		 * @param array      $args Args.
		 * @param string|int $current_value Current value.
		 *
		 * @return array|mixed|string
		 */
		public function get( $type = false, $args = array(), $current_value = '' ) {
			$opt_name = $this->opt_name;

			/**
			 * Filter 'redux/options/{opt_name}/pre_data/{type}'
			 *
			 * @param string $data
			 */

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$data = apply_filters( "redux/options/{$opt_name}/pre_data/$type", null );

			if ( null !== $data ) {
				return $data;
			} else {
				$data = array();
			}

			$current_data = array();

			// We add the current selected post type in the data so that it's always in the options (else it can be lost).
			if ( ! empty( $current_value ) && ( is_array( $current_value ) || Redux_Helpers::is_integer( $current_value ) ) ) {
				switch ( $type ) {
					case 'pages':
					case 'page':
						$this->maybe_translate( $current_value, 'page' );
						$pages = get_pages( array( 'include' => $current_value ) );
						if ( ! empty( $pages ) ) {
							foreach ( $pages as $page ) {
								$current_data[ $page->ID ] = $page->post_title;
							}
						}
						break;

					case 'categories':
					case 'category':
						$this->maybe_translate( $current_value, 'category' );
						$terms = get_categories( array( 'object_ids' => $current_value ) );
						if ( ! empty( $terms ) ) {
							foreach ( $terms as $term ) {
								$current_data[ $term->term_id ] = $term->name;
							}
						}
						break;

					case 'terms':
					case 'term':
						$this->maybe_translate( $current_value, isset( $args['taxonomy'] ) ? $args['taxonomy'] : '' );

						$terms = get_terms(
							array(
								'object_ids' => $current_value,
								'taxonomy'   => isset( $args['taxonomy'] ) ? $args['taxonomy'] : '',
							)
						);

						if ( ! empty( $terms ) && ! is_a( $terms, 'WP_Error' ) ) {
							foreach ( $terms as $term ) {
								$current_data[ $term->term_id ] = $term->name;
							}
						}

						break;

					case 'tags':
					case 'tag':
						$this->maybe_translate( $current_value, 'post_tag' );

						$terms = get_tags( array( 'object_ids' => $current_value ) );
						if ( ! empty( $terms ) ) {
							foreach ( $terms as $term ) {
								$current_data[ $term->term_id ] = $term->name;
							}
						}

						break;

					case 'menus':
					case 'menu':
						$this->maybe_translate( $current_value, 'nav_menu' );

						$menus = wp_get_nav_menus( array( 'object_ids' => $current_value ) );
						if ( ! empty( $menus ) ) {
							foreach ( $menus as $item ) {
								$current_data[ $item->term_id ] = $item->name;
							}
						}

						break;

					case 'post':
					case 'posts':
						$this->maybe_translate( $current_value, 'post' );
						if ( ! is_array( $current_value ) ) {
							$current_value = array( $current_value );
						}
						$posts = get_posts( array( 'post__in' => $current_value ) );
						if ( ! empty( $posts ) ) {
							foreach ( $posts as $post ) {
								$current_data[ $post->ID ] = $post->post_title;
							}
						}

						break;

					case 'users':
					case 'user':
						$users = get_users( array( 'include' => $current_value ) );
						if ( ! empty( $users ) ) {
							foreach ( $users as $user ) {
								$current_data[ $user->ID ] = $user->display_name;
							}
						}

						break;

					case 'sites':
					case 'site':
						// WP > 4.6.
						if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
							$sites = get_sites();
							// WP < 4.6.
						} else {
							$sites = wp_get_sites(); // phpcs:ignore WordPress.WP.DeprecatedFunctions
						}

						foreach ( $sites as $site ) {
							$site = (array) $site;
							$k    = $site['blog_id'];
							$v    = $site['domain'] . $site['path'];

							$current_data[ $k ] = $v;
						}

						break;
				}
			}

			// phpcs:ignore Squiz.PHP.CommentedOutCode
			$args_key = md5( maybe_serialize( $args ) );

			// Data caching.
			if ( isset( $this->wp_data[ $type . $args_key ] ) ) {
				$data = $this->wp_data[ $type . $args_key ];
			} elseif ( ! empty( $type ) ) {

				/**
				 * Use data from WordPress to populate options array.
				 * */
				if ( ! empty( $type ) && empty( $data ) ) {
					if ( empty( $args ) ) {
						$args = array();
					}

					$data = array();
					$args = wp_parse_args( $args, array() );
					if ( isset( $args['args'] ) && empty( $args['args'] ) ) {
						unset( $args['args'] );
					}

					switch ( $type ) {
						case 'categories':
						case 'category':
							$cats = get_categories( $args );
							if ( ! empty( $cats ) ) {
								foreach ( $cats as $cat ) {
									$data[ $cat->term_id ] = $cat->name;
								}
							}
							break;

						case 'pages':
						case 'page':
							if ( ! isset( $args['posts_per_page'] ) ) {
								$args['posts_per_page'] = 20;
							}
							$pages = get_pages( $args );
							if ( ! empty( $pages ) ) {
								foreach ( $pages as $page ) {
									$data[ $page->ID ] = $page->post_title;
								}
							}
							break;

						case 'terms':
						case 'term':
							if ( isset( $args['taxonomies'] ) ) {
								$args = $args['taxonomies'];
							}
							$terms = get_terms( $args );
							if ( ! empty( $terms ) && ! is_a( $terms, 'WP_Error' ) ) {
								foreach ( $terms as $term ) {
									$data[ $term->term_id ] = $term->name;
								}
							}
							break;

						case 'taxonomies':
						case 'taxonomy':
						case 'tax':
							if ( isset( $args['taxonomies'] ) ) {
								$args = $args['taxonomies'];
							}
							$taxonomies = get_taxonomies( $args );
							if ( ! empty( $taxonomies ) ) {
								foreach ( $taxonomies as $key => $taxonomy ) {
									$data[ $key ] = $taxonomy;
								}
							}
							break;
						case 'post':
						case 'posts':
							$posts = get_posts( $args );
							if ( ! empty( $posts ) ) {
								foreach ( $posts as $post ) {
									$data[ $post->ID ] = $post->post_title;
								}
							}
							break;

						case 'post_type':
						case 'post_types':
							global $wp_post_types;

							$defaults   = array(
								'public'              => true,
								'exclude_from_search' => false,
							);
							$args       = wp_parse_args( $args, $defaults );
							$output     = 'names';
							$operator   = 'and';
							$post_types = get_post_types( $args, $output, $operator );

							ksort( $post_types );

							foreach ( $post_types as $name => $title ) {
								if ( isset( $wp_post_types[ $name ]->labels->menu_name ) ) {
									$data[ $name ] = $wp_post_types[ $name ]->labels->menu_name;
								} else {
									$data[ $name ] = ucfirst( $name );
								}
							}
							break;

						case 'tags':
						case 'tag':
							$tags = get_tags( $args );
							if ( ! empty( $tags ) ) {
								foreach ( $tags as $tag ) {
									$data[ $tag->term_id ] = $tag->name;
								}
							}
							break;

						case 'menus':
						case 'menu':
							$menus = wp_get_nav_menus( $args );
							if ( ! empty( $menus ) ) {
								foreach ( $menus as $item ) {
									$data[ $item->term_id ] = $item->name;
								}
							}
							break;

						case 'menu_locations':
						case 'menu_location':
							global $_wp_registered_nav_menus;

							foreach ( $_wp_registered_nav_menus as $k => $v ) {
								$data[ $k ] = $v;
							}
							break;

						case 'image_size':
						case 'image_sizes':
							global $_wp_additional_image_sizes;

							foreach ( $_wp_additional_image_sizes as $size_name => $size_attrs ) {
								$data[ $size_name ] = $size_name . ' - ' . $size_attrs['width'] . ' x ' . $size_attrs['height'];
							}
							break;

						case 'elusive-icons':
						case 'elusive-icon':
						case 'elusive':
						case 'icons':
						case 'font-icon':
						case 'font-icons':
							/**
							 * Filter 'redux/font-icons'
							 *
							 * @deprecated
							 *
							 * @param array $font_icons array of elusive icon classes
							 */

							// phpcs:ignore WordPress.NamingConventions.ValidHookName
							$font_icons = apply_filters( 'redux/font-icons', array() );

							/**
							 * Filter 'redux/{opt_name}/field/font/icons'
							 *
							 * @deprecated
							 *
							 * @param array $font_icons array of elusive icon classes
							 */

							// phpcs:ignore WordPress.NamingConventions.ValidHookName
							$font_icons = apply_filters( "redux/{$opt_name}/field/font/icons", $font_icons );

							foreach ( $font_icons as $k ) {
								$data[ $k ] = $k;
							}
							break;

						case 'roles':
						case 'role':
							global $wp_roles;

							$data = $wp_roles->get_names();
							break;

						case 'sidebars':
						case 'sidebar':
							global $wp_registered_sidebars;

							foreach ( $wp_registered_sidebars as $key => $value ) {
								$data[ $key ] = $value['name'];
							}
							break;
						case 'capabilities':
						case 'capability':
							global $wp_roles;

							foreach ( $wp_roles->roles as $role ) {
								foreach ( $role['capabilities'] as $key => $cap ) {
									$data[ $key ] = ucwords( str_replace( '_', ' ', $key ) );
								}
							}
							break;

						case 'users':
						case 'user':
							$users = get_users( $args );
							if ( ! empty( $users ) ) {
								foreach ( $users as $user ) {
									$data[ $user->ID ] = $user->display_name;
								}
							}
							break;

						case 'sites':
						case 'site':
							// WP > 4.6.
							if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
								$sites = get_sites();
								// WP < 4.6.
							} else {
								$sites = wp_get_sites(); // phpcs:ignore WordPress.WP.DeprecatedFunctions
							}

							foreach ( $sites as $site ) {
								$site = (array) $site;
								$k    = $site['blog_id'];
								$v    = $site['domain'] . $site['path'];

								$data[ $k ] = $v;
							}

							break;

						case 'callback':
							if ( ! is_array( $args ) ) {
								$args = array( $args );
							}
							$data = call_user_func( $args[0], $current_value );
							break;
					}
				}

				if ( ! empty( $data ) ) {
					$this->wp_data[ $type . $args_key ] = $data;
				}
			}

			if ( ! empty( $current_data ) ) {
				$data += $current_data;
			}

			/**
			 * Filter 'redux/options/{opt_name}/data/{type}'
			 *
			 * @param string $data
			 */

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$data = apply_filters( "redux/options/{$opt_name}/data/$type", $data );

			return $data;
		}
	}
}
