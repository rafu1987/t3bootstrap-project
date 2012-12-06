<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "fl_realurl_image".
 *
 * Auto generated 10-11-2012 03:06
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Image RealURL',
	'description' => 'Add the RealURL functionality to image files. "typo3temp/2d972d5c89b5.jpg" goes "nice-name.jpg"!',
	'category' => 'fe',
	'shy' => 0,
	'version' => '2.1.11',
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
	'author' => 'Tim Lochmueller, Sareen Millet, Dr. Ronald P. Steiner',
	'author_email' => 'webmaster@fruit-lab.de, Ronald.Steiner [at] googlemail.com',
	'author_company' => 'typo3.fruit-lab.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.3.0-0.0.0',
			'typo3' => '4.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:15:{s:21:"ext_conf_template.txt";s:4:"a412";s:12:"ext_icon.gif";s:4:"c96d";s:17:"ext_localconf.php";s:4:"87ff";s:14:"ext_tables.php";s:4:"d11d";s:14:"ext_tables.sql";s:4:"15cf";s:31:"Classes/class.hook_tslib_fe.php";s:4:"ab4e";s:35:"Classes/class.tx_flrealurlimage.php";s:4:"561e";s:31:"Classes/class.ux_tslib_cObj.php";s:4:"f712";s:29:"Classes/Service/CleanTask.php";s:4:"4124";s:32:"Classes/XClass/ImageResource.php";s:4:"1454";s:27:"Configuration/TCA/Cache.php";s:4:"92fb";s:38:"Configuration/TypoScript/constants.txt";s:4:"7c58";s:34:"Configuration/TypoScript/setup.txt";s:4:"0aa7";s:40:"Resources/Private/Language/locallang.xml";s:4:"530e";s:14:"doc/manual.sxw";s:4:"a9fa";}',
);

?>