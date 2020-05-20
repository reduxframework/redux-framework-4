<?php

namespace StarterBlocks;

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
            esc_html__( 'StarterBlocks', 'starterblocks' ),
            esc_html__( 'StarterBlocks', 'starterblocks' ),
            'manage_options',
            'starterblocks',
            array( $this, 'create_admin_page' ),
            'data:image/svg+xml;base64,' . base64_encode(
                file_get_contents( STARTERBLOCKS_DIR_PATH . 'assets/img/logo.svg' )
            )
        );
    }

    /**
     * Register a setting and its sanitization callback.
     *
     * @since 1.0.0
     */
    public function register_settings() {
        register_setting( 'starterblocks_options', 'starterblocks_options', array( $this, 'sanitize' ) );
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
        global $starterblocks_fs;


        ?>
        <div class="wrap hide" style="margin:0;padding:0;display:none;"><h2>&nbsp;</h2></div>
        <div class="wrap">
            <div class="starterblocks-options-section starterblocks-mt-20 starterblocks-mb-30">
                <div class="starterblocks-options-section-header">
                    <div class="starterblocks-header-left"
                         style="background-image: url(<?php echo STARTERBLOCKS_DIR_URL . 'assets/img/logo.svg' ?>)">
                        <h2 class="starterblocks-options-section-title"><?php
                            esc_attr_e(
                                'Welcome to StarterBlocks!', 'starterblocks'
                            );
                            sprintf(
                                'Version %1$s',
                                esc_attr( STARTERBLOCKS_VERSION ),

                            ); ?>
                        </h2>
                    </div>
                </div>

                <div class="starterblocks-options-section-body-container">
                    <div class="section-body">
                        <div class="section-intro section-box grid">


                            <div class="section-intro-col">
                                <h3>👋 Get Started</h3>
                                <p><b>You now have StarterBlocks installed for your Gutenberg block editor!</b></p>
                                <p>Building a page in WordPress has never been faster than when you use
                                    StarterBlocks.</p>
                                <p><b>Need help?</b></p>
                                <p>Upgrade to Premium and our support team will be there to answer any questions you
                                    might
                                    have about the usage of StarterBlocks.</p>
                                <p>
                                    <a href="<?php
                                    echo $starterblocks_fs->get_upgrade_url() . '&' . $this->get_utm(
                                            'get_started', 'get_pro'
                                        );
                                    ?>" class="components-button"
                                       style="display:inline-block;margin:0;margin-right: 15px;">Get
                                        StarterBlocks Pro →</a>
                                    <a href="https://starterblocks.io/pro/?<?php
                                    echo $this->get_utm( 'get_started', 'learn_more' );
                                    ?>" target="_blank">Learn More →</a>
                                </p>
                            </div>
                            <div class="section-intro-col">
                                <p>
                                    <img style="max-width: 400px;"
                                         src="<?php echo STARTERBLOCKS_DIR_URL . 'assets/img/welcome-tutorial.gif'; ?>"
                                         alt="Launch Demo"/>
                                </p>

                                <p class="text-center">
                                    <a href="<?php echo admin_url(
                                        'post-new.php?post_type=page#starterblocks_tour=1'
                                    ) ?>" title="<?php _e( 'Create a New Page' ) ?>"><?php _e(
                                            'Create a New Page', 'starterblocks'
                                        ) ?> →</a>
                                </p>
                            </div>

                        </div>
                        <div class="section-box">
                            <h3><?php _e( '🍺 Supported Plugins', 'starterblocks' ) ?></h3>
                            <p><b><?php _e(
                                        'Templates built with the careful chosen beautiful blocks from these vendors:',
                                        'starterblocks'
                                    ) ?></b></p>
                            <ul>
                                <li><b><?php _e( 'Getwid', 'starterblocks' ) ?></b></li>
                                <li><b><?php _e( 'GutenbergHub', 'starterblocks' ) ?></b></li>
                                <li><b><?php _e( 'Kioken', 'starterblocks' ) ?></b></li>
                                <li><b><?php _e( 'ShareABlock', 'starterblocks' ) ?></b></li>
                                <li><b><?php _e( 'Stackable', 'starterblocks' ) ?></b></li>
                                <li><b><?php _e( 'Qubely', 'starterblocks' ) ?></b></li>
                            </ul>
                        </div>
                    </div>
                    <div class="section-side">
                        <aside class="section-box premium-box">
                            <h3><?php _e( '🚀 StarterBlocks Premium', 'starterblocks' ) ?></h3>
                            <p><b><?php _e( 'Priority Email & Forum Support', 'starterblocks' ) ?></b></p>
                            <ul>
                                <li><strong><?php _e( 'Weekly', 'starterblocks' ) ?></strong> <?php _e(
                                        'Updates to the Library', 'starterblocks'
                                    ) ?></li>
                                <li><strong><?php _e( 'Unlimited', 'starterblocks' ) ?></strong> <?php _e(
                                        ' Library Access', 'starterblocks'
                                    ) ?></li>
                                <li><strong><?php _e( '20+', 'starterblocks' ) ?></strong> <?php _e(
                                        ' Collections', 'starterblocks'
                                    ) ?></li>
                                <li><strong><?php _e( '125+', 'starterblocks' ) ?></strong> <?php _e(
                                        ' Page Templates', 'starterblocks'
                                    ) ?></li>
                                <li><strong><?php _e( '250+', 'starterblocks' ) ?></strong> <?php _e(
                                        ' Section Templates', 'starterblocks'
                                    ) ?></li>
                                <li><strong><?php _e( 'Priority', 'starterblocks' ) ?></strong> <?php _e(
                                        ' Support', 'starterblocks'
                                    ) ?></li>
                            </ul>
                            <p>
                                <a href="<?php echo $starterblocks_fs->get_upgrade_url() . '&' . $this->get_utm(
                                        'sidebar', 'get_pro'
                                    ); ?>" class="components-button"
                                   title="<?php _e( 'Get StarterBlocks Pro', 'starterblocks' ) ?>"><?php _e(
                                        'Get StarterBlocks Premium', 'starterblocks'
                                    ) ?></a>
                            </p>
                            <p class="text-center">
                                <a href="https://starterblocks.io/pro/?<?php
                                echo $this->get_utm( 'get_started', 'learn_more' );
                                ?>" title="<?php _e( 'Learn More' ) ?>" target="_blank"
                                   rel="noopener noreferrer"><?php _e( 'Learn More', 'starterblocks' ) ?> →</a>
                            </p>
                        </aside>
                        <aside class="section-box">
                            <h3><?php _e( '🗞 Updates', 'starterblocks' ) ?></h3>
                            <p><?php _e( 'Keep up to date by subscribing to our newsletter.' ) ?></p>
                            <a href="https://updates.starterblocks.io/welcome?<?php
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

