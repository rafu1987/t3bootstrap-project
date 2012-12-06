<?php

if (!defined('TYPO3_MODE'))
	die('Access denied.');

$TCA['tx_flrealurlimage_cache'] = Array(
	'ctrl' => Array(
		'title' => 'LLL:EXT:fl_realurl_image/Resources/Private/Language/locallang.xml:fl_realurl_image.tabletitle',
		'label' => 'realurl_path',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'rootLevel' => 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Cache.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif',
	),
	'feInterface' => Array(
		'fe_admin_fieldList' => '',
	)
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript/', 'fl_realurlimage');
?>