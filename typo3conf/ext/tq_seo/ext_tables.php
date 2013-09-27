<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$extPath = t3lib_extMgm::extPath($_EXTKEY);
$extRelPath = t3lib_extMgm::extRelPath($_EXTKEY);

###############################################################################
# TABLES
###############################################################################

###################
# Pages
###################

$tempColumns = array (
	'tx_tqseo_pagetitle' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_pagetitle',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
		)
	),

	'tx_tqseo_pagetitle_rel' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_pagetitle_rel',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
		)
	),

	'tx_tqseo_pagetitle_prefix' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_pagetitle_prefix',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
		)
	),

	'tx_tqseo_pagetitle_suffix' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_pagetitle_suffix',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
		)
	),

	'tx_tqseo_inheritance' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_inheritance',
		'config'  => array(
			'type'          => 'select',
			'items'         => array(
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_inheritance.I.0',
					0
				),
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_inheritance.I.1',
					1
				),
			),
			'size'          => 1,
			'maxitems'      => 1
		)
	),

	'tx_tqseo_is_exclude' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_is_exclude',
		'exclude' => 1,
		'config' => array (
			'type' => 'check'
		)
	),

	'tx_tqseo_canonicalurl' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_canonicalurl',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
			'wizards' => Array(
				'_PADDING' => 2,
				'link' => Array(
					'type' => 'popup',
					'title' => 'Link',
					'icon' => 'link_popup.gif',
					'script' => 'browse_links.php?mode=wizard&act=url',
					'params' => array(
						'blindLinkOptions' => 'mail',
					),
					'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
				),
			),
		)
	),

	'tx_tqseo_priority' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_priority',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'int',
		)
	),

	'tx_tqseo_change_frequency' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency',
		'config'  => array(
			'type'          => 'select',
			'items'         => array(
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency.I.0',
					0
				),
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency.I.1',
					1
				),
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency.I.2',
					2
				),
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency.I.3',
					3
				),
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency.I.4',
					4
				),
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency.I.5',
					5
				),
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency.I.6',
					6
				),
				array(
					'LLL:EXT:tq_seo/locallang_db.php:pages.tx_tqseo_change_frequency.I.7',
					7
				),
			),
			'size'          => 1,
			'maxitems'      => 1
		)
	),
);


t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);

// TCA Palettes
$TCA['pages']['palettes']['tx_tqseo_pagetitle'] = array(
	'showitem'			=> 'tx_tqseo_pagetitle,--linebreak--,tx_tqseo_pagetitle_prefix,tx_tqseo_pagetitle_suffix,--linebreak--,tx_tqseo_inheritance',
	'canNotCollapse'	=> 1
);

$TCA['pages']['palettes']['tx_tqseo_crawler'] = array(
	'showitem'			=> 'tx_tqseo_is_exclude,--linebreak--,tx_tqseo_canonicalurl',
	'canNotCollapse'	=> 1
);

$TCA['pages']['palettes']['tx_tqseo_sitemap'] = array(
	'showitem'			=> 'tx_tqseo_priority,--linebreak--,tx_tqseo_change_frequency',
	'canNotCollapse'	=> 1
);


t3lib_extMgm::addToAllTCAtypes('pages','tx_tqseo_pagetitle_rel', '1,4,7,3', 'after:title');

// Put it for standard page
t3lib_extMgm::addToAllTCAtypes('pages','--div--;LLL:EXT:tq_seo/locallang_tca.xml:pages.tab.seo;,--palette--;LLL:EXT:tq_seo/locallang_tca.xml:pages.palette.pagetitle;tx_tqseo_pagetitle,--palette--;LLL:EXT:tq_seo/locallang_tca.xml:pages.palette.crawler;tx_tqseo_crawler,--palette--;LLL:EXT:tq_seo/locallang_tca.xml:pages.palette.sitemap;tx_tqseo_sitemap', '1,4,7,3', 'after:author_email');

###################
# Page overlay (lang)
###################

$tempColumns = array (
	'tx_tqseo_pagetitle' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages_language_overlay.tx_tqseo_pagetitle',
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
		)
	),
	'tx_tqseo_pagetitle_rel' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages_language_overlay.tx_tqseo_pagetitle_rel',
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
		)
	),
	'tx_tqseo_pagetitle_prefix' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_pagetitle_prefix',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
		)
	),
	'tx_tqseo_pagetitle_suffix' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_pagetitle_suffix',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
		)
	),

	'tx_tqseo_canonicalurl' => array (
		'label' => 'LLL:EXT:tq_seo/locallang_db.xml:pages.tx_tqseo_canonicalurl',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'checkbox' => '',
			'eval' => 'trim',
			'wizards' => Array(
				'_PADDING' => 2,
				'link' => Array(
					'type' => 'popup',
					'title' => 'Link',
					'icon' => 'link_popup.gif',
					'script' => 'browse_links.php?mode=wizard&act=url',
					'params' => array(
						'blindLinkOptions' => 'mail',
					),
					'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
				),
			),
		)
	),
);

