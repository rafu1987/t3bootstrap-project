<?php

########################################################################
# Extension Manager/Repository config file for ext "mc_googlesitemapmod".
#
# Auto generated 05-11-2012 18:44
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Google Sitemap for Pages and Contents',
	'description' => 'XML Generator for Google\'s sitemaps , can be used for pages or contents. All sitemaps options available. Modification for TYPO3 4.5.x',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.0.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tt_content,pages',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Raphael Zschorsch',
	'author_email' => 'raphael@medialis.net',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:14:{s:36:"class.tx_mcgooglesitemapmod_base.php";s:4:"2295";s:74:"class.tx_mcgooglesitemapmod_tt_content_tx_mcgooglesitemapmod_objective.php";s:4:"3892";s:12:"ext_icon.gif";s:4:"f533";s:17:"ext_localconf.php";s:4:"e624";s:14:"ext_tables.php";s:4:"fac4";s:14:"ext_tables.sql";s:4:"aef3";s:28:"ext_typoscript_constants.txt";s:4:"a341";s:24:"ext_typoscript_setup.txt";s:4:"5cae";s:16:"locallang_db.php";s:4:"526c";s:8:"test.php";s:4:"2f13";s:14:"doc/manual.sxw";s:4:"d824";s:39:"pi1/class.tx_mcgooglesitemapmod_pi1.php";s:4:"7b4d";s:39:"pi2/class.tx_mcgooglesitemapmod_pi2.php";s:4:"c343";s:39:"pi3/class.tx_mcgooglesitemapmod_pi3.php";s:4:"7e83";}',
	'suggests' => array(
	),
);

?>