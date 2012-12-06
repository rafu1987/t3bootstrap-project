<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$TCA['tx_medbootstraptools_buttonstyles'] = array(
    'ctrl' => $TCA['tx_medbootstraptools_buttonstyles']['ctrl'],
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,fe_group,title,description'
    ),
    'feInterface' => $TCA['tx_medbootstraptools_buttonstyles']['feInterface'],
    'columns' => array(
        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '30',
            )
        ),
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
                )
            )
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_medbootstraptools_buttonstyles',
                'foreign_table_where' => 'AND tx_medbootstraptools_buttonstyles.pid=###CURRENT_PID### AND tx_medbootstraptools_buttonstyles.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'starttime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            )
        ),
        'endtime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => array(
                    'upper' => mktime(3, 14, 7, 1, 19, 2038),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
                )
            )
        ),
        'fe_group' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
                ),
                'foreign_table' => 'fe_groups'
            )
        ),
        'title' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:medbootstraptools/locallang_db.xml:tx_medbootstraptools_buttonstyles.title',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            )
        ),
        'description' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:medbootstraptools/locallang_db.xml:tx_medbootstraptools_buttonstyles.description',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            )
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title;;;;2-2-2, description')
    ),
    'palettes' => array(
        '1' => array('showitem' => 'starttime, endtime, fe_group')
    )
);

$TCA['tx_medbootstraptools_colors'] = array(
    'ctrl' => $TCA['tx_medbootstraptools_colors']['ctrl'],
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,fe_group,title,hex'
    ),
    'feInterface' => $TCA['tx_medbootstraptools_buttonstyles']['feInterface'],
    'columns' => array(
        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '30',
            )
        ),
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
                )
            )
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_medbootstraptools_buttonstyles',
                'foreign_table_where' => 'AND tx_medbootstraptools_buttonstyles.pid=###CURRENT_PID### AND tx_medbootstraptools_buttonstyles.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'starttime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            )
        ),
        'endtime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => array(
                    'upper' => mktime(3, 14, 7, 1, 19, 2038),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
                )
            )
        ),
        'fe_group' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
                ),
                'foreign_table' => 'fe_groups'
            )
        ),
        'title' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:medbootstraptools/locallang_db.xml:tx_medbootstraptools_colors.title',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            )
        ),
        'hex' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:medbootstraptools/locallang_db.xml:tx_medbootstraptools_colors.hex',
            'config' => array(
                'type' => 'input',
                'size' => '6',
                'eval' => 'required',
                'wizards' => array(
                    'colorpick' => array(
                        'type' => 'colorbox',
                        'title' => 'LLL:EXT:medbootstraptools/locallang_db.xml:tx_medbootstraptools_colors.colorpicker',
                        'script' => 'wizard_colorpicker.php',
                        'dim' => '15x15',
                        'tableStyle' => '',
                        'JSopenParams' => 'height=550,width=365,status=0,menubar=0,scrollbars=1',
                        'exampleImg' => 'gfx/wizard_colorpickerex.jpg',
                    )
                )
            )
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title;;;;2-2-2, hex')
    ),
    'palettes' => array(
        '1' => array('showitem' => 'starttime, endtime, fe_group')
    )
);
?>