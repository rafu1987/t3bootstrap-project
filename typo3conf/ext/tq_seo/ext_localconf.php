<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tq_seo']);

#################################################
## BACKEND
#################################################
if (TYPO3_MODE == 'BE') {
	// AJAX
	$TYPO3_CONF_VARS['BE']['AJAX']['tx_tqseo_backend_ajax::sitemap']	= 'EXT:tq_seo/lib/backend/ajax/class.sitemap.php:tx_tqseo_backend_ajax_sitemap->main';
	$TYPO3_CONF_VARS['BE']['AJAX']['tx_tqseo_backend_ajax::page']		= 'EXT:tq_seo/lib/backend/ajax/class.page.php:tx_tqseo_backend_ajax_page->main';

	// Field validations
	$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_tqseo_backend_validation_float'] = 'EXT:tq_seo/lib/backend/validation/class.float.php';
}

#################################################
## SEO
#################################################

$TYPO3_CONF_VARS['FE']['pageOverlayFields'] .= ',tx_tqseo_pagetitle,tx_tqseo_pagetitle_rel,tx_tqseo_pagetitle_prefix,tx_tqseo_pagetitle_suffix,tx_tqseo_canonicalurl';
$TYPO3_CONF_VARS['FE']['addRootLineFields'] .= ',tx_tqseo_pagetitle_prefix,tx_tqseo_pagetitle_suffix,tx_tqseo_inheritance';

//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typoLink_PostProc'][] = 'EXT:tq_seo/lib/class.linkparser.php:user_tqseo_linkparser->main';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typoLink_PostProc'][] = 'EXT:tq_seo/lib/sitemap/class.sitemap_indexer.php:user_tqseo_sitemap_indexer->hook_linkParse';

// Caching framework
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'tx_tqseo_cache->clearAll';

// HTTP Header extension
require_once t3lib_extMgm::extPath('tq_seo').'/lib/class.http.php';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['isOutputting'][] = 'user_tqseo_http->main';


#################################################
## SITEMAP
#################################################
// Frontend indexed
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['pageIndexing'][] = 'EXT:tq_seo/lib/sitemap/class.sitemap_indexer.php:user_tqseo_sitemap_indexer';

// Sitemap control
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions']['clearSeoSitemap']	= 'EXT:tq_seo/hooks/sitemap/class.cache_controller_hook.php:&tx_tqseo_sitemap_cache_controller_hook';

// Sitemal controll ajax
$TYPO3_CONF_VARS['BE']['AJAX']['tx_tqseo_sitemap::clearSeoSitemap'] = 'EXT:tq_seo/hooks/sitemap/class.cache_controller.php:tx_tqseo_sitemap_cache_controller->clearSeoSitemap';

#################################################
## TT_NEWS
#################################################
if( !empty($confArr['enableIntegrationTTNews']) ) {
	// Metatag fetch hook
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraItemMarkerHook']['tqseo'] = 'EXT:tq_seo/hooks/tt_news/class.metatags.php:&tx_tqseo_hook_ttnews_metatags';
}

#################################################
## SCHEDULER
#################################################
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_tqseo_scheduler_task_cleanup'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'TQ SEO Cleanup',
    'description'      => 'Cleanup old sitemap entries'
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_tqseo_scheduler_task_sitemap_xml'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'TQ SEO sitemap.xml builder',
    'description'      => 'Build sitemap xml as static file (in uploads/tx_tqseo/sitemap-xml/)'
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_tqseo_scheduler_task_sitemap_txt'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'TQ SEO sitemap.txt builder',
    'description'      => 'Build sitemap txt as static file (in uploads/tx_tqseo/sitemap-txt/)'
);

?>