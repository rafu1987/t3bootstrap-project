<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

/**
 * Configure the Plugin to call the
 * right combination of Controller and Action according to
 * the user input (default settings, FlexForm, URL etc.)
 */
Tx_Extbase_Utility_Extension::configurePlugin(
	'in2facebook',
	'Pi1',
	array (
		'Opengraph' => 'show'
	),
	array (
		'Opengraph' => ''
	)
);

?>