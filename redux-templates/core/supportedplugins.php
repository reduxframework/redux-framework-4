<?php

namespace ReduxTemplates;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SupportedPlugins {

    protected static $plugins = array();
    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function init( $plugins = array() ) {

        self::set_plugins( $plugins );
        self::detect_versions();

//        self::detect_stackable();
//        self::detect_qubely();
//        self::detect_coblocks();
//        self::detect_kioken();
    }

    private static function detect_versions() {
        $all_plugins    = get_plugins();

        $active_plugins = get_option( 'active_plugins' );

        $data = array();
        foreach ( $active_plugins as $plugin ) {
            $slug          = explode( '/', $plugin )[0];
            $data[ $slug ] = $all_plugins[ $plugin ];
        }
//        print_r($data);

        foreach ( self::$plugins as $key => $plugin ) {
            $selector = false;
            if ( isset( $data[ $key ] ) ) {
                $selector = $key;
            } else {
                if ( isset( $plugin['slug'] ) && isset( $data[ $plugin['slug'] ] ) ) {
                    $selector = $plugin['slug'];
                }
            }
            if ( isset( $plugin['detect'] ) ) {
                if ( isset( $plugin['detect']['freemius'] ) && $plugin['detect']['freemius'] ) {
                    // Freemius Version Detection
                    if ( isset( $GLOBALS[ $plugin['detect']['freemius'] ] ) && ! empty( $GLOBALS[ $plugin['detect']['freemius'] ] ) ) {
                        $freemius  = $GLOBALS[ $plugin['detect']['freemius'] ];
                        $selector  = $freemius->get_plugin_basename();
                        $true_slug = explode( '/', $selector )[0];
                        if ( $true_slug !== $key ) {
                            self::$plugins[ $key ]['true_slug'] = $true_slug;
                        }
                        if ( isset( self::$plugins[ $key ]['free_slug'] ) ) {
                            continue;  // Let's only store the info on the free version
                        }
                        $plugin_info = $freemius->get_plugin_data();
                        if ( $selector && ! isset( $data[ $selector ]['Version'] ) ) {
                            self::$plugins[ $key ]['version'] = $plugin_info['Version'];
                        }
                        if ( $freemius->can_use_premium_code() ) {
                            self::$plugins[ $key ]['is_pro'] = true;
                        }
                        unset( self::$plugins[ $key ]['detect']['freemius'] );
                    }
                }
                if ( isset( $plugin['detect']['defined'] ) ) {
                    if ( ! empty( $plugin['detect']['defined'] ) ) {
                        foreach ( $plugin['detect']['defined'] as $key_name => $defined_name ) {
                            if ( defined( $defined_name ) && ! empty( constant( $defined_name ) ) ) {
                                self::$plugins[ $key ][ $key_name ] = constant( $defined_name );
                            }
                        }
                    }
                    unset( self::$plugins[ $key ]['detect']['defined'] );
                }
                if ( empty( self::$plugins[ $key ]['detect'] ) ) {
                    unset( self::$plugins[ $key ]['detect'] );
                }

            } else {
                if (isset($data[$key])) {
                    if (isset($data[$key]['Version'])) {
                        self::$plugins[ $key ]['version'] = $data[$key]['Version'];
                    }
                }
            }

        }
    }

