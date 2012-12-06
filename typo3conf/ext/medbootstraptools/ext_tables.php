<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_medbootstraptools_buttonstyles');
t3lib_extMgm::allowTableOnStandardPages('tx_medbootstraptools_colors');

$TCA['tx_medbootstraptools_buttonstyles'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:medbootstraptools/locallang_db.xml:tx_medbootstraptools_buttonstyles',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => TRUE,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
        'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_medbootstraptools_buttonstyles.gif',
    ),
);

$TCA['tx_medbootstraptools_colors'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:medbootstraptools/locallang_db.xml:tx_medbootstraptools_colors',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => TRUE,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
        'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_medbootstraptools_colors.gif',
    ),
);

if (TYPO3_MODE === 'BE') {
    t3lib_extMgm::addUserTSConfig('
		templavoila.eTypes {
		    eType {
			t3bootstrap_bootstrapicons {
			    label = Bootstrap Icons
			}
			t3bootstrap_slider {
			    label = Bootstrap Column Slider
			} 
			t3bootstrap_offsetSlider {
			    label = Bootstrap Offset Slider
			}                         
		    }
		    defaultTypes_misc := addToList(t3bootstrap_bootstrapicons,t3bootstrap_slider,t3bootstrap_offsetSlider)
		}
	');
}

if (TYPO3_MODE === 'BE') {
    t3lib_extMgm::addModulePath('tools_txmedbootstraptoolsM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
        
    t3lib_extMgm::addModule('tools', 'txmedbootstraptoolsM1', 'top', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

?>