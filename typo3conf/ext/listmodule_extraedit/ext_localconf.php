<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
	$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/class.db_list_extra.inc']['actions'][] = 'EXT:listmodule_extraedit/class.tx_listmoduleextraedit_hooks.php:&tx_listmoduleextraedit_hooks';

?>
