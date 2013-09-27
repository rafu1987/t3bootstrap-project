<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "tq_seo".
 *
 * Auto generated 09-09-2013 10:10
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'TEQneers SEO Enhancements',
	'description' => 'Search Engine Optimization (SEO), Indexed Google-Sitemap (TXT- and XML-Sitemap) for all Extensions (pibase, extbase), Metatags, Canonical-URL, Pagetitle manipulations, Crawler verification, Piwik and Google Analytics support and some more... multi-language- and multi-tree-support',
	'category' => 'misc',
	'shy' => 0,
	'version' => '5.0.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'pages,pages_language_overlay',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Markus Blaschke',
	'author_email' => 'blaschke@teqneers.de',
	'author_company' => 'TEQneers GmbH & Co. KG',
	'CGLcompliance' => NULL,
	'CGLcompliance_note' => NULL,
	'constraints' => 
	array (
		'depends' => 
		array (
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.5.0-0.0.0',
			'fluid' => '1.0.0-0.0.0',
		),
		'conflicts' => 
		array (
			'tq_seo_sitemap' => '',
		),
		'suggests' => 
		array (
		),
	),
);

?>