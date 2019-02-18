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

		$( '#publishing-action .button, #save-action .button' ).click(
			function() {
				window.onbeforeunload = null;
			}
		);
	}
);
