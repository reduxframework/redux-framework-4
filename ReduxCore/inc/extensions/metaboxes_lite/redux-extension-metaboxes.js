/**
 * Redux Metaboxes
 * Dependencies      : jquery
 * Created by        : Dovy Paukstys
 * Date              : 19 Feb. 2014
 */

jQuery(
	function( $ ) {
		'use strict';

		var isGutenburg = false;

		$.reduxMetaBoxes = $.reduxMetaBoxes || {};

		$( document ).ready(
			function() {
				$.reduxMetaBoxes.init();

				if ( $( 'body' ).hasClass( 'block-editor-page' ) ) {
					isGutenburg = true;
				}
			}
		);

		$.reduxMetaBoxes.init = function() {
			$.redux.initFields();
		};

		setTimeout( function() {
			if ( true === isGutenburg ) {
				$( '.postbox .toggle-indicator' ).removeClass( 'toggle-indicator' ).addClass( 'el' );
			}

			$( '#publishing-action .button, #save-action .button, .editor-post-publish-button' ).click(
				function() {
					$( '.redux-save-warn' ).slideUp();

					window.onbeforeunload = null;
				}
			);
		}, 1000 );
	}
);
