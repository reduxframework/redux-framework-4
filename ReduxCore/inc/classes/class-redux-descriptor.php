<?php

/**
 * Class Redux_Descriptor
 *
 * Used to describe redux fields
 * @author Tofandel
 */
class Redux_Descriptor {
	protected $reflection_class;
	protected $field_type;

	protected $name;
	protected $description;
	protected $icon;
	protected $required;

	/**
	 * @var Redux_Descriptor_Fields[]
	 */
	protected $fields = array();

	protected $current_field;

	/**
	 * Redux_Descriptor constructor.
	 *
	 * @param $field
	 */
	public function __construct( $field ) {
		Redux_Descriptor_Fields::$order = 0;
		try {
			$this->reflection_class = new ReflectionClass( $field );
		} catch ( ReflectionException $e ) {
			die ( $e->getMessage() );
		}
		$this->field_type = strtolower( Redux_Helpers::remove_prefix( $this->reflection_class->getShortName(), 'ReduxFramework_' ) );
		$this->name       = ucfirst( $this->field_type );
	}


	/**
	 * Get field type.
	 *
	 * @return string
	 */
	public function get_field_type() {
		return $this->field_type;
	}

	/**
	 * Set the basic required information.
	 *
	 * @param        $name
	 * @param string $description
	 * @param string $icon
	 */
	public function set_info( $name, $description = '', $icon = '' ) {
		$this->name        = $name;
		$this->description = $description;
		$this->icon        = $icon;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Add field to the descriptor.
	 *
	 * @param string $name
	 * @param string $title
	 * @param string $type
	 * @param string $description
	 * @param null   $default
	 *
	 * @return Redux_Descriptor_Fields
	 */
	public function add_field( $name, $title, $type, $description = '', $default = null ) {
		try {
			$this->fields[ $name ] = new Redux_Descriptor_Fields( $name, $title, $type, $description, $default );
		} catch ( Exception $e ) {
			return null;
		}
		$this->current_field = $name;

		return $this->fields[ $name ];
	}

	/**
	 * @param array $req
	 *
	 * @return array
	 */
	public function parse_request( $req ) {
		$parsed_req = array();
		foreach ( $req as $k => $v ) {
			if ( isset( $this->fields[ $k ] ) ) {
				$parsed_req[ $k ] = $v;
			}
		}

		return $parsed_req;
	}

	/**
	 * Selects and returns a field or the current field
	 *
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function field( $field_name = '' ) {
		if ( ! empty( $field_name ) ) {
			$this->current_field = $field_name;
		}

		if ( isset( $this->fields[ $this->current_field ] ) ) {
			return $this->fields[ $this->current_field ];
		} else {
			return null;
		}
	}

	/**
	 * Remove a field.
	 *
	 * @param $name
	 */
	public function remove_field( $name ) {
		unset( $this->fields[ $name ] );
	}

	/**
	 * To documentation.
	 *
	 * @return string
	 */
	public function to_doc() {
		$doc = $this->name . "\n" . $this->description . "\n";
		$doc .= 'Fields:';
		$this->sort_fields();
		foreach ( $this->fields as $option ) {
			$doc .= $option->to_doc();
		}

		return $doc;
	}

	/**
	 * Sorts the fields by their order field.
	 */
	protected function sort_fields() {
		uksort( $this->fields, function ( $item1, $item2 ) {
			if ( $item1[ 'order' ] == $item2[ 'order' ] ) {
				return 0;
			}

			return $item1[ 'order' ] < $item2[ 'order' ] ? - 1 : 1;
		} );
	}

	/**
	 * To array.
	 *
	 * @return array
	 */
	public function to_array() {
		$fields = array();

		$this->sort_fields();
		foreach ( $this->fields as $option ) {
			$fields[ $option[ 'name' ] ] = $option->to_array();
		}

		return array(
			'type'        => $this->field_type,
			'name'        => $this->name,
			'description' => $this->description,
			'icon'        => $this->icon,
			'fields'      => $fields,
		);
	}
}