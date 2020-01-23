/**
 * Field Border (border)
 */

/*global redux_change, redux, colorValidate */

(function( $ ) {
	'use strict';

	redux.field_objects        = redux.field_objects || {};
	redux.field_objects.border = redux.field_objects.border || {};

	redux.field_objects.border.init = function( selector ) {
		if ( ! selector ) {
			selector = $( document ).find( '.redux-group-tab:visible' ).find( '.redux-container-border:visible' );
		}

		$( selector ).each(
			function() {
				var el     = $( this );
				var parent = el;

				if ( ! el.hasClass( 'redux-field-container' ) ) {
					parent = el.parents( '.redux-field-container:first' );
				}

				if ( parent.is( ':hidden' ) ) {
					return;
				}

				if ( parent.hasClass( 'redux-field-init' ) ) {
					parent.removeClass( 'redux-field-init' );
				} else {
					return;
				}

				el.find( '.redux-border-top, .redux-border-right, .redux-border-bottom, .redux-border-left, .redux-border-all' ).numeric( { allowMinus: false } );
				el.find( '.redux-border-style' ).select2();

				el.find( '.redux-border-input' ).on(
					'change',
					function() {
						var value;

						var units = $( this ).parents( '.redux-field:first' ).find( '.field-units' ).val();

						if ( 0 !== $( this ).parents( '.redux-field:first' ).find( '.redux-border-units' ).length ) {
							units = $( this ).parents( '.redux-field:first' ).find( '.redux-border-units option:selected' ).val();
						}

						value = $( this ).val();

						if ( 'undefined' !== typeof units && value ) {
							value += units;
						}

						if ( $( this ).hasClass( 'redux-border-all' ) ) {
							$( this ).parents( '.redux-field:first' ).find( '.redux-border-value' ).each(
								function() {
									$( this ).val( value );
								}
							);
						} else {
							$( '#' + $( this ).attr( 'rel' ) ).val( value );
						}
					}
				);

				el.find( '.redux-border-units' ).on(
					'change',
					function() {
						$( this ).parents( '.redux-field:first' ).find( '.redux-border-input' ).change();
					}
				);

				el.find( '.redux-color-init' ).wpColorPicker(
					{
						change: function( e, ui ) {
							$( this ).val( ui.color.toString() );
							redux_change( $( this ) );
							el.find( '#' + e.target.getAttribute( 'data-id' ) + '-transparency' ).removeAttr( 'checked' );
						},
						clear: function( e, ui ) {
							e = null;
							$( this ).val( ui.color.toString() );
							redux_change( $( this ).parent().find( '.redux-color-init' ) );
						}
					}
				);

				el.find( '.redux-color' ).on(
					'keyup',
					function() {
						var color = colorValidate( this );

						if ( color && color !== $( this ).val() ) {
							$( this ).val( color );
						}
					}
				);

				// Replace and validate field on blur.
				el.find( '.redux-color' ).on(
					'blur',
					function() {
						var value = $( this ).val();

						if ( colorValidate( this ) === value ) {
							if ( 0 !== value.indexOf( '#' ) ) {
								$( this ).val( $( this ).data( 'oldcolor' ) );
							}
						}
					}
				);

				// Store the old valid color on keydown.
				el.find( '.redux-color' ).on(
					'keydown',
					function() {
						$( this ).data( 'oldkeypress', $( this ).val() );
					}
				);
			}
		);
	};

	redux.field_objects.border.customizer_preview_output = function( $selector_array, $style ) {
		// Expected Input
            /* - selector_array => [".site-header"], 
                $style => {
                    border-color: "#1e73be"
                    border-style: "solid"
                    border-top: "3px"
                    border-right: "3px"
                    border-bottom: "3px"
                    border-left: "3px"}
                */
		// Desired Output: String
		// .site-header { border-top: "3px solid #1e73be", border-bottom: "3px solid #1e73be", border-left: "3px solid #1e73be", border-right: "3px solid #1e73be"}
		var selectors = $selector_array.join(", ");
	
		let borderColor = $style['border-color'] ? $style['border-color'] : '';
		let borderStyle = $style['border-style'] ? $style['border-style'] : '';

		let filteredStyle = Object.keys($style).filter(function (elem) {
			return (elem !== 'border-color' && elem!== 'border-style');
		});
		let newStyle = "{";
		newStyle += filteredStyle.reduce(function(output, elem) {
			output += `${elem}: ${$style[elem]} ${borderStyle} ${borderColor}, `;
			return output;
		}, "");
		newStyle = newStyle.length > 1 ? newStyle.slice(0, -2) : newStyle;
		newStyle += "}";

		return selectors + newStyle;

	};
})( jQuery );
