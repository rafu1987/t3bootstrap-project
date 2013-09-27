<?php
// ----------------------------------------------------------------------------
// Example of TEQneers SEO Enhancements
//
// Example integrations (eg. in localconf.php or ext_localconf.php):
//
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['metatag-setup'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_metatagSetup';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['metatag-output'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_metatagOutput';
//
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['pagetitle-setup'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_pagetitleSetup';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['pagetitle-output'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_pagetitleOutput';
//
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['pagefooter-setup'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_pagefooterSetup';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['pagefooter-output'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_pagefooterOutput';
//
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['sitemap-index-page'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_sitemapIndexPage';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['sitemap-index-link'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_sitemapIndexLink';
//
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['sitemap-setup'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_sitemapSetup';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['sitemap-text-output'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_sitemapTextOutput';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['sitemap-xml-index-output'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_sitemapXmlIndexOutput';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['sitemap-xml-page-output'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_sitemapXmlPageOutput';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['sitemap-clear'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_sitemapClear';
//
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['robotstxt-marker'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_robotsTxtMarker';
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['robotstxt-output'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_robotsTxtOutput';
//
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_seo']['hooks']['httpheader-output'][] = 'EXT:tq_seo/examples/hooks.php:user_tqseo_hook->hook_httpHeaderOutput';
// ----------------------------------------------------------------------------

class user_tqseo_hook {

	public function hook_metatagSetup(&$args, $obj) {
		// Hook for metatag setup
	}

	public function hook_metatagOutput(&$args, $obj) {
		// Hook for metatag output
	}

	// ------------------------------------------------------------------------

	public function hook_pagetitleSetup(&$args, $obj) {
		// Hook for pagetitle setup
	}

	public function hook_pagetitleOutput(&$args, $obj) {
		// Hook for pagetitle output
	}

	// ------------------------------------------------------------------------

	public function hook_pagefooterSetup(&$args, $obj) {
		// Hook for page footer setup
	}

	public function hook_pagefooterOutput(&$args, $obj) {
		// Hook for page footer output
	}

	// ------------------------------------------------------------------------

	public function hook_sitemapIndexPage(&$args) {
		// Hook for sitemap page indexer
	}

	public function hook_sitemapIndexLink(&$args) {
		// Hook for sitemap link indexer
	}

	// ------------------------------------------------------------------------

	public function hook_sitemapSetup(&$args, $obj) {
		// Hook for sitemap setup
	}

	public function hook_sitemapTextOutput(&$args, $obj) {
		// Hook for xml text output
	}

	public function hook_sitemapXmlIndexOutput(&$args, $obj) {
		// Hook for xml index-page output
	}

	public function hook_sitemapXmlPageOutput(&$args, $obj) {
		// Hook for xml page output
	}

	public function hook_sitemapClear(&$args, $obj) {
		// Hook for sitemap clearing (truncating via clear-cache hook)
	}

	// ------------------------------------------------------------------------

	public function hook_robotsTxtMarker(&$args, $obj) {
		// Hook for robots.txt marker list
	}

	public function hook_robotsTxtOutput(&$args, $obj) {
		// Hook for robots.txt output
	}

	// ------------------------------------------------------------------------

	public function hook_httpHeaderOutput($args, $obj) {
		// Hook for http header output
	}

	// ------------------------------------------------------------------------
}

?>
