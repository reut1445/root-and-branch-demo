/**
 * Sign-up form — editor. Plain JS (no build step); uses WordPress globals.
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var ToggleControl = wp.components.ToggleControl;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'root-branch/signup-form', {
		edit: function ( props ) {
			var a = props.attributes;
			var set = props.setAttributes;

			function text( key, label ) {
				return el( TextControl, {
					__nextHasNoMarginBottom: true,
					__next40pxDefaultSize: true,
					label: label,
					value: a[ key ],
					onChange: function ( value ) {
						var next = {};
						next[ key ] = value;
						set( next );
					},
				} );
			}

			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Form text', 'root-branch-core' ) },
						text( 'nameLabel', __( 'Name field label', 'root-branch-core' ) ),
						text( 'emailLabel', __( 'Email field label', 'root-branch-core' ) ),
						el( ToggleControl, {
							__nextHasNoMarginBottom: true,
							label: __( 'Ask for a phone number', 'root-branch-core' ),
							checked: a.showPhone,
							onChange: function ( value ) {
								set( { showPhone: value } );
							},
						} ),
						a.showPhone ? text( 'phoneLabel', __( 'Phone field label', 'root-branch-core' ) ) : null,
						text( 'buttonText', __( 'Button text', 'root-branch-core' ) ),
						el( TextareaControl, {
							__nextHasNoMarginBottom: true,
							label: __( 'Thank-you message', 'root-branch-core' ),
							value: a.successMessage,
							onChange: function ( value ) {
								set( { successMessage: value } );
							},
						} ),
						text( 'listName', __( 'Saved as (shown in Sign-ups)', 'root-branch-core' ) )
					)
				),
				el(
					Disabled,
					null,
					el( ServerSideRender, { block: 'root-branch/signup-form', attributes: a } )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
