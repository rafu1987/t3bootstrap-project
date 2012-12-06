<?php
/*
 * Register necessary class names with autoloader
 */
$extensionPath = t3lib_extMgm::extPath('l10nmgr');
return array(
	'tx_l10nmgr_filegarbagecollection' => $extensionPath . 'tasks/class.tx_l10nmgr_filegarbagecollection.php',
	'tx_l10nmgr_filegarbagecollection_additionalfieldprovider' => $extensionPath . 'tasks/class.tx_l10nmgr_filegarbagecollection_additionalfieldprovider.php',
);
?>
