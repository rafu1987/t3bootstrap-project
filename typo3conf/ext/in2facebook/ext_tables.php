<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript/Opengraph/', 'in2facebook Main');
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript/Share/', 'Share Buttons');

$tempColumns = Array (
	'tx_in2facebook_title' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_title',
		'config' => Array (
			'type' => 'input',
			'size' => '30',
			'eval' => 'trim'
		)
	),
	'tx_in2facebook_type' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type',
		'config' => Array (
			'type'  => 'select',
			'items' => array (
				array('LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type.div_activities', '--div--'),
				array('activity', 'activity'),
				array('sport', 'sport'),
				array('LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type.div_business', '--div--'),
				array('bar', 'bar'),
				array('company', 'company'),
				array('cafe', 'cafe'),
				array('hotel', 'hotel'),
				array('restaurant', 'restaurant'),
				array('LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type.div_groups', '--div--'),
				array('cause', 'cause'),
				array('sports_league', 'sports_league'),
				array('sports_team', 'sports_team'),
				array('LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type.div_organizations', '--div--'),
				array('band', 'band'),
				array('government', 'government'),
				array('non_profit', 'non_profit'),
				array('school', 'school'),
				array('university', 'university'),
				array('LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type.div_people', '--div--'),
				array('actor', 'actor'),
				array('athlete', 'athlete'),
				array('author', 'author'),
				array('director', 'director'),
				array('musician', 'musician'),
				array('politician', 'politician'),
				array('public_figure', 'public_figure'),
				array('LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type.div_places', '--div--'),
				array('city', 'city'),
				array('country', 'country'),
				array('landmark', 'landmark'),
				array('state_province', 'state_province'),
				array('LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type.div_entertainment', '--div--'),
				array('album', 'album'),
				array('book', 'book'),
				array('drink', 'drink'),
				array('food', 'food'),
				array('game', 'game'),
				array('product', 'product'),
				array('song', 'song'),
				array('movie', 'movie'),
				array('tv_show', 'tv_show'),
				array('LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_type.div_websites', '--div--'),
				array('blog', 'blog'),
				array('website', 'website'),
				array('article', 'article')
			),
		)
	),
	'tx_in2facebook_url' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_url',
		'config' => Array (
			'type' => 'input',
			'size' => '30',
			'eval' => 'trim',
		)
	),
	'tx_in2facebook_image' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_image',
		'config' => array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
			'uploadfolder' => 'uploads/tx_in2facebook',
			'show_thumbs' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
	'tx_in2facebook_site_name' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_site_name',
		'config' => Array (
			'type' => 'input',
			'size' => '30',
			'eval' => 'trim',
		)
	),
	'tx_in2facebook_admins' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_admins',
		'config' => Array (
			'type' => 'input',
			'size' => '30',
			'eval' => 'trim',
		)
	),
	'tx_in2facebook_description' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:in2facebook/locallang_db.xml:tx_in2facebook_description',
		'config' => Array (
			'type' => 'text',
			'cols' => '30',
			'rows' => '30'
		)
	)
);
t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('pages','tx_in2facebook_title, tx_in2facebook_type, tx_in2facebook_url, tx_in2facebook_image, tx_in2facebook_site_name, tx_in2facebook_admins, tx_in2facebook_description');
?>