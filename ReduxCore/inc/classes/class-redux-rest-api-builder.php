<?php

/**
 * Class Redux_Rest_Api_Builder
 * The rest api to make the redux field builder.
 * @author Tofandel
 */
class Redux_Rest_Api_Builder {

	const ENDPOINT = 'redux/descriptors';
	const VER = 'v1';

	/**
	 * Get the namespace of the api.
	 *
	 * @return string
	 */
	public function get_namespace() {
		return self::ENDPOINT . '/' . self::VER;
	}

	/**
	 * Get the rest url for an api call.
	 *
	 * @param string $route Route router.
	 *
	 * @return string
	 */
	public function get_url( $route ) {
		return rest_url( trailingslashit( $this->get_namespace() ) . ltrim( '/', $route ) );
	}

	/**
	 * Redux_Rest_Api_Builder constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Init the rest api.
	 */
	public function rest_api_init() {
		register_rest_route(
			$this->get_namespace(),
			'/fields',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'list_fields' ),
			)
		);
		register_rest_route(
			$this->get_namespace(),
			'/field/(?P<type>[a-z0-9]+)',
			array(
				'args'     => array(
					'name' => array(
						'description' => __( 'The field type' ),
						'type'        => 'string',
					),
				),
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_field' ),
			)
		);
		register_rest_route(
			$this->get_namespace(),
			'/field/(?P<type>[a-z0-9]+)/render',
			array(
				'args'     => array(
					'name' => array(
						'description' => __( 'The field type' ),
						'type'        => 'string',
					),
				),
				'methods'  => WP_REST_Server::ALLMETHODS,
				'callback' => array( $this, 'render_field' ),
			)
		);
	}

	/**
	 * List the available fields.
	 *
	 * @return array
	 */
	public function list_fields() {
		$fields     = array();
		$fields_dir = trailingslashit( ReduxCore::$dir ) . 'inc' . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR;
		$dirs       = scandir( $fields_dir );
		$classes    = array();
		foreach ( $dirs as $folder ) {
			if ( $folder != '.' && $folder != '..' ) {
				$files = array(
					$fields_dir . $folder . DIRECTORY_SEPARATOR . 'field_' . $folder . '.php',
				);

				$filename = Redux_Functions::file_exists_ex( $files );

				if ( ! empty( $filename ) ) {
					$classes[ $folder ] = $filename;
					require_once $filename;
					// Load it here to save some resources in autoloading!
				}
			}
		}

		// phpcs:ignore WordPress.NamingConventions.ValidHookName
		$classes = apply_filters( 'redux/fields', $classes );
		foreach ( $classes as $field => $file ) {
			/**
			 * Loops over the classes
			 *
			 * @var class Redux_Descriptor_Field $descriptor
			 */

			$field_classes = array( 'Redux_' . ucwords( $field ), 'ReduxFramework_' . ucwords( $field ) );
			$field_class   = Redux_Functions::class_exists_ex( $field_classes );

			if ( $field_class && file_exists( $file ) ) {
				require_once $file;
			}


			if ( is_subclass_of( $field_class, 'Redux_Field' ) ) {
				$descriptor = call_user_func( array( $field_class, 'get_descriptor' ) );
				if ( ! empty( $descriptor->get_field_type() ) ) {
					$fields[ $descriptor->get_field_type() ] = $descriptor->to_array();
				}
			}
		}

		return $fields;
	}

	/**
	 * Get the information of a field.
	 *
	 * @param string $data Pointer to ReduxFramework object.
	 *
	 * @return array
	 */
	public function get_field( $data = '' ) {
		$type = $data[ 'type' ];

		if ( ! empty( $type ) ) {
			$field_classes = array( 'Redux_' . ucwords( $type ), 'ReduxFramework_' . ucwords( $type ) );
			$field_class   = Redux_Functions::class_exists_ex( $field_classes );

			if ( $field_class && is_subclass_of( $field_class, 'Redux_Field' ) ) {
				/**
				 * Test if the field exists
				 *
				 * @var Redux_Descriptor_Field $descriptor
				 */
				$descriptor = call_user_func( array( 'Redux_' . $type, 'get_descriptor' ) );

				return $descriptor->to_array();
			}
		}

		return array( 'success' => false );
	}


	/**
	 * Render the html of a field and return it to the api.
	 *
	 * @param string $data Name of field.
	 *
	 * @return array
	 */
	public function render_field( $data = '' ) {

		// TODO MODIFY the function to get the post data from the data object with a post method in the register route!
		$type = $data[ 'type' ];
		if ( ! empty( $type ) && class_exists( 'ReduxFramework_' . $type ) && is_subclass_of( 'ReduxFramework_' . $type, 'Redux_Field' ) ) {
			try {
				$class = new ReflectionClass( 'ReduxFramework_' . $type );
			} catch ( ReflectionException $e ) {
				return array( 'success' => false );
			}
			$opt_name = 'my_opt_name';
			// phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
			if ( ! empty( $_REQUEST[ 'opt_name' ] ) ) {
				$opt_name = $_REQUEST[ 'opt_name' ];
			}

			/**
			 * Grab the field descriptor
			 *
			 * @var Redux_Descriptor_Field $descriptor
			 */
			$descriptor = call_user_func( array( 'ReduxFramework_' . $type, 'get_descriptor' ) );

			$redux_instance = new ReduxFramework( array(), array( 'opt_name' => $opt_name ) );
			$req            = $descriptor->parse_request( $_REQUEST );
			$field          = $class->newInstance(
				$req,
				isset( $_REQUEST[ 'example_values' ] ) ? $_REQUEST[ 'example_values' ] : '', $redux_instance
			);
			ob_start();
			$field->render();

			return array(
				'success' => true,
				'render'  => ob_get_clean(),
			);
		}

		return array( 'success' => false );
	}
}