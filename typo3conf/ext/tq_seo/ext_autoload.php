<?php
$extensionPath = t3lib_extMgm::extPath('tq_seo');

return array(
	'tx_tqseo_cache'						=> $extensionPath.'lib/class.cache.php',
	'tx_tqseo_tools'						=> $extensionPath.'lib/class.tools.php',
	'tx_tqseo_robots_txt'					=> $extensionPath.'lib/class.robots_txt.php',
	'tx_tqseo_sitemap'						=> $extensionPath.'lib/sitemap/class.sitemap.php',

	'tx_tqseo_sitemap_builder_base'			=> $extensionPath.'lib/sitemap/builder/class.base.php',
	'tx_tqseo_sitemap_builder_txt'			=> $extensionPath.'lib/sitemap/builder/class.txt.php',
	'tx_tqseo_sitemap_builder_xml'			=> $extensionPath.'lib/sitemap/builder/class.xml.php',

	'tx_tqseo_sitemap_output_base'			=> $extensionPath.'lib/sitemap/output/class.base.php',
	'tx_tqseo_sitemap_output_txt'			=> $extensionPath.'lib/sitemap/output/class.txt.php',
	'tx_tqseo_sitemap_output_xml'			=> $extensionPath.'lib/sitemap/output/class.xml.php',

	'tx_tqseo_scheduler_task_cleanup'		=> $extensionPath.'lib/scheduler/class.cleanup.php',
	'tx_tqseo_scheduler_task_sitemap_base'	=> $extensionPath.'lib/scheduler/class.sitemap_base.php',
	'tx_tqseo_scheduler_task_sitemap_txt'	=> $extensionPath.'lib/scheduler/class.sitemap_txt.php',
	'tx_tqseo_scheduler_task_sitemap_xml'	=> $extensionPath.'lib/scheduler/class.sitemap_xml.php',

	'tx_tqseo_module_base'					=> $extensionPath.'lib/backend/class.base.php',
	'tx_tqseo_module_standalone'			=> $extensionPath.'lib/backend/class.standalone.php',
	'tx_tqseo_module_tree'					=> $extensionPath.'lib/backend/class.tree.php',
	'tx_tqseo_backend_tools'				=> $extensionPath.'lib/backend/class.tools.php',

	'tx_tqseo_backend_ajax_base'			=> $extensionPath.'lib/backend/ajax/class.base.php',
	'tx_tqseo_backend_ajax_sitemap'			=> $extensionPath.'lib/backend/ajax/class.sitemap.php',
	'tx_tqseo_backend_ajax_page'			=> $extensionPath.'lib/backend/ajax/class.page.php',

	'tx_tqseo_backend_validation_float'		=> $extensionPath.'lib/backend/validation/class.float.php',
);

?>