<?php
/**
 * Redux Dev Functions Class
 *
 * @class Redux_Core
 * @version 4.0.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Dev', false ) ) {

	/**
	 * Class Redux_Dev
	 */
	class Redux_Dev extends Redux_Class {

		/**
		 * Redux_Dev constructor.
		 *
		 * @param object $parent ReduxFramework pointer.
		 */
		public function __construct( $parent = null ) {
			parent::__construct( $parent );

			$this->load( $parent );
		}

		/**
		 * Load the admin news blast notice.
		 *
		 * @param object $core ReduxFramework object.
		 */
		public function load( $core ) {
			if ( true === $core->args['dev_mode'] || true === Redux_Helpers::is_local_host() ) {
				new Redux_Dashboard( $core );

				if ( ! isset( $GLOBALS['redux_notice_check'] ) || 0 === $GLOBALS['redux_notice_check'] ) {
					$params = array(
						'dir_name'    => 'notice',
						'server_file' => 'https://reduxframework.com/wp-content/uploads/redux/redux_notice.json',
						'interval'    => 3,
						'cookie_id'   => 'redux_blast',
					);

					new Redux_Newsflash( $core, $params );

					$GLOBALS['redux_notice_check'] = 1;
				}
			}
		}
	}
}
