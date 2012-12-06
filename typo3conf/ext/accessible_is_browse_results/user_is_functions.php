<?php
class user_is_functions {

	function change_title() {
	
		$conf=$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_indexedsearch.'];
		$sitename=$GLOBALS['TSFE']->tmpl->setup['sitetitle'];
		$orig_title=$GLOBALS['TSFE']->config['config']['pageTitleFirst']? $GLOBALS['TSFE']->page['title'].': '.$sitename : $sitename.': '.$GLOBALS['TSFE']->page['title'];
		$params_search = t3lib_div::_POST('tx_indexedsearch') ? t3lib_div::_POST('tx_indexedsearch') : t3lib_div::_GET('tx_indexedsearch');

		if($params_search['sword']) {
			$num_page=$params_search['pointer'] ? $params_search['pointer']+1 : '1';
			$page_title=$orig_title.' - '.$conf['pageTitleAdd'].' '.$params_search['sword'].' - '.$conf['pageTitlePage'].' '.$num_page;
			return $page_title;
		} else {
			// original code from class.tslib_pagegen.php :
			$titleTagContent = $GLOBALS['TSFE']->tmpl->printTitle(
			$GLOBALS['TSFE']->altPageTitle?$GLOBALS['TSFE']->altPageTitle:$GLOBALS['TSFE']->page['title'],
			$GLOBALS['TSFE']->config['config']['noPageTitle'],
			$GLOBALS['TSFE']->config['config']['pageTitleFirst']
		);
			return $titleTagContent;
		}
	}
	
	function add_totop_params() {
	
		$params_search = t3lib_div::_GET('tx_indexedsearch');
		if($params_search) {
				$addParams=t3lib_div::_GET('no_cache') ? '&no_cache=1': '' ;
				foreach($params_search as $k=>$v) {
					$addParams .= '&tx_indexedsearch['.$k.']='.$v;
				}
		}
		return($addParams);
	}

}
?>
