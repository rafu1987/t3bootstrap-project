<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

/** @noinspection PhpUndefinedVariableInspection */
t3lib_extMgm::addStaticFile($_EXTKEY, 'configuration/', 'Scriptmerger');

?>