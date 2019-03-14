<?php

/**
 * Class Redux_Descriptor_Fields
 * Used to map fields of field descriptors.
 */


class Redux_Descriptor_Fields {
	protected $options;

	public static $order = 0;

	/**
	 * Redux_Descriptor_Fields constructor.
	 *
	 * @param string $name
	 * @param string $title
	 * @param string $type
	 * @param string $description
	 * @param mixed  $default
	 *
	 * @throws Exception
	 */
	public function __construct( $name, $title, $type, $description = '', $default = null ) {
		if ( ! Redux_Descriptor_Types::is_valid_type( $type ) ) {
			throw new Exception( 'Unknown type ' . $type . ' for option ' . $name );
		}
		if ( ! is_string( $title ) ) {
			$title = ucfirst( $name );
		}
		$this->options = array(
			'name'        => $name,
			'title'       => $title,
			'type'        => $type,
			'description' => $description,
			'default'     => $default,
			'order'       => static::$order ++,
			'required'    => (bool) $this->required,
		);
	}

	protected $required = false;

	/**
	 * Set required.
	 *
	 * @param bool $required
	 *
	 * @return Redux_Descriptor_Fields
	 */
	public function set_required( $required = true ) {
		$this->required = $required;

		return $this;
	}

	/**
	 * Set order.
	 *
	 * @param $order
	 *
	 * @return $this
	 */
	public function set_order( $order ) {
		static::$order          = $order;
		$this->options['order'] = (float) $order;

		return $this;
	}

	/**
	 * Set group.
	 *
	 * @param string $group
	 *
	 * @return $this
	 */
	public function set_group( $group ) {
		$this->options['group'] = $group;

		return $this;
	}

	/**
	 * Set an option
	 *
	 * @param string $option_key
	 * @param mixed  $option_value
	 *
	 * @return $this
	 */
	public function set_option( $option_key, $option_value ) {
		$this->options[ $option_key ] = $option_value;

		return $this;
	}

	/**
	 * Remove an option.
	 *
	 * @param string $option_key
	 */
	public function remove_option( $option_key ) {
		unset( $this->options[ $option_key ] );
	}

	/**
	 * To documentation.
	 *
	 * @return string
	 */
	public function to_doc() {
		$doc = $this['name'] . "(" . $this['type'] . ")\n" . $this['description'] . "\n";

		return $doc;
	}

	/**
	 * To array.
	 *
	 * @return array
	 */
	public function to_array() {
		return $this->options;
	}
}