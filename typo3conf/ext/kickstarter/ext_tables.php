<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

if (TYPO3_MODE=="BE")	{
	t3lib_extMgm::insertModuleFunction(
		"tools_em",
		"tx_kickstarter_modfunc1",
		t3lib_extMgm::extPath($_EXTKEY)."modfunc1/class.tx_kickstarter_modfunc1.php",
		"LLL:EXT:kickstarter/locallang_db.xml:moduleFunction.tx_kickstarter_modfunc1"
	);
	t3lib_extMgm::insertModuleFunction(
		"tools_em",
		"tx_kickstarter_modfunc2",
		t3lib_extMgm::extPath($_EXTKEY)."modfunc1/class.tx_kickstarter_modfunc1.php",
		"LLL:EXT:kickstarter/locallang_db.xml:moduleFunction.tx_kickstarter_modfunc2",
		'singleDetails'
	);
}
?>