t3lib_div::loadTCA('pages_language_overlay');
t3lib_extMgm::addTCAcolumns('pages_language_overlay',$tempColumns,1);

// TCA Palettes
$TCA['pages_language_overlay']['palettes']['tx_tqseo_pagetitle'] = array(
	'showitem'			=> 'tx_tqseo_pagetitle,--linebreak--,tx_tqseo_pagetitle_prefix,tx_tqseo_pagetitle_suffix',
	'canNotCollapse'	=> 1
);

$TCA['pages_language_overlay']['palettes']['tx_tqseo_crawler'] = array(
	'showitem'			=> 'tx_tqseo_canonicalurl',
	'canNotCollapse'	=> 1
);

t3lib_extMgm::addToAllTCAtypes('pages_language_overlay','tx_tqseo_pagetitle_rel', '', 'after:title');

// Put it for standard page overlay
t3lib_extMgm::addToAllTCAtypes('pages_language_overlay','--div--;LLL:EXT:tq_seo/locallang_tca.xml:pages.tab.seo;,--palette--;LLL:EXT:tq_seo/locallang_tca.xml:pages.palette.pagetitle;tx_tqseo_pagetitle,--palette--;LLL:EXT:tq_seo/locallang_tca.xml:pages.palette.crawler;tx_tqseo_crawler', '', 'after:author_email');

###################
# Domains
###################

/*
$tempColumns = array (
);

t3lib_div::loadTCA('sys_domain');
t3lib_extMgm::addTCAcolumns('sys_domain',$tempColumns,1);
*/

###################
# Settings Root
###################
//t3lib_extMgm::addToInsertRecords('tx_tqseo_setting_root');
//t3lib_extMgm::allowTableOnStandardPages('tx_tqseo_setting_root');

$TCA['tx_tqseo_setting_root'] = array(
	'ctrl' => array(
		'title'				=> 'LLL:EXT:tq_seo/locallang_db.xml:tx_tqseo_setting_root',
		'label'				=> 'uid',
		'adminOnly'			=> true,
		'dynamicConfigFile'	=> $extPath.'tca.php',
		'iconfile'			=> 'page',
		'hideTable'			=> true,
		'dividers2tabs'		=> true,
	),
	'feInterface' => array (
	),
	'interface' => array(
		'always_description'	=> true,
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_tqseo_setting_root','EXT:tq_seo/locallang_csh_setting_root.xml');

/*
$TCA['tx_tqseo_setting_page'] = array(
	'ctrl' => array(
		'title'				=> 'LLL:EXT:tq_seo/locallang_db.xml:tx_tqseo_setting_page',
		'label'				=> 'uid',
		'adminOnly'			=> 1,
		'dynamicConfigFile'	=> $extPath.'tca.php',
		'iconfile'			=> 'page',
		'hideTable'			=> true,
	),
	'feInterface' => array (
	),
	'interface' => array(
	),
);
*/

###############################################################################
# BACKEND MODULE
###############################################################################
if (TYPO3_MODE == 'BE') {
	// add module before 'Help'
	if (!isset($TBE_MODULES['tqseo']))	{
		$temp_TBE_MODULES = array();
		foreach($TBE_MODULES as $key => $val) {
			if ($key == 'help') {
				$temp_TBE_MODULES['tqseo'] = '';
				$temp_TBE_MODULES[$key] = $val;
			} else {
				$temp_TBE_MODULES[$key] = $val;
			}
		}

		$TBE_MODULES = $temp_TBE_MODULES;
	}

	t3lib_extMgm::addModule('tqseo', '', '', $extPath.'mod1/');
	t3lib_extMgm::addModule('tqseo', 'txtqseoM2', 'bottom', $extPath . 'mod2/');
	t3lib_extMgm::addModule('tqseo', 'txtqseoM3', 'bottom', $extPath . 'mod3/');


    t3lib_extMgm::addModulePath('web_txtqseoM4', $extPath . 'mod4/');
    t3lib_extMgm::addModule('web', 'txtqseoM4', '', $extPath . 'mod4/');
}

###############################################################################
# CONFIGURATION
###############################################################################

t3lib_extMgm::addStaticFile($_EXTKEY,'static/default/', 'TEQneers SEO');

?>