<?php

########################################################################
# Extension Manager/Repository config file for ext "scriptmerger".
#
# Auto generated 05-11-2012 13:57
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'CSS/Javascript Minificator, Compressor And Concatenator',
	'description' => 'This extension minimizes the http requests by concatenating your css and javascript. Furthermore the result can be minified and compressed. This whole process is highly configurable and is partly based on the "minify", "jsminplus" and "jsmin" projects.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '3.2.4',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => 'bottom',
	'loadOrder' => 'tstidy',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'typo3temp/scriptmerger/',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Stefan Galinski',
	'author_email' => 'stefan.galinski@gmail.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.1-5.3.99',
			'typo3' => '4.2.0-4.6.99',
		),
		'conflicts' => array(
			'speedy' => '',
			'queo_speedup' => '',
			'js_css_optimizer' => '',
			'minify' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:16:{s:25:"class.tx_scriptmerger.php";s:4:"4c22";s:31:"class.tx_scriptmerger_cache.php";s:4:"58ce";s:16:"example.htaccess";s:4:"7469";s:12:"ext_icon.gif";s:4:"2cfb";s:17:"ext_localconf.php";s:4:"ac3c";s:14:"ext_tables.php";s:4:"9927";s:27:"configuration/constants.txt";s:4:"038d";s:23:"configuration/setup.txt";s:4:"14da";s:14:"doc/manual.sxw";s:4:"00c6";s:19:"resources/jsmin.php";s:4:"c243";s:23:"resources/jsminplus.php";s:4:"13d4";s:48:"resources/minify/lib/Minify/CommentPreserver.php";s:4:"9762";s:35:"resources/minify/lib/Minify/CSS.php";s:4:"388d";s:47:"resources/minify/lib/Minify/ImportProcessor.php";s:4:"d60f";s:46:"resources/minify/lib/Minify/CSS/Compressor.php";s:4:"c078";s:47:"resources/minify/lib/Minify/CSS/UriRewriter.php";s:4:"d1db";}',
);

?>