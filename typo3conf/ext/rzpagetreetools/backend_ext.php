<?php
if (is_object($TYPO3backend)) {
    $pageRenderer = $GLOBALS['TBE_TEMPLATE']->getPageRenderer();

    $path = t3lib_extMgm::extRelPath('rzpagetreetools');
    $pageRenderer->addJsFile($path . 'res/js/rzpagetreetools.js');
}
?>