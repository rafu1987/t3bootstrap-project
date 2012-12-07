<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TYPO3_CONF_VARS['EXT']['extList'] = 'info,perm,func,filelist,extbase,fluid,about,version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,css_styled_content,t3skin,t3editor,reports,felogin,form';

$typo_db_extTableDef_script = 'extTables.php';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

$TYPO3_CONF_VARS['EXT']['extList'] = 'extbase,css_styled_content,info,perm,func,filelist,fluid,about,version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,t3skin,t3editor,reports,felogin,form,rsaauth,saltedpasswords,t3jquery,static_info_tables,static_info_tables_de,kickstarter,extension_builder,workspaces,cshmanual,linkvalidator,recycler,feedit,scheduler,realurl,realurl_clearcache,phpmyadmin,cl_jquery_fancybox,lorem_ipsum,ad_rtepasteplain,rzdummyimage,powermail,rzpagetreetools,mc_googlesitemapmod,ws_404,listmodule_extraedit,rzgooglemaps2,templavoila,sys_action,opendocs,fl_realurl_image,sourceopt,indexed_search,macina_searchbox,accessible_is_browse_results,in2facebook,ods_seo,t3_less,medresponsinator,medbootstraptools,medmobilehide,jftcaforms,news,medfancyboxcontent,adodb,scriptmergerbless,scriptmergercache,medmarkdown,scriptmerger';

$TYPO3_CONF_VARS['SYS']['encryptionKey'] = 'e7cdb05e44d6c55267a45ed9a5e7f0dbde64876623579f94e33df9de13eb8ce74fd9adbe2eb2effa5d560b3d4bbdef98';

$typo_db_username = '';
$typo_db_password = '';
$typo_db_host = '';
$typo_db = '';

$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = '/usr/local/bin/';
$TYPO3_CONF_VARS['GFX']['im_path'] = '/usr/local/bin/';

@include(PATH_typo3conf . 'settings.php');

$TYPO3_CONF_VARS['SYS']['compat_version'] = '4.7';
$TYPO3_CONF_VARS['GFX']['jpg_quality'] = '100';
$TYPO3_CONF_VARS['SYS']['enableDeprecationLog'] = '';
$TYPO3_CONF_VARS['BE']['versionNumberInFilename'] = '0';
$TYPO3_CONF_VARS['SYS']['sitename'] = 'Project';
$TYPO3_CONF_VARS['BE']['disable_exec_function'] = '0';
$TYPO3_CONF_VARS['GFX']['gdlib_png'] = '1';
$TYPO3_CONF_VARS['GFX']['im_version_5'] = 'im6';
$TYPO3_CONF_VARS['BE']['installToolPassword'] = '5f4dcc3b5aa765d61d8327deb882cf99';
$TYPO3_CONF_VARS['SYS']['ddmmyy'] = 'd.m.y';
$TYPO3_CONF_VARS['GFX']['im_noScaleUp'] = '1';
$TYPO3_CONF_VARS['GFX']['gdlib'] = '1';
$TYPO3_CONF_VARS['GFX']['png_truecolor'] = '1';
$TYPO3_CONF_VARS['GFX']['im'] = '1';
$TYPO3_CONF_VARS['GFX']['im_combine_filename'] = 'composite';

$TYPO3_CONF_VARS['EXT']['extList_FE'] = 'extbase,css_styled_content,fluid,version,install,rtehtmlarea,t3skin,felogin,form,rsaauth,saltedpasswords,t3jquery,static_info_tables,static_info_tables_de,kickstarter,extension_builder,workspaces,feedit,realurl,realurl_clearcache,phpmyadmin,cl_jquery_fancybox,lorem_ipsum,ad_rtepasteplain,rzdummyimage,powermail,rzpagetreetools,mc_googlesitemapmod,ws_404,listmodule_extraedit,rzgooglemaps2,templavoila,fl_realurl_image,sourceopt,indexed_search,macina_searchbox,accessible_is_browse_results,in2facebook,ods_seo,t3_less,medresponsinator,medbootstraptools,medmobilehide,jftcaforms,news,medfancyboxcontent,adodb,scriptmergerbless,scriptmergercache,medmarkdown,scriptmerger'; 

