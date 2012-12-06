<?php
$TYPO3_CONF_VARS['EXTCONF']['realurl'] = array(
   '_DEFAULT' => array(
        'init' => array(
            'enableCHashCache' => 1, 
            'appendMissingSlash' => 'ifNotFile',
            'enableUrlDecodeCache' => 1,
            'enableUrlEncodeCache' => 1,
            'postVarSet_failureMode' => ''
            ),	
		'preVars' => array(
			array(
				'GETvar' => 'no_cache',
				'valueMap' => array(
					'no_cache' => 1,
					'nc' => 1,
				),
				'noMatch' => 'bypass',
			),
			array(
				'GETvar' => 'L',
				'valueMap' => array(  
					'en' => '1'
				),         
				'noMatch' => 'bypass',
			),
		),    
        'pagePath' => array(
    	     'type' => 'user',
                'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
                'spaceCharacter' => '-',
                'languageGetVar' => 'L',  
                'expireDays' => 7,
                //'rootpage_id' => 1, 
                // 'encodeTitle_userProc'=>'EXT:realurl/tx_realurl_encodeTitle_userProc.php:&user_encodeDates',
            ),
            'postVarSets' => array(
                '_DEFAULT' => array(
                   // EXT:news start
                   'news' => array(
                       array(
                           'GETvar' => 'tx_news_pi1[action]',
                           'noMatch' => 'bypass'
                       ),
                       array(
                           'GETvar' => 'tx_news_pi1[controller]',
                           'noMatch' => 'bypass'
                       ),
                       array(
                           'GETvar' => 'tx_news_pi1[news]',
                           'lookUpTable' => array(
                               'table' => 'tx_news_domain_model_news',
                               'id_field' => 'uid',
                               'alias_field' => 'title',
                               'addWhereClause' => ' AND NOT deleted',
                               'useUniqueCache' => 1,
                               'useUniqueCache_conf' => array(
                                   'strtolower' => 1,
                                   'spaceCharacter' => '-',
                               ),
                              'languageGetVar' => 'L',
                              'languageExceptionUids' => '',
                              'languageField' => 'sys_language_uid',
                              'transOrigPointerField' => 'l10n_parent',
                              'autoUpdate' => 1,
                              'expireDays' => 180                             
                           ),
                       ),
                   ),
                       // EXT:news end
                ),
            ),          
    'fileName' => array(
         'defaultToHTMLsuffixOnPrev' => 0,
         'index' => array(
             'feed.rss' => array(
                 'keyValues' => array(
                     'type' => 9818,
                 )
             ),
         ),
     ),
    ),
);

?>