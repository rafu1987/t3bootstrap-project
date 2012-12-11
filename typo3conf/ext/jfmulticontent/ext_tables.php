<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}



// get extension configuration
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);



$tempColumns = array(
	'tx_jfmulticontent_view' => array(
		'exclude' => 1,
		'onChange' => 'reload',
		'label' => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.view',
		'config' => array (
			'type' => 'select',
			'size' => 1,
			'maxitems' => 1,
			'default' => 'content',
			'items' => array(
				array('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.view.I.0', 'content'),
				array('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.view.I.1', 'page'),
				array('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.view.I.2', 'irre'),
			),
			'itemsProcFunc' => 'tx_jfmulticontent_itemsProcFunc->getViews',
		)
	),
	'tx_jfmulticontent_pages' => array(
		'exclude' => 1,
		'displayCond' => 'FIELD:tx_jfmulticontent_view:IN:page',
		'label' => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.pages',
		'config' => array (
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'pages',
			'size' => 12,
			'minitems' => 0,
			'maxitems' => 1000,
			'wizards' => array(
				'suggest' => array(
					'type' => 'suggest',
				),
			),
		)
	),
	'tx_jfmulticontent_irre' => Array (
		'exclude' => 1,
		'displayCond' => 'FIELD:tx_jfmulticontent_view:IN:irre',
		'label' => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.irre',
		'config' => array (
			'type' => 'inline',
			'foreign_table' => 'tt_content',
			'foreign_field' => 'tx_jfmulticontent_irre_parentid',
			'foreign_sortby' => 'sorting',
			'foreign_label' => 'header',
			'maxitems' => 1000,
			'appearance' => array(
				'showSynchronizationLink' => FALSE,
				'showAllLocalizationLink' => FALSE,
				'showPossibleLocalizationRecords' => FALSE,
				'showRemovedLocalizationRecords' => FALSE,
				'expandSingle' => TRUE,
				'newRecordLinkAddTitle' => TRUE,
				'useSortable' => TRUE,
			),
			'behaviour' => array(
				'localizeChildrenAtParentLocalization' => 1,
				'localizationMode' => 'select',
			),
		)
	),
);



if ($confArr["useStoragePidOnly"]) {
	$tempColumns['tx_jfmulticontent_contents'] = array(
		'exclude' => 1,
		'displayCond' => 'FIELD:tx_jfmulticontent_view:IN:,content',
		'label' => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.contents',
		'config' => array (
			'type' => 'select',
			'foreign_table' => 'tt_content',
			'foreign_table_where' => 'AND tt_content.pid=###STORAGE_PID### AND tt_content.hidden=0 AND tt_content.deleted=0 AND tt_content.sys_language_uid IN (0,-1) ORDER BY tt_content.uid',
			'size' => 12,
			'minitems' => 0,
			'maxitems' => 1000,
			'wizards' => array(
				'_PADDING'  => 2,
				'_VERTICAL' => 1,
				'add' => array(
					'type'   => 'script',
					'title'  => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.contents_add',
					'icon'   => 'add.gif',
					'script' => 'wizard_add.php',
					'params' => array(
						'table'    => 'tt_content',
						'pid'      => '###STORAGE_PID###',
						'setValue' => 'prepend'
					),
				),
				'list' => array(
					'type'   => 'script',
					'title'  => 'List',
					'icon'   => 'list.gif',
					'script' => 'wizard_list.php',
					'params' => array(
						'table' => 'tt_content',
						'pid'   => '###STORAGE_PID###',
					),
				),
				'edit' => array(
					'type'   => 'popup',
					'title'  => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.contents_edit',
					'icon'   => 'edit2.gif',
					'script' => 'wizard_edit.php',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=600,width=800,status=0,menubar=0,scrollbars=1',
				),
			),
		)
	);
} else {
	$tempColumns['tx_jfmulticontent_contents'] = array(
		'exclude' => 1,
		'displayCond' => 'FIELD:tx_jfmulticontent_view:IN:,content',
		'label' => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.contents',
		'config' => array (
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'tt_content',
			'size' => 12,
			'minitems' => 0,
			'maxitems' => 1000,
			'wizards' => array(
				'_PADDING'  => 2,
				'_VERTICAL' => 1,
				'add' => array(
					'type'   => 'script',
					'title'  => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.contents_add',
					'icon'   => 'add.gif',
					'script' => 'wizard_add.php',
					'params' => array(
						'table'    => 'tt_content',
						'pid'      => '###STORAGE_PID###',
						'setValue' => 'prepend'
					),
				),
				'list' => array(
					'type'   => 'script',
					'title'  => 'List',
					'icon'   => 'list.gif',
					'script' => 'wizard_list.php',
					'params' => array(
						'table' => 'tt_content',
						'pid'   => '###STORAGE_PID###',
					),
				),
				'edit' => array(
					'type'   => 'popup',
					'title'  => 'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.contents_edit',
					'icon'   => 'edit2.gif',
					'script' => 'wizard_edit.php',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=600,width=800,status=0,menubar=0,scrollbars=1',
				),
			),
		)
	);
}


t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'tx_jfmulticontent_view,tx_jfmulticontent_pages,tx_jfmulticontent_contents,tx_jfmulticontent_irre,pi_flexform';
// Add reload field to tt_content
$TCA['tt_content']['ctrl']['requestUpdate'] .= ($TCA['tt_content']['ctrl']['requestUpdate'] ? ',' : ''). 'tx_jfmulticontent_view';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_jfmulticontent_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_jfmulticontent_pi1_wizicon.php';
	if (! isset($TCA['tt_content']['columns']['colPos']['config']['items'][$confArr['colPosOfIrreContent']])) {
		// Add the new colPos to the array, only if the ID does not exist...
		$TCA['tt_content']['columns']['colPos']['config']['items'][$confArr['colPosOfIrreContent']] = array ($_EXTKEY, $confArr['colPosOfIrreContent']);
	}
}


t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'Multi content');

require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_jfmulticontent_itemsProcFunc.php');

?>