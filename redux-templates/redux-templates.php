<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define Version
define( 'REDUXTEMPLATES_VERSION', Redux_Core::$version );

// Define File DIR
define( 'REDUXTEMPLATES_FILE', __FILE__ );

// Define Dir URL
define( 'REDUXTEMPLATES_DIR_URL', plugin_dir_url( __FILE__ ) );

// Define Physical Path
define( 'REDUXTEMPLATES_DIR_PATH', plugin_dir_path( __FILE__ ) );

// Version Check & Include Core
if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
    add_action( 'admin_notices', 'ReduxTemplates\Notices\Notices::php_error_notice' ); // PHP Version Check
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.5', '>=' ) ) {
    add_action(
        'admin_notices', 'ReduxTemplates\Notices\Notices::wordpress_error_notice'
    ); // WordPress Version Check
} else {
//        $class = 'ReduxTemplates\Init\Init';
    new ReduxTemplates\Init();
}


