<?php
if (!defined ('TYPO3_MODE')) {
	die('Access denied.');
}


if (TYPO3_MODE == 'BE') {
	// get extension configuration
	$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3jquery']);

	if ($confArr['enableStyleStatic']) {
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/bootstrap',         'T3JQUERY Style: Bootstrap 2 default');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/bootstrap-3.0.x',   'T3JQUERY Style: Bootstrap 3 default');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/mobile',            'T3JQUERY Style: Mobiles default');
		if ($confArr['jQueryUiVersion'] == '1.9.x') {
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/base',           'T3JQUERY Style: UI Base');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/black-tie',      'T3JQUERY Style: UI Black-Tie');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/blitzer',        'T3JQUERY Style: UI Blitzer');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/cupertino',      'T3JQUERY Style: UI Cupertino');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/dark-hive',      'T3JQUERY Style: UI Dark-Hive');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/dot-luv',        'T3JQUERY Style: UI Dot-Luv');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/eggplant',       'T3JQUERY Style: UI Eggplant');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/excite-bike',    'T3JQUERY Style: UI Excite-Bike');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/flick',          'T3JQUERY Style: UI Flick');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/hot-sneaks',     'T3JQUERY Style: UI Hot-Sneaks');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/humanity',       'T3JQUERY Style: UI Humanity');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/le-frog',        'T3JQUERY Style: UI Le-Frog');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/mint-choc',      'T3JQUERY Style: UI Mint-Choc');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/overcast',       'T3JQUERY Style: UI Overcast');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/pepper-grinder', 'T3JQUERY Style: UI Pepper-Grinder');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/redmond',        'T3JQUERY Style: UI Redmond');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/smoothness',     'T3JQUERY Style: UI Smoothness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/south-street',   'T3JQUERY Style: UI South-Street');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/start',          'T3JQUERY Style: UI Start');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/sunny',          'T3JQUERY Style: UI Sunny');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/swanky-purse',   'T3JQUERY Style: UI Swanky-Purse');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/trontastic',     'T3JQUERY Style: UI Trontastic');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/ui-darkness',    'T3JQUERY Style: UI UI-Darkness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/ui-lightness',   'T3JQUERY Style: UI UI-Lightness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.9.x/vader',          'T3JQUERY Style: UI Vader');
		} elseif ($confArr['jQueryUiVersion'] == '1.10.x') {
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/black-tie',      'T3JQUERY Style: UI Black-Tie');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/blitzer',        'T3JQUERY Style: UI Blitzer');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/cupertino',      'T3JQUERY Style: UI Cupertino');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/dark-hive',      'T3JQUERY Style: UI Dark-Hive');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/dot-luv',        'T3JQUERY Style: UI Dot-Luv');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/eggplant',       'T3JQUERY Style: UI Eggplant');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/excite-bike',    'T3JQUERY Style: UI Excite-Bike');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/flick',          'T3JQUERY Style: UI Flick');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/hot-sneaks',     'T3JQUERY Style: UI Hot-Sneaks');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/humanity',       'T3JQUERY Style: UI Humanity');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/le-frog',        'T3JQUERY Style: UI Le-Frog');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/mint-choc',      'T3JQUERY Style: UI Mint-Choc');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/overcast',       'T3JQUERY Style: UI Overcast');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/pepper-grinder', 'T3JQUERY Style: UI Pepper-Grinder');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/redmond',        'T3JQUERY Style: UI Redmond');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/smoothness',     'T3JQUERY Style: UI Smoothness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/south-street',   'T3JQUERY Style: UI South-Street');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/start',          'T3JQUERY Style: UI Start');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/sunny',          'T3JQUERY Style: UI Sunny');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/swanky-purse',   'T3JQUERY Style: UI Swanky-Purse');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/trontastic',     'T3JQUERY Style: UI Trontastic');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/ui-darkness',    'T3JQUERY Style: UI UI-Darkness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/ui-lightness',   'T3JQUERY Style: UI UI-Lightness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui-1.10.x/vader',          'T3JQUERY Style: UI Vader');
		} else {
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/blitzer',        'T3JQUERY Style: UI Blitzer');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/cupertino',      'T3JQUERY Style: UI Cupertino');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/dark-hive',      'T3JQUERY Style: UI Dark-Hive');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/eggplant',       'T3JQUERY Style: UI Eggplant');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/excite-bike',    'T3JQUERY Style: UI Excite-Bike');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/flick',          'T3JQUERY Style: UI Flick');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/hot-sneaks',     'T3JQUERY Style: UI Hot-Sneaks');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/humanity',       'T3JQUERY Style: UI Humanity');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/le-frog',        'T3JQUERY Style: UI Le-Frog');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/overcast',       'T3JQUERY Style: UI Overcast');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/pepper-grinder', 'T3JQUERY Style: UI Pepper-Grinder');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/redmond',        'T3JQUERY Style: UI Redmond');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/smoothness',     'T3JQUERY Style: UI Smoothness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/south-street',   'T3JQUERY Style: UI South-Street');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/start',          'T3JQUERY Style: UI Start');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/sunny',          'T3JQUERY Style: UI Sunny');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/typo3',          'T3JQUERY Style: UI Typo3');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/ui-darkness',    'T3JQUERY Style: UI UI-Darkness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/ui-lightness',   'T3JQUERY Style: UI UI-Lightness');
			t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/vader',          'T3JQUERY Style: UI Vader');
		}
	}

	if (! $confArr['integrateFromCDN']) {
		t3lib_extMgm::addModulePath('tools_txt3jqueryM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		t3lib_extMgm::addModule('tools', 'txt3jqueryM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	}
}

?>