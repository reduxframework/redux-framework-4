<?php
/**
 * Register an autoloader for custom mu-plugins.
 *
 * @package redux-framework
 */

/**
 * Class Autoloader
 *
 * @package altis/core
 */
class Redux_Autoloader {
	const NS_SEPARATOR = '\\';

	/**
	 * Prefix to validate against.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * String length of the prefix.
	 *
	 * @var int
	 */
	protected $prefix_length;

	/**
	 * Path to validate.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Autoloader constructor.
	 *
	 * @param string $prefix Prefix to validate against.
	 * @param string $path Path to validate.
	 */
	public function __construct( $prefix, $path ) {
		$this->prefix        = $prefix;
		$this->prefix_length = strlen( $prefix );
		$this->path          = trailingslashit( $path );
	}

	/**
	 * Load a class file if it matches our criteria.
	 *
	 * @param string $class Class to test and/or load.
	 */
	public function load( $class ) {
		if ( strpos( $class, $this->prefix . self::NS_SEPARATOR ) !== 0 ) {
			return;
		}

		// Strip prefix from the start (ala PSR-4).
		$class = substr( $class, $this->prefix_length + 1 );
		$class = strtolower( $class );
		$file  = '';

		// Split on namespace separator.
		$last_ns_pos = strripos( $class, self::NS_SEPARATOR );
		if ( false !== $last_ns_pos ) {
			$namespace = substr( $class, 0, $last_ns_pos );
			$class     = substr( $class, $last_ns_pos + 1 );
			$file      = str_replace( self::NS_SEPARATOR, DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
		}
		$file .= 'class-' . str_replace( '_', '-', $class ) . '.php';

		$path = $this->path . $file;

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}
