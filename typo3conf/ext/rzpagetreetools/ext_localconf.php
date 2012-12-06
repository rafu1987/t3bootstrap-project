<?php

$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath('rzpagetreetools', 'backend_ext.php');

$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultUserTSconfig'] .= '
    options.contextMenu {
        table.pages.items {
            410 = ITEM
            410 {
                name = hideInMenu
                label = LLL:EXT:rzpagetreetools/locallang.xml:hideInMenu
                icon = 
                spriteIcon = apps-pagetree-page-not-in-menu
                displayCondition = getRecord|nav_hide = 0 && canBeDisabledAndEnabled != 0
                callbackAction = hideInMenu
            }    
            420 = ITEM
            420 {
                name = showInMenu
                label = LLL:EXT:rzpagetreetools/locallang.xml:showInMenu
                icon = 
                spriteIcon = apps-pagetree-page-default
                displayCondition = getRecord|nav_hide = 1 && canBeDisabledAndEnabled != 0
                callbackAction = showInMenu
            }   
            950 = SUBMENU
            950 {
                label = LLL:EXT:rzpagetreetools/locallang.xml:subMenu
                
                100 = ITEM
                100 {
                    name = standardPage
                    label = LLL:EXT:rzpagetreetools/locallang.xml:standardPage
                    icon = 
                    spriteIcon = apps-pagetree-page-default
                    displayCondition = 
                    callbackAction = standardPage       
                }
                200 = ITEM
                200 {
                    name = backendPage
                    label = LLL:EXT:rzpagetreetools/locallang.xml:backendPage
                    icon = 
                    spriteIcon = apps-pagetree-page-backend-users
                    displayCondition = 
                    callbackAction = backendPage       
                }
                300 = ITEM
                300 {
                    name = shortcutPage
                    label = LLL:EXT:rzpagetreetools/locallang.xml:shortcutPage
                    icon = 
                    spriteIcon = apps-pagetree-page-shortcut
                    displayCondition = 
                    callbackAction = shortcutPage       
                }
                400 = ITEM
                400 {
                    name = mountPage
                    label = LLL:EXT:rzpagetreetools/locallang.xml:mountPage
                    icon = 
                    spriteIcon = apps-pagetree-page-mountpoint
                    displayCondition = 
                    callbackAction = mountPage       
                }
                500 = ITEM
                500 {
                    name = urlPage
                    label = LLL:EXT:rzpagetreetools/locallang.xml:urlPage
                    icon = 
                    spriteIcon = apps-pagetree-page-shortcut-external
                    displayCondition = 
                    callbackAction = urlPage       
                }                
                600 = ITEM
                600 {
                    name = storageFolder
                    label = LLL:EXT:rzpagetreetools/locallang.xml:storageFolder
                    icon = 
                    spriteIcon = apps-filetree-folder-default
                    displayCondition = 
                    callbackAction = storageFolder       
                }    
                700 = ITEM
                700 {
                    name = trashPage
                    label = LLL:EXT:rzpagetreetools/locallang.xml:trashPage
                    icon = 
                    spriteIcon = apps-pagetree-page-recycler
                    displayCondition = 
                    callbackAction = trashPage       
                }  
                800 = ITEM
                800 {
                    name = menuPage
                    label = LLL:EXT:rzpagetreetools/locallang.xml:menuPage
                    icon = 
                    spriteIcon = apps-pagetree-spacer
                    displayCondition = 
                    callbackAction = menuPage       
                }                  
                900 = DIVIDER
            }
        }
    }
';

t3lib_extMgm::registerExtDirectComponent(
    'TYPO3.hideShow.Menue',
    'typo3conf/ext/rzpagetreetools/lib/class.tx_rzpagetreetools_tools.php:tx_rzpagetreetools_tools'
  );

?>