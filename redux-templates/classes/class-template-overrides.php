<?php // phpcs:ignore WordPress.Files.FileName
/**
 * CSS overrides for block plugins.
 *
 * @since 4.0.0
 * @package Redux Framework
 */

namespace ReduxTemplates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Redux Templates Templates Class
 *
 * @since 4.0.0
 */
class Template_Overrides {

	/**
	 * ReduxTemplates Template_Overrides.
	 *
	 * @since 4.0.0
	 */
	public function __construct() { }

	/**
	 * Detects the current theme and provides overrides.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public static function get_overrides() {
		$template = strtolower( get_template() );

		$css = "";
		if ( method_exists(__CLASS__, $template ) ) {
			$css = call_user_func( array( __CLASS__, $template ) );
			$css = preg_replace( '/\s+/S', " ", $css );
		}

		$css .= <<<'EOD'
			#main {
				padding: unset !important;
			}
			#content {
				padding: unset !important;
			}
			#wrapper {
				min-height: unset !important;
			}
			.alignfull, .alignwide {
				margin: unset !important;
			}
EOD;

		return preg_replace( '/\s+/S', " ", $css );
	}

	/**
	 * Consulting theme overrides.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public static function consulting() {
		return <<<'EOD'
			#content-core {
				max-width: 100%;
			}
EOD;
	}

	/**
	 * TwentyTwenty theme overrides.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public static function twentytwenty() {
		return <<<'EOD'
			[class*="__inner-container"] > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.is-style-wide) {
				max-width: unset;
			}
			.wp-block-archives:not(.alignwide):not(.alignfull), .wp-block-categories:not(.alignwide):not(.alignfull), .wp-block-code, .wp-block-columns:not(.alignwide):not(.alignfull), .wp-block-cover:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.aligncenter), .wp-block-embed:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.aligncenter), .wp-block-gallery:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.aligncenter), .wp-block-group:not(.has-background):not(.alignwide):not(.alignfull), .wp-block-image:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.aligncenter), .wp-block-latest-comments:not(.aligncenter):not(.alignleft):not(.alignright), .wp-block-latest-posts:not(.aligncenter):not(.alignleft):not(.alignright), .wp-block-media-text:not(.alignwide):not(.alignfull), .wp-block-preformatted, .wp-block-pullquote:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright), .wp-block-quote, .wp-block-quote.is-large, .wp-block-quote.is-style-large, .wp-block-verse, .wp-block-video:not(.alignwide):not(.alignfull) {
				margin-top: unset;
				margin-bottom: unset;
			}
			
EOD;
	}

}
