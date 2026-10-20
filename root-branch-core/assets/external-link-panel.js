/**
 * "External link" panel in the post editor sidebar. Plain JS (no build step).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var Panel = ( wp.editor && wp.editor.PluginDocumentSettingPanel ) || ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );
	var TextControl = wp.components.TextControl;
	var useSelect = wp.data.useSelect;
	var useEntityProp = wp.coreData.useEntityProp;

	if ( ! Panel ) {
		return;
	}

	function ExternalLinkPanel() {
		var postType = useSelect( function ( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );
		var meta = useEntityProp( 'postType', postType, 'meta' );
		var values = meta[ 0 ] || {};
		var setMeta = meta[ 1 ];

		if ( 'post' !== postType ) {
			return null;
		}

		return el(
			Panel,
			{ name: 'root-branch-external-link', title: __( 'External link', 'root-branch-core' ) },
			el( TextControl, {
				__nextHasNoMarginBottom: true,
				__next40pxDefaultSize: true,
				type: 'url',
				label: __( 'Article URL', 'root-branch-core' ),
				help: __( 'For press articles hosted on another website. Cards and titles will open this link in a new tab. Leave empty to show this post on the site.', 'root-branch-core' ),
				value: values.rb_external_url || '',
				onChange: function ( value ) {
					setMeta( Object.assign( {}, values, { rb_external_url: value } ) );
				},
			} )
		);
	}

	wp.plugins.registerPlugin( 'root-branch-external-link', { render: ExternalLinkPanel } );
} )( window.wp );