$TYPO3_CONF_VARS['EXT']['extConf']['saltedpasswords'] = 'a:2:{s:3:"FE.";a:2:{s:7:"enabled";s:1:"1";s:21:"saltedPWHashingMethod";s:28:"tx_saltedpasswords_salts_md5";}s:3:"BE.";a:2:{s:7:"enabled";s:1:"1";s:21:"saltedPWHashingMethod";s:28:"tx_saltedpasswords_salts_md5";}}';
$TYPO3_CONF_VARS['BE']['loginSecurityLevel']  = 'rsa';
$TYPO3_CONF_VARS['FE']['loginSecurityLevel']  = 'rsa';
$TYPO3_CONF_VARS['EXT']['extConf']['static_info_tables'] = 'a:1:{s:7:"charset";s:5:"utf-8";}';
$TYPO3_CONF_VARS['EXT']['extConf']['static_info_tables_de'] = 'a:1:{s:5:"dummy";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['templavoila'] = 'a:2:{s:7:"enable.";a:3:{s:13:"oldPageModule";s:1:"0";s:19:"selectDataStructure";s:1:"0";s:15:"renderFCEHeader";s:1:"1";}s:9:"staticDS.";a:3:{s:6:"enable";s:1:"0";s:8:"path_fce";s:61:"fileadmin/templates/default/templates/templavoila/static/fce/";s:9:"path_page";s:62:"fileadmin/templates/default/templates/templavoila/static/page/";}}';
$TYPO3_CONF_VARS['EXT']['extConf']['extension_builder'] = 'a:3:{s:15:"enableRoundtrip";s:1:"0";s:15:"backupExtension";s:1:"1";s:9:"backupDir";s:35:"uploads/tx_extensionbuilder/backups";}';
$TYPO3_CONF_VARS['EXT']['extConf']['em'] = 'a:1:{s:17:"selectedLanguages";s:2:"de";}';
$TYPO3_CONF_VARS['EXT']['extConf']['workspaces'] = 'a:0:{}';
$TYPO3_CONF_VARS['EXT']['extConf']['scheduler'] = 'a:3:{s:11:"maxLifetime";s:4:"1440";s:11:"enableBELog";s:1:"1";s:15:"showSampleTasks";s:1:"1";}';
$TYPO3_CONF_VARS['EXT']['extConf']['realurl'] = 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['phpmyadmin'] = 'a:4:{s:12:"hideOtherDBs";s:1:"1";s:9:"uploadDir";s:21:"uploads/tx_phpmyadmin";s:10:"allowedIps";s:0:"";s:12:"useDevIpMask";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['t3jquery'] = 'a:13:{s:15:"alwaysIntegrate";s:1:"1";s:17:"integrateToFooter";s:1:"1";s:17:"enableStyleStatic";s:1:"0";s:18:"dontIntegrateOnUID";s:0:"";s:23:"dontIntegrateInRootline";s:0:"";s:13:"jqLibFilename";s:23:"jquery-###VERSION###.js";s:9:"configDir";s:19:"uploads/tx_t3jquery";s:13:"jQueryVersion";s:5:"1.8.x";s:15:"jQueryUiVersion";s:5:"1.9.x";s:18:"jQueryTOOLSVersion";s:0:"";s:22:"jQueryBootstrapVersion";s:0:"";s:16:"integrateFromCDN";s:1:"0";s:11:"locationCDN";s:6:"jquery";}';
$TYPO3_CONF_VARS['EXT']['extConf']['image_crop'] = 'a:1:{s:12:"enableDevLog";s:1:"0";}'; 
$TYPO3_CONF_VARS['EXT']['extConf']['rzdummyimage'] = 'a:5:{s:5:"width";s:3:"600";s:6:"height";s:3:"400";s:9:"textColor";s:6:"ffffff";s:15:"backgroundColor";s:6:"cccccc";s:9:"storePath";s:34:"fileadmin/user_upload/rzdummyimage";}'; 
$TYPO3_CONF_VARS['EXT']['extConf']['powermail'] = 'a:5:{s:12:"disableIpLog";s:1:"0";s:20:"disableBackendModule";s:1:"0";s:24:"disablePluginInformation";s:1:"0";s:13:"enableCaching";s:1:"0";s:15:"l10n_mode_merge";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['news'] = 'a:12:{s:29:"removeListActionFromFlexforms";s:1:"2";s:20:"pageModuleFieldsNews";s:313:"LLL:EXT:news/Resources/Private/Language/locallang_be.xml:pagemodule_simple=title,datetime;LLL:EXT:news/Resources/Private/Language/locallang_be.xml:pagemodule_advanced=title,datetime,teaser,category;LLL:EXT:news/Resources/Private/Language/locallang_be.xml:pagemodule_complex=title,datetime,teaser,category,archive;";s:24:"pageModuleFieldsCategory";s:17:"title,description";s:6:"tagPid";s:1:"1";s:14:"hideMediaTable";s:1:"1";s:13:"hideFileTable";s:1:"1";s:13:"prependAtCopy";s:1:"1";s:19:"categoryRestriction";s:4:"none";s:22:"contentElementRelation";s:1:"1";s:13:"manualSorting";s:1:"1";s:11:"archiveDate";s:4:"date";s:12:"showImporter";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['ws_404'] = 'a:4:{s:16:"pagesFor404Error";s:2:"28";s:11:"languageVar";s:1:"L";s:7:"typeNum";s:0:"";s:11:"staticFiles";s:52:"gif,jpg,jpeg,png,txt,log,css,js,ico,xml,doc,docx,pdf";}';
$TYPO3_CONF_VARS['EXT']['extConf']['opendocs'] = 'a:1:{s:12:"enableModule";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['fl_realurl_image'] = 'a:4:{s:10:"storagePid";s:2:"33";s:12:"cacheControl";s:1:"1";s:9:"fileLinks";s:8:"hardLink";s:17:"virtualPathRemove";s:0:"";}';
$TYPO3_CONF_VARS['EXT']['extConf']['indexed_search'] = 'a:18:{s:8:"pdftools";s:9:"/usr/bin/";s:8:"pdf_mode";s:2:"20";s:5:"unzip";s:9:"/usr/bin/";s:6:"catdoc";s:9:"/usr/bin/";s:6:"xlhtml";s:9:"/usr/bin/";s:7:"ppthtml";s:9:"/usr/bin/";s:5:"unrtf";s:9:"/usr/bin/";s:9:"debugMode";s:1:"0";s:18:"fullTextDataLength";s:1:"0";s:23:"disableFrontendIndexing";s:1:"0";s:21:"enableProjectphoneSearch";s:1:"1";s:6:"minAge";s:1:"0";s:6:"maxAge";s:1:"0";s:16:"maxExternalFiles";s:1:"5";s:26:"useCrawlerForExternalFiles";s:1:"0";s:11:"flagBitMask";s:3:"192";s:16:"ignoreExtensions";s:0:"";s:17:"indexExternalURLs";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['medbootstraptools'] = 'a:1:{s:9:"less_path";s:37:"fileadmin/templates/project/app/less/";}';
$TYPO3_CONF_VARS['EXT']['extConf']['scriptmergercache'] = 'a:1:{s:9:"cacheTime";s:5:"86400";}';
?>