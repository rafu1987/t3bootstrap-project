<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "itypo_404handler".
 *
 * Auto generated 09-09-2013 09:59
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'iTypo 404/403 Handler',
	'description' => 'Proper 404 & 403 handling. URL does not change & correct HTTP status codes. (Even when logged in!) Tested with TYPO3 4.5.x - 4.7.x. Compatible with RealURL.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.0.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Sander Leeuwesteijn | iTypo',
	'author_email' => 'info@itypo.nl',
	'author_company' => 'iTypo',
	'CGLcompliance' => NULL,
	'CGLcompliance_note' => NULL,
	'constraints' => 
	array (
		'depends' => 
		array (
			'typo3' => '4.5.0-4.7.99',
		),
		'conflicts' => 
		array (
			'pagenotfoundhandling' => '',
			'ws_404' => '',
			'hype_error' => '',
		),
		'suggests' => 
		array (
		),
	),
);

?>