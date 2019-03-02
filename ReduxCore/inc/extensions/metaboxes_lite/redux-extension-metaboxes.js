/**
 * Redux Metaboxes
 * Dependencies      : jquery
 * Created by        : Dovy Paukstys
 * Date              : 19 Feb. 2014
 */

jQuery(
	function( $ ) {
		'use strict';

		$.reduxMetaBoxes = $.reduxMetaBoxes || {};

		$( document ).ready(
			function() {
				$.reduxMetaBoxes.init();
			}
		);

		$.reduxMetaBoxes.init = function() {
			$.redux.initFields();
		};

		setTimeout( function() {
			$( '#publishing-action .button, #save-action .button, .editor-post-publish-button' ).click(
				function() {
					$( '.redux-save-warn' ).slideUp();

					window.onbeforeunload = null;
				}
			);
		}, 1000 );
	}
);
