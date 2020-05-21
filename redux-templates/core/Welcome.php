<?php

namespace ReduxTemplates;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Welcome {

    // Constructor
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        //			add_action( "network_admin_menu", array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
//			add_action('admin_print_scripts', array($this,'remove_admin_notices'));
    }

    function remove_admin_notices() {
        global $wp_filter;
        unset( $wp_filter['admin_notices'] );
    }

    /**
     * Add Menu Page Callback
     *
     * @since 1.0.0
     */
    public function add_admin_menu() {
        add_menu_page(
            esc_html__( 'ReduxTemplates', 'reduxtemplates' ),
            esc_html__( 'ReduxTemplates', 'reduxtemplates' ),
            'manage_options',
            'reduxtemplates',
            array( $this, 'create_admin_page' ),
            'data:image/svg+xml;base64,' . base64_encode(
                file_get_contents( REDUXTEMPLATES_DIR_PATH . 'assets/img/logo.svg' )
            )
        );
    }

    /**
     * Register a setting and its sanitization callback.
     *
     * @since 1.0.0
     */
    public function register_settings() {
        register_setting( 'reduxtemplates_options', 'reduxtemplates_options', array( $this, 'sanitize' ) );
    }

    /**
     * Sanitization callback
     *
     * @since 1.0.0
     */
    public function sanitize( $options ) {
        if ( $options ) {
            if ( ! empty( $options['css_save_as'] ) ) {
                $options['css_save_as'] = sanitize_text_field( $options['css_save_as'] );
            }
        }

        return $options;
    }

    public function get_utm( $campaign = "get_pro", $content = "get_button" ) {
        $url = 'utm_source=welcome&utm_medium=settings';
        if ( $campaign ) {
            $url .= '&utm_campaign=' . $campaign;
        }
        if ( $content ) {
            $url .= '&utm_campaign=' . $campaign;
        }

        return $url;
    }


    /**
     * Settings page output
     *
     * @since 1.0.0
     */
    public function create_admin_page() {
        global $reduxtemplates_fs;


        ?>
        <div class="wrap hide" style="margin:0;padding:0;display:none;"><h2>&nbsp;</h2></div>
        <div class="wrap">
            <div class="reduxtemplates-options-section reduxtemplates-mt-20 reduxtemplates-mb-30">
                <div class="reduxtemplates-options-section-header">
                    <div class="reduxtemplates-header-left"
                         style="background-image: url(<?php echo REDUXTEMPLATES_DIR_URL . 'assets/img/logo.svg' ?>)">
                        <h2 class="reduxtemplates-options-section-title"><?php
                            esc_attr_e(
                                'Welcome to ReduxTemplates!', 'reduxtemplates'
                            );
                            sprintf(
                                'Version %1$s',
                                esc_attr( REDUXTEMPLATES_VERSION ),

                            ); ?>
                        </h2>
                    </div>
                </div>

                <div class="reduxtemplates-options-section-body-container">
                    <div class="section-body">
                        <div class="section-intro section-box grid">


                            <div class="section-intro-col">
                                <h3>👋 Get Started</h3>
                                <p><b>You now have ReduxTemplates installed for your Gutenberg block editor!</b></p>
                                <p>Building a page in WordPress has never been faster than when you use
                                    ReduxTemplates.</p>
                                <p><b>Need help?</b></p>
                                <p>Upgrade to Premium and our support team will be there to answer any questions you
                                    might
                                    have about the usage of ReduxTemplates.</p>
                                <p>
                                    <a href="<?php
                                    echo $reduxtemplates_fs->get_upgrade_url() . '&' . $this->get_utm(
                                            'get_started', 'get_pro'
                                        );
                                    ?>" class="components-button"
                                       style="display:inline-block;margin:0;margin-right: 15px;">Get
                                        ReduxTemplates Pro →</a>
                                    <a href="https://reduxtemplates.io/pro/?<?php
                                    echo $this->get_utm( 'get_started', 'learn_more' );
                                    ?>" target="_blank">Learn More →</a>
                                </p>
                            </div>
                            <div class="section-intro-col">
                                <p>
                                    <img style="max-width: 400px;"
                                         src="<?php echo REDUXTEMPLATES_DIR_URL . 'assets/img/welcome-tutorial.gif'; ?>"
                                         alt="Launch Demo"/>
                                </p>

                                <p class="text-center">
                                    <a href="<?php echo admin_url(
                                        'post-new.php?post_type=page#reduxtemplates_tour=1'
                                    ) ?>" title="<?php _e( 'Create a New Page' ) ?>"><?php _e(
                                            'Create a New Page', 'reduxtemplates'
                                        ) ?> →</a>
                                </p>
                            </div>

                        </div>
                        <div class="section-box">
                            <h3><?php _e( '🍺 Supported Plugins', 'reduxtemplates' ) ?></h3>
                            <p><b><?php _e(
                                        'Templates built with the careful chosen beautiful blocks from these vendors:',
                                        'reduxtemplates'
                                    ) ?></b></p>
                            <ul>
                                <li><b><?php _e( 'Getwid', 'reduxtemplates' ) ?></b></li>
                                <li><b><?php _e( 'GutenbergHub', 'reduxtemplates' ) ?></b></li>
                                <li><b><?php _e( 'Kioken', 'reduxtemplates' ) ?></b></li>
                                <li><b><?php _e( 'ShareABlock', 'reduxtemplates' ) ?></b></li>
                                <li><b><?php _e( 'Stackable', 'reduxtemplates' ) ?></b></li>
                                <li><b><?php _e( 'Qubely', 'reduxtemplates' ) ?></b></li>
                            </ul>
                        </div>
                    </div>
                    <div class="section-side">
                        <aside class="section-box premium-box">
                            <h3><?php _e( '🚀 ReduxTemplates Premium', 'reduxtemplates' ) ?></h3>
                            <p><b><?php _e( 'Priority Email & Forum Support', 'reduxtemplates' ) ?></b></p>
                            <ul>
                                <li><strong><?php _e( 'Weekly', 'reduxtemplates' ) ?></strong> <?php _e(
                                        'Updates to the Library', 'reduxtemplates'
                                    ) ?></li>
                                <li><strong><?php _e( 'Unlimited', 'reduxtemplates' ) ?></strong> <?php _e(
                                        ' Library Access', 'reduxtemplates'
                                    ) ?></li>
                                <li><strong><?php _e( '20+', 'reduxtemplates' ) ?></strong> <?php _e(
                                        ' Collections', 'reduxtemplates'
                                    ) ?></li>
                                <li><strong><?php _e( '125+', 'reduxtemplates' ) ?></strong> <?php _e(
                                        ' Page Templates', 'reduxtemplates'
                                    ) ?></li>
                                <li><strong><?php _e( '250+', 'reduxtemplates' ) ?></strong> <?php _e(
                                        ' Section Templates', 'reduxtemplates'
                                    ) ?></li>
                                <li><strong><?php _e( 'Priority', 'reduxtemplates' ) ?></strong> <?php _e(
                                        ' Support', 'reduxtemplates'
                                    ) ?></li>
                            </ul>
                            <p>
                                <a href="<?php echo $reduxtemplates_fs->get_upgrade_url() . '&' . $this->get_utm(
                                        'sidebar', 'get_pro'
                                    ); ?>" class="components-button"
                                   title="<?php _e( 'Get ReduxTemplates Pro', 'reduxtemplates' ) ?>"><?php _e(
                                        'Get ReduxTemplates Premium', 'reduxtemplates'
                                    ) ?></a>
                            </p>
                            <p class="text-center">
                                <a href="https://reduxtemplates.io/pro/?<?php
                                echo $this->get_utm( 'get_started', 'learn_more' );
                                ?>" title="<?php _e( 'Learn More' ) ?>" target="_blank"
                                   rel="noopener noreferrer"><?php _e( 'Learn More', 'reduxtemplates' ) ?> →</a>
                            </p>
                        </aside>
                        <aside class="section-box">
                            <h3><?php _e( '🗞 Updates', 'reduxtemplates' ) ?></h3>
                            <p><?php _e( 'Keep up to date by subscribing to our newsletter.' ) ?></p>
                            <a href="https://updates.reduxtemplates.io/welcome?<?php
                            echo $this->get_utm( 'updates', 'subscribe' );
                            ?>" target="_blank" aria-label="Subscribe" class="components-button">
                                Subscribe
                            </a>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    <?php }
}

