<?php
/**
 * Script dependencies for the editor script (no build step).
 *
 * @package RootBranchCore
 */

return array(
	'dependencies' => array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
	'version'      => (string) filemtime( __DIR__ . '/index.js' ),
);
