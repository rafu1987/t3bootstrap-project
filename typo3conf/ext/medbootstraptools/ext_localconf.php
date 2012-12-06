<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_medbootstraptools_buttonstyles=1
');

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_medbootstraptools_colors=1
');

// TemplaVoilà Field Hook
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila']['cm1']['eTypesConfGen']['t3bootstrap_bootstrapicons'] = 'typo3conf/ext/medbootstraptools/lib/class.tx_medbootstraptools_eTypesConfGen.php:&tx_medbootstraptools_eTypesConfGen->bootstrapFields';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila']['cm1']['eTypesConfGen']['t3bootstrap_slider'] = 'typo3conf/ext/medbootstraptools/lib/class.tx_medbootstraptools_eTypesConfGen.php:&tx_medbootstraptools_eTypesConfGen->bootstrapFields';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila']['cm1']['eTypesConfGen']['t3bootstrap_offsetSlider'] = 'typo3conf/ext/medbootstraptools/lib/class.tx_medbootstraptools_eTypesConfGen.php:&tx_medbootstraptools_eTypesConfGen->bootstrapFields';

// t3_less Hook
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['medbootstraptools']);
$pathArr = explode(",",$extConf['less_path']);

// Only process hook if at least one path is defined
if($pathArr) {
    foreach($pathArr as $p) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3less']['addForeignLessFiles'][] = $p;
    }
}

require_once(t3lib_extMgm::extPath('medbootstraptools').'lib/class.tx_medbootstraptools_extraFields.php');

// Install Tool additional step
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/install/mod/class.tx_install.php']['stepOutput'][] = 'EXT:'.$_EXTKEY.'/Classes/Controller/Controller.php:&tx_medbootstraptools_controller';
	
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/install/mod/class.tx_install.php']['additionalSteps'][] = 'EXT:'.$_EXTKEY.'/Classes/Controller/AdditionalStepsController.php:&tx_medbootstraptools_additionalstepscontroller';

?>