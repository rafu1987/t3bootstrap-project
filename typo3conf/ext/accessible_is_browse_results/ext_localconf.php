<?php

if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/indexed_search/pi/class.tx_indexedsearch.php'] = PATH_typo3conf.'ext/accessible_is_browse_results/pi/class.ux_tx_indexedsearch.php';
$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/indexed_search/class.indexer.php'] = PATH_typo3conf.'ext/accessible_is_browse_results/ux_tx_indexedsearch_indexer.php';

?>
