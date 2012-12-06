<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "accessible_is_browse_results".
 *
 * Auto generated 12-11-2012 17:39
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Accessible browse results for indexed search',
	'description' => '- Makes browse results links for indexed search accessible : reformat links without JavaScript and sends indexed search parameters via URL.
										- Adds title attribute to A tags in result pages browser.
										- Modifies page results title according to search sword and result page.
										- Adds configuration option (boolean) for indexing page meta description & meta keywords.
										- A script comes with the extension which modifies "to top" link in order to work properly.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.2.1',
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
	'author' => 'Frederic Petit, Christophe Deleury',
	'author_email' => 'contact@oblady.com',
	'author_company' => 'Oblady',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:9:"ChangeLog";s:4:"ea1a";s:12:"ext_icon.gif";s:4:"7b76";s:17:"ext_localconf.php";s:4:"7299";s:28:"ext_typoscript_constants.txt";s:4:"0120";s:24:"ext_typoscript_setup.txt";s:4:"eb47";s:10:"README.txt";s:4:"ee2d";s:21:"user_is_functions.php";s:4:"8d5c";s:31:"ux_tx_indexedsearch_indexer.php";s:4:"d1a0";s:14:"doc/manual.sxw";s:4:"7f28";s:32:"pi/class.ux_tx_indexedsearch.php";s:4:"b58a";}',
);

?>