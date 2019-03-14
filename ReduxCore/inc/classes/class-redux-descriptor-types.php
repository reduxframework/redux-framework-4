<?php

/**
 * Class Redux_Descriptor_Types
 * @author Tofandel
 */
abstract class Redux_Descriptor_Types {
	const TEXT = 'text';
	const TEXTAREA = 'textarea';
	const BOOL = 'bool';
	const SLIDER = 'slider';
	const NUMBER = 'number';
	const RANGE = 'range';
	const OPTIONS = 'array';
	const WP_DATA = 'wp_data';
	const RADIO = 'radio';
	//Todo add more field types for the builder

	/**
	 * Get the available types of field.
	 *
	 * @return array
	 * @throws ReflectionException
	 */
	public static function get_types() {
		static $constCache;

		if ( ! isset( $constCache ) ) {
			$reflect    = new ReflectionClass( __CLASS__ );
			$constCache = $reflect->getConstants();
		}

		return $constCache;
	}


	/**
	 * Check if a type is in the list of available types.
	 *
	 * @param string $value
	 *
	 * @return bool
	 * @throws ReflectionException
	 */
	public static function is_valid_type( $value ) {
		return in_array( $value, self::get_types(), true );
	}

}