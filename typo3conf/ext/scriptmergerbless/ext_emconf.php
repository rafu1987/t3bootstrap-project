<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "scriptmergerbless".
 *
 * Auto generated 19-11-2012 11:55
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Scriptmerger Bless',
	'description' => 'XCLASS of the scriptmerger Extension, to do something similar as the Bless Plugin (http://blesscss.com/). IE versions 6, 7, 8 & 9 all have a limit on the number of selectors allowed in a single CSS file. Once the limit is reached, IE silently fails and just ignores any further CSS in the file leaving parts of your site totally unstyled.

When a lot of CSS files are minified and merged by the scriptmerger extension, the limit is quickly reached. This extensions solves this problem by splitting the CSS files with help of a threshold TypoScript value. You also have the possibility to de/activate the splitting via TypoScript; the extension then does practically nothing.',
	'category' => 'fe',
	'author' => 'Tobias Schenk, Raphael Zschorsch',
	'author_email' => 'web@medialis.net',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'medialis.net UG (haftungsbeschränkt)',
	'version' => '0.0.2-dev',
	'constraints' => array(
		'depends' => array(
      'scriptmerger' => ''
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:8:{s:9:"ChangeLog";s:4:"d7d9";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"1f77";s:10:"README.txt";s:4:"ee2d";s:19:"doc/wizard_form.dat";s:4:"eded";s:20:"doc/wizard_form.html";s:4:"e061";s:39:"static/scriptmerger_bless/constants.txt";s:4:"d41d";s:35:"static/scriptmerger_bless/setup.txt";s:4:"d41d";}',
);

?>