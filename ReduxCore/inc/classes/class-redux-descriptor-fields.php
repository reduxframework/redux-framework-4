<?php

/**
 * Class Redux_Descriptor_Fields
 * Used to map fields of field descriptors.
 *
 * @author Tofandel
 */
class Redux_Descriptor_Fields implements ArrayAccess {
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
			'required'    => (bool) $this->required
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
		static::$order            = $order;
		$this->options[ 'order' ] = (float) $order;

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
		$this->options[ 'group' ] = $group;

		return $this;
	}

	/**
	 * Set an option.
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
	 * Get an option.
	 *
	 * @param $option_key
	 *
	 * @return mixed
	 */
	public function get_option( $option_key ) {
		return $this->options[ $option_key ];
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
		$doc = $this[ 'name' ] . "(" . $this[ 'type' ] . ")\n" . $this[ 'description' ] . "\n";

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

	/**
	 * Whether a offset exists
	 * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset ) {
		return array_key_exists( $offset, $this->options );
	}

	/**
	 * Offset to retrieve
	 * @link  http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet( $offset ) {
		return $this->options[ $offset ];
	}

	/**
	 * Offset to set
	 * @link  http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 *                      </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 *                      </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet( $offset, $value ) {
		$this->options[ $offset ] = $value;
	}

	/**
	 * Offset to unset
	 * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset( $offset ) {
		unset( $this->options[ $offset ] );
	}
}