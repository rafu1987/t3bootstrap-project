<?php

########################################################################
# Extension Manager/Repository config file for ext "ad_rtepasteplain".
#
# Auto generated 05-11-2012 12:16
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'htmlArea RTE - Paste As Plain Text',
	'description' => 'This extension forces htmlArea RTE to automatically paste the 
clipboard content as plain text. Developed and tested with rtehtmlarea 1.8.6 and Typo3 4.3.0',
	'category' => 'be',
	'shy' => 0,
	'version' => '2.1.6',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Alexander Dick',
	'author_email' => 'alex@dick.at',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'rtehtmlarea' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:12:{s:9:"ChangeLog";s:4:"686c";s:40:"class.ux_tx_rtehtmlarea_removeformat.php";s:4:"bea1";s:12:"ext_icon.gif";s:4:"4140";s:17:"ext_localconf.php";s:4:"68c6";s:13:"locallang.xml";s:4:"fcfe";s:10:"README.txt";s:4:"555e";s:14:"doc/manual.pdf";s:4:"24b7";s:14:"doc/manual.sxw";s:4:"6bce";s:19:"doc/wizard_form.dat";s:4:"7178";s:20:"doc/wizard_form.html";s:4:"6a71";s:15:"res/helpers.css";s:4:"1ede";s:17:"res/pasteplain.js";s:4:"dfa0";}',
);

?>