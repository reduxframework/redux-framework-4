<?php
/**
 * Redux Welcome Class
 *
 * @class Redux_Core
 * @version 4.0.0
 * @package Redux Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Redux_ConnectionBanner', false ) ) {

	/**
	 * Class Redux_ConnectionBanner
	 */
    class Redux_ConnectionBanner {
        /**
         * Plugin version, used for cache-busting of style and script file references.
         *
         * @since   1.0.0
         * @var     string
         */
        protected $version = '1.0.0';

        /**
         * @var Redux_ConnectionBanner
         **/
        private static $instance = null;

        static function init() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new Redux_ConnectionBanner();
            }

            return self::$instance;
        }

        /**
         * Redux_ConnectionBanner constructor.
         *
         * Since we call the Redux_ConnectionBanner:init() method from the `Redux` class, and after
         * the admin_init action fires, we know that the admin is initialized at this point.
         */
        private function __construct() {
            add_action( 'current_screen', array( $this, 'maybe_initialize_hooks' ) );
            add_action( 'admin_notices', array( $this, 'render_banner' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_banner_scripts' ) );
            add_action( 'admin_notices', array( $this, 'render_connect_prompt_full_screen' ) );
            add_action( 'admin_head', array( $this, 'admin_head' ) );
        }

        /**
         * Given a string for the the banner was added, and an int that represents the slide to
         * a URL for, this function returns a connection URL with a from parameter that will
         * support split testing.
         *
         * @since 7.2   Event key format is now banner-connect-banner-72-dashboard or connect-banner-72-plugins.
         *              The param $slide_num was removed since we removed all slides but the first one.
         * @since 4.4.0
         *
         * @param string $version_banner_added A short version of when the banner was added. Ex. 44
         *
         * @return string
         */
        function build_connect_url_for_slide( $version_banner_added ) {
            global $current_screen;
            /* $url = Redux::init()->build_connect_url(
                true,
                false,
                sprintf( 'connect-banner-%s-%s', $version_banner_added, $current_screen->base )
            );*/
            return add_query_arg( 'auth_approved', 'true', $url );
        }

        /**
         * Will initialize hooks to display the new (as of 4.4) connection banner if the current user can
         * connect Redux, if Redux has not been deactivated, and if the current page is the plugins page.
         *
         * This method should not be called if the site is connected to WordPress.com or if the site is in development mode.
         *
         * @since 4.4.0
         * @since 4.5.0 Made the new (as of 4.4) connection banner display to everyone by default.
         * @since 5.3.0 Running another split test between 4.4 banner and a new one in 5.3.
         * @since 7.2   B test was removed.
         *
         * @param $current_screen
         */
        function maybe_initialize_hooks( $current_screen ) {

            // Kill if banner has been dismissed
            /* if ( Redux_Options::get( 'dismissed_ConnectionBanner' ) ) {
                return;
            }*/

            // Don't show the connect notice anywhere but the plugins.php after activating
            if ( 'plugins' !== $current_screen->base && 'dashboard' !== $current_screen->base ) {
                return;
            }

            if ( ! current_user_can( 'Redux_connect' ) ) {
                return;
            }

            add_action( 'admin_notices', array( $this, 'render_banner' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_banner_scripts' ) );

            if ( Redux::state( 'network_nag' ) ) {
                add_action( 'network_admin_notices', array( $this, 'network_connect_notice' ) );
            }

            // Only fires immediately after plugin activation
            if ( get_transient( 'activated_Redux' ) ) {
                add_action( 'admin_notices', array( $this, 'render_connect_prompt_full_screen' ) );
                delete_transient( 'activated_Redux' );
            }
        }

        /**
         * Enqueues JavaScript for new connection banner.
         *
         * @since 4.4.0
         */
        public function enqueue_banner_scripts() {
            wp_enqueue_script(
                'Redux-connection-banner-js',
                '_inc/build/Redux-connection-banner.min.js',
/*                Assets::get_file_url_for_environment(
                    '_inc/build/Redux-connection-banner.min.js',
                    '_inc/Redux-connection-banner.js'
                ),*/
                array( 'jquery' ),
                $this->version,
                true
            );

            wp_localize_script(
                'Redux-connection-banner-js',
                'redux-banner',
                array(
                    'ajax_url'              => admin_url( 'admin-ajax.php' ),
                    'connectionBannerNonce' => wp_create_nonce( 'reduxion-banner-nonce' ),
                )
            );
        }

        /**
         * Enqueues JavaScript and CSS for new connect-in-place flow.
         *
         * @since 7.7
         */
        public static function enqueue_connect_button_scripts() {
            global $is_safari;

            wp_enqueue_script(
                'Redux-connect-button',
                // Assets::get_file_url_for_environment(
                    // '_inc/build/connect-button.min.js',
                    '_inc/connect-button.js',
                // ),
                array( 'jquery' ),
                $this->version,
                true
            );

            wp_enqueue_style(
                'Redux-connect-button',
                // Assets::get_file_url_for_environment(
                    // 'css/Redux-connect.min.css',
                    'css/Redux-connect.css'
                // )
            );

            $ReduxApiUrl = wp_parse_url( Redux::connection()->api_url( '' ) );

            // Due to the limitation in how 3rd party cookies are handled in Safari,
            // we're falling back to the original flow on Safari desktop and mobile.
            if ( $is_safari ) {
                $force_variation = 'original';
            } elseif ( Constants::is_true( 'Redux_SHOULD_USE_CONNECTION_IFRAME' ) ) {
                $force_variation = 'in_place';
            } elseif ( Constants::is_defined( 'Redux_SHOULD_USE_CONNECTION_IFRAME' ) ) {
                $force_variation = 'original';
            } else {
                $force_variation = null;
            }

            $tracking = new Automattic\Redux\Tracking();
            $identity = $tracking->tracks_get_identity( get_current_user_id() );

            wp_localize_script(
                'Redux-connect-button',
                'reduxConnect',
                array(
                    'apiBaseUrl'            => esc_url_raw( rest_url( 'Redux/v4' ) ),
                    'registrationNonce'     => wp_create_nonce( 'Redux-registration-nonce' ),
                    'apiNonce'              => wp_create_nonce( 'wp_rest' ),
                    'apiSiteDataNonce'      => wp_create_nonce( 'wp_rest' ),
                    'buttonTextRegistering' => __( 'Loading...', 'Redux' ),
                    'ReduxApiDomain'      => $ReduxApiUrl['scheme'] . '://' . $ReduxApiUrl['host'],
                    'forceVariation'        => $force_variation,
                    'connectInPlaceUrl'     => Redux::admin_url( 'page=Redux#/setup' ),
                    'dashboardUrl'          => Redux::admin_url( 'page=Redux#/dashboard' ),
                    'plansPromptUrl'        => Redux::admin_url( 'page=Redux#/plans-prompt' ),
                    'identity'              => $identity,
                    'preFetchScript'        => plugins_url( '_inc/build/admin.js', Redux__PLUGIN_FILE ) . '?ver=' . Redux__VERSION,
                )
            );
        }

		/**
		 * Hide Individual Dashboard Pages
		 *
		 * @access public
		 * @since  1.4
		 * @return void
		 */
		public function admin_head() {
			?>

			<link
				rel='stylesheet' id='redux-banner-css' <?php // phpcs:ignore WordPress.WP.EnqueuedResources ?>
				href='<?php echo esc_url( Redux_Core::$url ); ?>inc/welcome/css/redux-banner.css'
				type='text/css' media='all'/>
			<?php
        }

        /**
         * Renders the new connection banner as of 4.4.0.
         *
         * @since 7.2   Copy and visual elements reduced to show the new focus of Redux on Security and Performance.
         * @since 4.4.0
         */
        function render_banner() {
            ?>
            <div id="message" class="updated redux-banner-container">
                <div class="redux-banner-container-top-text">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="0" fill="none" width="24" height="24"/><g><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2l-.5-6h3l-.5 6z"/></g></svg>
                    <span>
                        <?php esc_html_e( 'You’re almost done. Set up Redux to enable powerful security and performance tools for WordPress.', 'Redux' ); ?>
                    </span>
                </div>
                <div class="redux-banner-inner-container">
                    <span
                        class="notice-dismiss connection-banner-dismiss"
                        title="<?php esc_attr_e( 'Dismiss this notice', 'Redux' ); ?>">
                    </span>

                    <div class="redux-banner-content-container">

                        <!-- slide 1: intro -->
                        <div class="redux-banner-slide redux-banner-slide-one redux-slide-is-active">

                            <div class="redux-banner-content-icon redux-illo">
                                <?php
                                // $logo = new Logo();
                                // echo $logo->render();
                                ?>
                                <img
                                    src="<?php echo plugins_url( 'images/Redux-powering-up.svg', Redux__PLUGIN_FILE ); ?>"
                                    class="redux-banner-hide-phone-and-smaller"
                                    alt="
                                    <?php
                                    esc_attr_e(
                                        'Redux premium services offer even more powerful performance, security, ' .
                                        'and revenue tools to help you keep your site safe, fast, and help generate income.',
                                        'Redux'
                                    );
                                    ?>
                                    "
                                    height="auto"
                                    width="225"
                                    />
                            </div>

                            <div class="redux-banner-slide-text">
                                <h2><?php esc_html_e( 'Simplify your site security and performance with Redux', 'Redux' ); ?></h2>

                                <p>
                                    <?php
                                    esc_html_e(
                                        'Redux protects you against brute force attacks and unauthorized logins. Basic protection ' .
                                        'is always free, while premium plans add unlimited backups of your whole site, spam protection, ' .
                                        'malware scanning, and automated fixes.',
                                        'Redux'
                                    );
                                    ?>
                                </p>

                                <p>
                                    <?php
                                    esc_html_e(
                                        'Activate site accelerator tools and watch your page load times decrease—we’ll ' .
                                        'optimize your images and serve them from our own powerful global network of servers, ' .
                                        'and speed up your mobile site to reduce bandwidth usage.',
                                        'Redux'
                                    );
                                    ?>
                                </p>

                                <div class="redux-button-container">
                                    <span class="redux-tos-blurb"><?php $this->render_tos_blurb(); ?></span>
                                    <a
                                            href="<?php echo esc_url( $this->build_connect_url_for_slide( '72' ) ); ?>"
                                            class="dops-button is-primary redux-alt-connect-button">
                                        <?php esc_html_e( 'Set up Redux', 'Redux' ); ?>
                                    </a>
                                </div>

                            </div>
                        </div> <!-- end slide 1 -->
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Renders the full-screen connection prompt.  Only shown once and on plugin activation.
         */
        public static function render_connect_prompt_full_screen() {
            $current_screen = get_current_screen();
            if ( 'plugins' === $current_screen->base ) {
                $bottom_connect_url_from = 'full-screen-prompt';
            } else {
                $bottom_connect_url_from = 'landing-page-bottom';
            }
            ?>
            <div class="redux-banner-full-container"><div class="redux-banner-full-container-card">

                    <?php if ( 'plugins' === $current_screen->base ) : ?>

                        <div class="redux-banner-full-dismiss">
                            <svg class="redux-banner-svg-dismiss" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Dismiss Redux Connection Window</title><rect x="0" fill="none" /><g><path d="M17.705 7.705l-1.41-1.41L12 10.59 7.705 6.295l-1.41 1.41L10.59 12l-4.295 4.295 1.41 1.41L12 13.41l4.295 4.295 1.41-1.41L13.41 12l4.295-4.295z"/></g></svg>
                        </div>
                    <?php endif; ?>

                    <div class="redux-banner-full-step-header">
                        <h2 class="redux-banner-full-step-header-title"><?php esc_html_e( 'Activate essential WordPress security and performance tools by setting up Redux', 'Redux' ); ?></h2>
                    </div>


                    <p class="redux-banner-full-button-container">
                        <a href=""
                        class="dops-button is-primary redux-button">
                            <?php esc_html_e( 'Set up Redux', 'Redux' ); ?>
                        </a>
                    </p>

                    <div class="redux-banner-full-row" id="Redux-connection-cards">
                        <div class="redux-banner-full-slide">
                            <div class="redux-banner-full-slide-card illustration">
                                <img
                                        src="<?php echo plugins_url( 'images/security.svg', Redux__PLUGIN_FILE ); ?>"
                                        alt="<?php esc_attr_e( 'Security & Backups', 'Redux' ); ?>"
                                />
                            </div>
                            <div class="redux-banner-full-slide-card">
                                <p>
                                <?php
                                    esc_html_e(
                                        'Redux protects you against brute force attacks and unauthorized logins. ' .
                                        'Basic protection is always free, while premium plans add unlimited backups of your whole site, ' .
                                        'spam protection, malware scanning, and automated fixes.',
                                        'Redux'
                                    );
                                ?>
                                    </p>
                            </div>
                        </div>
                        <div class="redux-banner-full-slide">
                            <div class="redux-banner-full-slide-card illustration">
                                <img
                                        src="<?php echo plugins_url( 'images/Redux-speed.svg', Redux__PLUGIN_FILE ); ?>"
                                        alt="<?php esc_attr_e( 'Built-in Performance', 'Redux' ); ?>"
                                />
                            </div>
                            <div class="redux-banner-full-slide-card">
                                <p>
                                <?php
                                    esc_html_e(
                                        'Activate site accelerator tools and watch your page load times decrease—' .
                                        "we'll optimize your images and serve them from our own powerful global network of servers, " .
                                        'and speed up your mobile site to reduce bandwidth usage.',
                                        'Redux'
                                    );
                                ?>
                                    </p>
                            </div>
                        </div>
                    </div>

                    <?php if ( 'plugins' === $current_screen->base ) : ?>
                        <p class="redux-banner-full-dismiss-paragraph">
                            <a>
                                <?php
                                echo esc_html_x(
                                    'Not now, thank you.',
                                    'a link that closes the modal window that offers to connect Redux',
                                    'Redux'
                                );
                                ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }

        /**
         * Renders the legacy network connection banner.
         */
        function network_connect_notice() {
            ?>
            <div id="message" class="updated Redux-message">
                <div class="squeezer">
                    <h2>
                        <?php
                            echo wp_kses(
                                __(
                                    '<strong>Redux is activated!</strong> Each site on your network must be connected individually by an admin on that site.',
                                    'Redux'
                                ),
                                array( 'strong' => array() )
                            );
                        ?>
                    </h2>
                </div>
            </div>
            <?php
        }

        /**
         * Prints a TOS blurb used throughout the connection prompts.
         *
         * @since 4.0
         *
         * @echo string
         */
        function render_tos_blurb() {
            printf(
                wp_kses(
                    /* Translators: placeholders are links. */
                    __( 'By clicking the <strong>Set up Jetpack</strong> button, you agree to our <a href="" target="_blank" rel="noopener noreferrer">Terms of Service</a> and to <a href="" target="_blank" rel="noopener noreferrer">share details</a> with WordPress.com.', 'redux' ),
                    array(
                        'a'      => array(
                            'href'   => array(),
                            'target' => array(),
                            'rel'    => array(),
                        ),
                        'strong' => true,
                    )
                ),
                // esc_url( Redirect::get_url( 'wpcom-tos' ) ),
                // esc_url( Redirect::get_url( 'redux-support-what-data-does-redux-sync' ) )
            );
        }

        public function get_file_url_for_environment( $min_path, $non_min_path ) {
            $path = ( Jetpack_Constants::is_defined( 'SCRIPT_DEBUG' ) && Jetpack_Constants::get_constant( 'SCRIPT_DEBUG' ) )
                ? $non_min_path
                : $min_path;
    
            $url = plugins_url( $path, Jetpack_Constants::get_constant( 'JETPACK__PLUGIN_FILE' ) );
    
            /**
             * Filters the URL for a file passed through the get_file_url_for_environment function.
             *
             * @since 8.1.0
             *
             * @package assets
             *
             * @param string $url The URL to the file.
             * @param string $min_path The minified path.
             * @param string $non_min_path The non-minified path.
             */
            return apply_filters( 'jetpack_get_file_for_environment', $url, $min_path, $non_min_path );
        }
    }
}
