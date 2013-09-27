<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "realurl".
 *
 * Auto generated 27-09-2013 17:42
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'RealURL: speaking paths for TYPO3',
	'description' => 'Creates nice looking URLs for TYPO3 pages: converts http://example.com/index.phpid=12345&L=2 to http://example.com/path/to/your/page/. Please, ask for free support in TYPO3 mailing lists or contact the maintainer for paid support.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.12.7',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages,sys_domain,pages_language_overlay,sys_template',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Dmitry Dulepov',
	'author_email' => 'dmitry.dulepov@gmail.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.2-5.999.999',
			'typo3' => '4.5.0-6.1.999',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:10:"_.htaccess";s:4:"9ab3";s:9:"ChangeLog";s:4:"03c9";s:20:"class.ext_update.php";s:4:"9794";s:20:"class.tx_realurl.php";s:4:"643e";s:29:"class.tx_realurl_advanced.php";s:4:"f387";s:32:"class.tx_realurl_autoconfgen.php";s:4:"f227";s:28:"class.tx_realurl_tcemain.php";s:4:"d27c";s:16:"ext_autoload.php";s:4:"a6ff";s:21:"ext_conf_template.txt";s:4:"c890";s:12:"ext_icon.gif";s:4:"ea80";s:17:"ext_localconf.php";s:4:"e297";s:14:"ext_tables.php";s:4:"6f96";s:14:"ext_tables.sql";s:4:"150c";s:17:"locallang_csh.xml";s:4:"369d";s:16:"locallang_db.xml";s:4:"584a";s:14:"doc/manual.sxw";s:4:"3b3d";s:38:"modfunc1/class.tx_realurl_modfunc1.php";s:4:"6a87";s:41:"modfunc1/class.tx_realurl_pagebrowser.php";s:4:"60b1";s:22:"modfunc1/locallang.xml";s:4:"7e4f";}',
);

?>