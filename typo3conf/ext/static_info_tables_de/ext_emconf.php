<?php

########################################################################
# Extension Manager/Repository config file for ext "static_info_tables_de".
#
# Auto generated 31-10-2012 16:16
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Static Info Tables (de)',
	'description' => '(de) language pack for the Static Info Tables providing localized names for countries, currencies and so on.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '2.0.3',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'static_countries,static_languages,static_currencies,static_territories',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'RenÃ© Fritz',
	'author_email' => 'r.fritz@colorcube.de',
	'author_company' => 'Colorcube - digital media lab, www.colorcube.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'static_info_tables' => '2.0.1-',
			'php' => '4.1.0-0.0.0',
			'typo3' => '4.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:9:"ChangeLog";s:4:"5bfe";s:20:"class.ext_update.php";s:4:"0db3";s:21:"ext_conf_template.txt";s:4:"7de5";s:12:"ext_icon.gif";s:4:"639f";s:17:"ext_localconf.php";s:4:"096d";s:14:"ext_tables.php";s:4:"ef3a";s:14:"ext_tables.sql";s:4:"152a";s:28:"ext_tables_static_update.sql";s:4:"b6cb";s:16:"locallang_db.xml";s:4:"c00c";}',
);

?>