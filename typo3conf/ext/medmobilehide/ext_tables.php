<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tempColumns = array(
    'tx_medmobilehide_mobile_hidden' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden',
        'config' => array(
            'type' => 'select',
            'items' => array(
                array('LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden.I.0', '0'),
                array('LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden.I.1', '1'),
                array('LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden.I.2', '2'),
                array('LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden.I.3', '3'),
                array('LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden.I.4', '4'),
                array('LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden.I.5', '5'),
                array('LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden.I.6', '6'),
                array('LLL:EXT:medmobilehide/locallang_db.xml:tt_content.tx_medmobilehide_mobile_hidden.I.7', '7'),
            ),
            'size' => 1,
            'maxitems' => 1,
        )
    ),
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('tt_content', 'tx_medmobilehide_mobile_hidden', '', 'after:spaceAfter');


t3lib_extMgm::addStaticFile($_EXTKEY, 'static/', 'Hide in mobile version');
?>