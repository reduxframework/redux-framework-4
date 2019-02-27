<?php
/**
 * Redux Filesystem Class
 *
 * @class Redux_Filesystem
 * @version 4.0.0
 * @package Redux Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Filesystem', false ) ) {

	/**
	 * Class Redux_Filesystem
	 */
	class Redux_Filesystem {

		/**
		 * Instance of this class.
		 *
		 * @since    1.0.0
		 * @var      object
		 */
		protected static $instance = null;

		/**
		 * WP Filesystem object.
		 *
		 * @var object
		 */
		protected static $direct = null;

		/**
		 * File system credentials.
		 *
		 * @var array
		 */
		private $creds = array();

		/**
		 * ReduxFramework object pointer.
		 *
		 * @var object
		 */
		public $parent = null;

		/**
		 * Return an instance of this class.
		 *
		 * @param object $parent ReduxFramework pointer.
		 *
		 * @since     1.0.0
		 * @return    object    A single instance of this class.
		 */
		public static function get_instance( $parent = null ) {

			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			if ( null !== $parent ) {
				self::$instance->parent = $parent;
			}

			return self::$instance;
		}

		/**
		 * Build FTP form.
		 */
		public function ftp_form() {
			if ( isset( $this->parent->ftp_form ) && ! empty( $this->parent->ftp_form ) ) {
				echo '<div class="wrap">';
				echo '<div class="error">';
				echo '<p>';
				// translators: %1$s: Upload URL.  %2$s: Codex URL.
				echo '<strong>' . esc_html__( 'File Permission Issues', 'redux-framework' ) . '</strong><br/>' . sprintf( esc_html__( 'We were unable to modify required files. Please ensure that %1$s has the proper read-write permissions, or modify your wp-config.php file to contain your FTP login credentials as %2$s.', 'redux-framework' ), '<code>' . esc_url( Redux_Functions_Ex::wp_normalize_path( trailingslashit( WP_CONTENT_DIR ) ) . '/uploads/' ) . '</code>', ' <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">' . esc_html__( 'outlined here', 'redux-framework' ) . '</a>' );
				echo '</p>';
				echo '</div>';
				echo '<h2></h2>';
				echo '</div>';
			}
		}

		/**
		 * Init WO Filesystem.
		 *
		 * @param string $form_url Form URL.
		 * @param string $method Connect method.
		 * @param bool   $context Context.
		 * @param null   $fields Fields.
		 *
		 * @return bool
		 */
		public function filesystem_init( $form_url, $method = '', $context = false, $fields = null ) {
			global $wp_filesystem;

			if ( ! empty( $this->creds ) ) {
				return true;
			}

			ob_start();

			$this->creds = request_filesystem_credentials( $form_url, $method, false, $context );

			/* first attempt to get credentials */
			if ( false === $this->creds ) {
				$this->creds            = array();
				$this->parent->ftp_form = ob_get_contents();
				ob_end_clean();

				/**
				 * If we comes here - we don't have credentials
				 * so the request for them is displaying
				 * no need for further processing
				 * */
				return false;
			}

			/* now we got some credentials - try to use them */
			if ( ! WP_Filesystem( $this->creds ) ) {
				$this->creds = array();
				/* incorrect connection data - ask for credentials again, now with error message */
				request_filesystem_credentials( $form_url, '', true, $context );
				$this->parent->ftp_form = ob_get_contents();
				ob_end_clean();

				return false;
			}

			return true;
		}

		/**
		 * Load WP fiesystem directly.
		 */
		public static function load_direct() {
			if ( null === self::$direct ) {
				require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
				require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';

				self::$direct = new WP_Filesystem_Direct( array() );
			}
		}

		/**
		 * Execute filesystem request.
		 *
		 * @param string $action Action to perform.
		 * @param string $file File to perform upon.
		 * @param string $params Argument for action.
		 *
		 * @return bool|void
		 */
		public function execute( $action, $file = '', $params = '' ) {
			if ( empty( $this->parent->args ) ) {
				return;
			}

			if ( ! empty( $params ) ) {

				// phpcs:ignore WordPress.PHP.DontExtract
				extract( $params );
			}

			// Setup the filesystem with creds.
			require_once ABSPATH . '/wp-admin/includes/template.php';
			require_once ABSPATH . '/wp-includes/pluggable.php';
			require_once ABSPATH . '/wp-admin/includes/file.php';

			if ( 'submenu' === $this->parent->args['menu_type'] ) {
				$page_parent = $this->parent->args['page_parent'];
				$base        = $page_parent . '?page=' . $this->parent->args['page_slug'];
			} else {
				$base = 'admin.php?page=' . $this->parent->args['page_slug'];
			}

			$url = wp_nonce_url( $base, 'redux-options' );

			$this->filesystem_init( $url, 'direct', dirname( $file ) );

			if ( ! file_exists( Redux_Core::$upload_dir ) ) {
				$this->do_action( 'mkdir', Redux_Core::$upload_dir );
			}

			$hash_path = trailingslashit( Redux_Core::$upload_dir ) . 'hash';
			if ( ! file_exists( $hash_path ) ) {
				$this->do_action(
					'put_contents',
					$hash_path,
					array(
						'content' => Redux_Helpers::get_hash(),
					)
				);
			}

			$version_path = trailingslashit( Redux_Core::$upload_dir ) . 'version';
			if ( ! file_exists( $version_path ) ) {
				$this->do_action(
					'put_contents',
					$version_path,
					array(
						'content' => Redux_Core::$version,
					)
				);
			}

			$index_path = trailingslashit( Redux_Core::$upload_dir ) . 'index.php';
			if ( ! file_exists( $index_path ) ) {
				$this->do_action(
					'put_contents',
					$index_path,
					array(
						'content' => '<?php' . PHP_EOL . '// Silence is golden.',
					)
				);
			}

			return $this->do_action( $action, $file, $params );
		}

		/**
		 * Do requested filesystem action.
		 *
		 * @param string $action Requested action.
		 * @param string $file File to perform action upon.
		 * @param string $params Action arguments.
		 *
		 * @return bool|void
		 */
		public function do_action( $action, $file = '', $params = '' ) {
			if ( ! empty( $params ) ) {

				// phpcs:ignore WordPress.PHP.DontExtract
				extract( $params );
			}

			global $wp_filesystem;

			if ( ! isset( $params['chmod'] ) || ( isset( $params['chmod'] ) && empty( $params['chmod'] ) ) ) {
				if ( defined( 'FS_CHMOD_FILE' ) ) {
					$chmod = FS_CHMOD_FILE;
				} else {
					$chmod = 0644;
				}
			}
			$res = false;
			if ( ! isset( $recursive ) ) {
				$recursive = false;
			}

			// Do unique stuff.
			if ( 'mkdir' === $action ) {
				if ( defined( 'FS_CHMOD_DIR' ) ) {
					$chmod = FS_CHMOD_DIR;
				} else {
					$chmod = 0755;
				}

				wp_mkdir_p( $file );

				$res = file_exists( $file );
				if ( ! $res ) {
					// phpcs:ignore Generic.Strings.UnnecessaryStringConcat
					call_user_func( 'mk' . 'dir', $file, $chmod, true );
					$res = file_exists( $file );
				}

				$index_path = trailingslashit( $file ) . 'index.php';
				if ( ! file_exists( $index_path ) ) {
					$wp_filesystem->put_contents(
						$index_path,
						'<?php' . PHP_EOL . '// Silence is golden.',
						FS_CHMOD_FILE // predefined mode settings for WP files.
					);
				}
			} elseif ( 'rmdir' === $action ) {
				$res = $wp_filesystem->rmdir( $file, $recursive );
			} elseif ( 'copy' === $action && ! isset( $this->filesystem->killswitch ) ) {
				if ( isset( $this->parent->ftp_form ) && ! empty( $this->parent->ftp_form ) ) {
					$res = copy( $file, $destination );

					if ( $res ) {
						chmod( $destination, $chmod );
					}
				} else {
					$res = $wp_filesystem->copy( $file, $destination, $overwrite, $chmod );
				}
			} elseif ( 'move' === $action && ! isset( $this->filesystem->killswitch ) ) {
				$res = $wp_filesystem->copy( $file, $destination, $overwrite );
			} elseif ( 'delete' === $action ) {
				$res = $wp_filesystem->delete( $file, $recursive );
			} elseif ( 'rmdir' === $action ) {
				$res = $wp_filesystem->rmdir( $file, $recursive );
			} elseif ( 'dirlist' === $action ) {
				if ( ! isset( $include_hidden ) ) {
					$include_hidden = true;
				}
				$res = $wp_filesystem->dirlist( $file, $include_hidden, $recursive );
			} elseif ( 'put_contents' === $action && ! isset( $this->filesystem->killswitch ) ) {
				// Write a string to a file.
				if ( isset( $this->parent->ftp_form ) && ! empty( $this->parent->ftp_form ) ) {
					self::load_direct();
					$res = self::$direct->put_contents( $file, $content, $chmod );
				} else {
					$res = $wp_filesystem->put_contents( $file, $content, $chmod );
				}
			} elseif ( 'chown' === $action ) {
				// Changes file owner.
				if ( isset( $owner ) && ! empty( $owner ) ) {
					$res = $wp_filesystem->chmod( $file, $chmod, $recursive );
				}
			} elseif ( 'owner' === $action ) {
				// Gets file owner.
				$res = $wp_filesystem->owner( $file );
			} elseif ( 'chmod' === $action ) {
				if ( ! isset( $params['chmod'] ) || ( isset( $params['chmod'] ) && empty( $params['chmod'] ) ) ) {
					$chmod = false;
				}

				$res = $wp_filesystem->chmod( $file, $chmod, $recursive );
			} elseif ( 'get_contents' === $action ) {
				// Reads entire file into a string.
				if ( isset( $this->parent->ftp_form ) && ! empty( $this->parent->ftp_form ) ) {
					self::load_direct();
					$res = self::$direct->get_contents( $file );
				} else {
					$res = $wp_filesystem->get_contents( $file );
				}
			} elseif ( 'get_contents_array' === $action ) {
				// Reads entire file into an array.
				$res = $wp_filesystem->get_contents_array( $file );
			} elseif ( 'object' === $action ) {
				$res = $wp_filesystem;
			} elseif ( 'unzip' === $action ) {
				$unzipfile = unzip_file( $file, $destination );
				if ( $unzipfile ) {
					$res = true;
				}
			}

			if ( ! $res ) {
				if ( 'dirlist' === $action ) {
					if ( empty( $res ) || false === $res || '' === $res ) {
						return;
					}

					if ( is_array( $res ) && empty( $res ) ) {
						return;
					}

					if ( ! is_array( $res ) ) {
						if ( count( glob( "$file*" ) ) === 0 ) {
							return;
						}
					}
				}

				$this->killswitch = true;

				// translators: %1$s: Upload URL.  %2$s: Codex URL.
				$msg = '<strong>' . esc_html__( 'File Permission Issues', 'redux-framework' ) . '</strong><br/>' . sprintf( esc_html__( 'We were unable to modify required files. Please ensure that %1$s has the proper read-write permissions, or modify your wp-config.php file to contain your FTP login credentials as %2$s.', 'redux-framework' ), '<code>' . esc_url( Redux_Functions_Ex::wp_normalize_path( trailingslashit( WP_CONTENT_DIR ) ) ) . '/uploads/</code>', '<a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">' . esc_html__( 'outlined here', 'redux-framework' ) . '</a>' );

				$data = array(
					'parent'  => self::$instance->parent,
					'type'    => 'error',
					'msg'     => $msg,
					'id'      => 'redux-wp-login',
					'dismiss' => false,
				);

				Redux_Admin_Notices::set_notice( $data );
			}

			return $res;
		}

	}

	Redux_Filesystem::get_instance();
}