    private static function set_plugins( $plugins = array() ) {
        self::$plugins = $plugins;

        return;
        self::$plugins = array(
            'acf-blocks-pro'             => array(
                'name' => 'ACF Blocks Pro',
                'url'  => 'https://www.acfblocks.com/pro/',
            ),
            'acf-blocks'                 => array(
                'name' => 'ACF Blocks',
                'url'  => 'https://wordpress.org/plugins/acf-blocks/',
            ),
            'acf'                        => array(
                'name' => 'Advanced Custom Fields',
                'url'  => 'https://wordpress.org/plugins/advanced-custom-fields/',
                'slug' => 'advanced-custom-fields',
            ),
            'acf-pro'                    => array(
                'name' => 'ACF Pro',
                'url'  => 'https://www.advancedcustomfields.com/pro/',
            ),
            'advgb'                      => array(
                'name' => 'Advanced Gutenberg',
                'url'  => 'https://www.joomunited.com/wordpress-products/advanced-gutenberg',
            ),
            'advanced-gutenberg-blocks'  => array(
                'name' => 'Adv. Gutenberg Blocks',
                'url'  => 'https://advanced-gutenberg-blocks.com/',
            ),
            'atomic-blocks'              => array(
                'name' => 'Atomic Blocks',
                'url'  => 'https://atomicblocks.com/',
            ),
            'blocks-css'                 => array(
                'name' => 'Blocks CSS',
                'url'  => 'https://wordpress.org/plugins/blocks-css/',
            ),
            'block-slider'               => array(
                'name' => 'Block Slider',
                'url'  => 'https://wordpress.org/plugins/block-slider',
            ),
            'coblocks'                   => array(
                'name' => 'CoBlocks',
                'url'  => 'https://coblocks.com/',
            ),
            'cpblocks'                   => array(
                'name'    => 'CP Blocks',
                'url'     => 'https://services.dwbooster.com/pricing',
                'has_pro' => true
            ),
            'essential-gutenberg-blocks' => array(
                'name' => 'Easy Blocks',
                'url'  => 'https://jeweltheme.com/shop/easy-gutenberg-blocks/',
                'slug' => 'easy-gutenberg-blocks'
            ),
            'block-options'              => array(
                'name' => 'EditorsKit',
                'url'  => 'https://wordpress.org/plugins/block-options/',
            ),
            'essential-blocks'           => array(
                'name' => 'Essential Blocks',
                'url'  => 'https://essential-blocks.com/',
            ),
            'getwid'                     => array(
                'name' => 'Getwid',
                'url'  => 'https://motopress.com/products/getwid/',
            ),
            'guteblock'                  => array(
                'name' => 'Guteblock',
                'url'  => 'https://guteblock.com/'
            ),
            'forms-gutenberg'            => array(
                'name' => 'Gutenberg Forms',
                'url'  => 'https://wordpress.org/plugins/forms-gutenberg',
            ),
            'themeisle-blocks'           => array(
                'name' => 'Gutenberg Blocks by Otter',
                'url'  => 'https://themeisle.com/plugins/otter-blocks'
            ),
            'gutenberg'                  => array(
                'name' => 'Gutenberg',
                'url'  => 'https://www.cssigniter.com/plugins/gutenberg/',
            ),
            'gutenbee'                   => array(
                'name' => 'Gutenbee',
                'url'  => 'https://www.cssigniter.com/plugins/gutenbee/',
            ),
            'kadence'                    => array(
                'name'    => 'Kadence Blocks',
                'url'     => 'https://www.kadencewp.com/product/kadence-gutenberg-blocks/',
                'has_pro' => true,
            ),
            'kioken'                     => array(
                'name'    => 'Kioken Blocks',
                'url'     => 'https://kiokengutenberg.com/',
                'has_pro' => true,
                'slug'    => 'kioken-blocks'
            ),
            'premium'                    => array(
                'name' => 'Premium Blocks',
                'url'  => 'https://premiumblocks.io/',
            ),
            'qubely'                     => array(
                'name'         => 'Qubely',
                'url'          => 'https://www.themeum.com/qubely-pricing/',
                'has_pro'      => true,
                'premium_slug' => 'qubely-pro',
            ),
            'qodeblock'                  => array(
                'name'    => 'Quodeblocks',
                'url'     => 'https://qodeblock.com/',
                'has_pro' => true,
            ),
            'block-slider'               => array(
                'name' => 'Block Slider',
                'url'  => 'https://wordpress.org/plugins/block-slider',
                'slug' => 'block-slider'
            ),
            'cwp'                        => array(
                'name' => 'Gutenberg Forms',
                'url'  => 'http://www.gutenbergforms.com',
                'slug' => 'forms-gutenberg'
            ),
            'snow-monkey-blocks'         => array(
                'name' => 'Snow Monkey Blocks',
                'url'  => 'https://wordpress.org/plugins/snow-monkey-blocks/'
            ),
            'ugb'                        => array(
                'name'         => 'Stackable',
                'url'          => 'https://wpstackable.com/premium/#pricing-table',
                'has_pro'      => true,
                'slug'         => 'stackable-ultimate-gutenberg-blocks',
                'premium_slug' => 'stackable-ultimate-gutenberg-blocks-premium',
            ),
            'uagb'                       => array(
                'name' => 'Ultimate Addons Blocks',
                'url'  => 'https://github.com/brainstormforce/ultimate-addons-for-gutenberg',
            ),
            'ub'                         => array(
                'name' => 'Ultimate Blocks',
                'url'  => 'https://ultimateblocks.com/',
            ),
        );
    }

    public static function get_plugins() {
        $instance = self::instance();
        if ( empty( $instance::$plugins ) ) {
            $instance->init();
        }

        return $instance::$plugins;
    }

    private static function detect_stackable() {
        if ( defined( 'STACKABLE_VERSION' ) ) {
            $slug                              = "ugb";
            self::$plugins[ $slug ]['version'] = STACKABLE_VERSION;
            self::$plugins[ $slug ]['is_pro']  = function_exists( 'sugb_fs' ) && sugb_fs()->can_use_premium_code(
            ) ? true : false;
        }
    }

    private static function detect_qubely() {
        if ( defined( 'QUBELY_VERSION' ) || defined( 'QUBELY_PRO_VERSION' ) ) {
            $slug                              = "qubely";
            self::$plugins[ $slug ]['version'] = defined(
                'QUBELY_PRO_VERSION'
            ) ? QUBELY_PRO_VERSION : QUBELY_VERSION;
            if ( isset( self::$plugins[ $slug ]['version'] ) && defined( 'QUBELY_PRO_VERSION' ) ) {
                self::$plugins[ $slug ]['is_pro'] = true;
            }

        }
    }

    private static function detect_coblocks() {
        if ( defined( 'COBLOCKS_VERSION' ) ) {
            $slug                              = "coblocks";
            self::$plugins[ $slug ]['version'] = COBLOCKS_VERSION;
        }
    }

    private static function detect_kioken() {
        if ( defined( 'KK_BLOCKS_VERSION' ) ) {
            $slug                              = "kioken";
            self::$plugins[ $slug ]['version'] = KK_BLOCKS_VERSION;
            self::$plugins[ $slug ]['is_pro']  = function_exists( 'kkb_fs' ) && kkb_fs()->can_use_premium_code(
            ) ? true : false;
        }
    }
}
