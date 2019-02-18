/* global redux */

(function( $ ) {
	'use strict';

	$.redux = $.redux || {};

	$( document ).ready(
		function() {
			var el;
			var instance;
			var thread;
			var rAds;

			if ( undefined !== redux.optName ) {
				if ( redux.optName.rAds ) {
					instance = redux.optName.core_instance;
					thread   = redux.optName.core_thread;

					if ( $( '#redux-header' ).length > 0 ) {
						$( '#redux-header' ).append( '<div id="' + instance + '"></div>' );
						el = $( '#redux-header' );
					} else {
						$( '#customize-theme-controls ul' ).first().prepend( '<li id="' + thread + '" class="accordion-section" style="position: relative;"><div id="' + instance + '"></div></li>' );
						el = $( '#' + thread );
					}

					el.css( 'position', 'relative' );

					el.find( '#' + instance ).attr( 'style', 'position:absolute; top: 0px; right: 0px; display:block !important;' ).css( 'left', '-99999px' );

					el.find( '#' + instance ).html( redux.optName.rAds.replace( /<br\s?\/?>/, '' ) );

					rAds = el.find( '#' + instance );

					$( rAds ).hide();
					rAds.bind(
						'DOMSubtreeModified',
						function() {
							if ( $( this ).html().indexOf( '<a href' ) >= 0 ) {
								rAds.find( 'img' ).css( 'visibility', 'hidden' );
								setTimeout(
									function() {
										rAds.show();
										$.redux.resizeAds();
									},
									400
								);

								rAds.find( 'img' ).css( 'visibility', 'inherit' );
								rAds.unbind( 'DOMSubtreeModified' );
							}
						}
					);

					$( window ).resize(
						function() {
							$.redux.resizeAds();
						}
					);
				}
			}
		}
	);

	$.redux.scaleToRatio = function( el, maxHeight, maxWidth ) {
		var test;

		var ratio   = 0;  // Used for aspect ratio.
		var width   = el.attr( 'data-width' );
		var height  = el.attr( 'data-height' );
		var eHeight = el.height();

		if ( ! width ) {
			width = el.width();
			el.attr( 'data-width', width );
		}

		if ( ! height || eHeight > height ) {
			height = eHeight;
			el.attr( 'data-height', height );
			el.css( 'width', 'auto' );
			el.attr( 'data-width', el.width() );
			width = el.width();
		}

		test = ( $( '#redux-header' ).outerHeight( true ) - el.height() - 5 ) / 2;

		if ( 'both' === $( '#' + redux.optName.core_instance ).css( 'clear' ) ) {
			test = 0;
		}

		el.css( 'margin-top', test );

		if ( test > 5 ) {
			el.css( 'margin-right', test + 'px' );
		} else if ( maxWidth < 520 ) {
			el.css( 'margin-right', '0px' );
		} else {
			el.css( 'margin-right', ( test + 3 ) + 'px' );
		}

		maxWidth -= test;

		// Check if the current width is larger than the max.
		if ( width > maxWidth ) {
			ratio = maxWidth / width;            // Get ratio for scaling image.
			el.css( 'width', maxWidth );         // Set new width.
			el.css( 'height', height * ratio );  // Scale height based on ratio.
			height = height * ratio;             // Reset height to match scaled image.
			width  = width * ratio;              // Reset width to match scaled image.
		} else {
			el.css( 'width', 'auto' );           // Set new height.
		}

		if ( maxWidth < 500 ) {
			maxHeight = height;
		}

		// Check if current height is larger than max.
		if ( height > maxHeight ) {
			ratio = maxHeight / height;         // Get ratio for scaling image.
			el.css( 'height', maxHeight );      // Set new height.
			el.css( 'width', width * ratio );   // Scale width based on ratio.
			width  = width * ratio;             // Reset width to match scaled image.
			height = height * ratio;            // Reset height to match scaled image.
		} else {
			el.css( 'height', 'auto' );         // Set new height.
		}
		if ( $( '#redux-header .redux_field_search' ) ) {
			$( '#redux-header .redux_field_search' ).css( 'right', ( $( el ).width() + 20 ) );
		}
	};

	$.redux.resizeAds = function() {
		var maxWidth;
		var maxHeight;
		var el_margin;
		var rAds;
		var dhw;
		var dev_mode_header;

		var el = $( '#redux-header' );

		if ( 0 === el.length ) {
			el = $( '.customize-pane-parent' );
		}

		maxHeight = el.innerHeight();
		el_margin = el.outerWidth( true ) - el.innerWidth();
		rAds      = el.find( '#' + redux.optName.core_instance );

		if ( el.length ) {
			maxWidth = el.outerWidth( true ) - 30;
			dhw      = el.find( '.display_header' ).outerWidth( true );

			if ( el.find( '.redux-dev-mode-notice-container' ).length && maxWidth < 600 ) {
				if ( maxWidth > 500 ) {
					dev_mode_header = el.find( '.redux-dev-mode-notice-container' ).outerWidth( true ) + el_margin;

					if ( dev_mode_header > dhw ) {
						dhw = dev_mode_header;
					}
				}
			}

			if ( maxWidth < 520 ) {
				maxWidth  = el.width();
				maxHeight = 500;
				dhw       = 4;
				rAds.css( 'clear', 'both' ).css( 'position', 'static' ).css( 'text-align', 'center' );
			} else {
				rAds.css( 'clear', 'none' ).css( 'position', 'absolute' ).css( 'text-align', 'left' );
			}

			maxWidth = maxWidth - dhw;
		} else {
			el       = $( '#customize-info' );
			maxWidth = el.width();
		}

		$( rAds ).find( 'video' ).each(
			function() {
				$.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
			}
		);

		$( rAds ).find( 'img' ).each(
			function() {
				$.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
			}
		);

		$( rAds ).find( 'div' ).each(
			function() {
				$.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
			}
		);

		if ( '-99999px' === rAds.css( 'left' ) ) {
			rAds.css( 'display', 'none' ).css( 'left', 'auto' ).css( 'top', '0px' );
		}

		rAds.fadeIn( 'slow' );
	};
})( jQuery );
