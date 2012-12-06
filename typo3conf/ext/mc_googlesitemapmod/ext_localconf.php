<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_mcgooglesitemapmod_pi1.php","_pi1","menu_type",1);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi2/class.tx_mcgooglesitemapmod_pi2.php","_pi2","menu_type",1);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi3/class.tx_mcgooglesitemapmod_pi3.php","_pi3","menu_type",1);
?>