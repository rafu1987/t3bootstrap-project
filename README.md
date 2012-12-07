# t3bootstrap - TYPO3 & Bootstrap

t3bootstrap is a TYPO3 collection of tools, flexible content elements ([TemplaVoilà](http://typo3.org/extensions/repository/view/templavoila)), custom helper extensions and the basic [twitter Bootstrap](http://twitter.github.com/bootstrap/) framework. Initially developed by us for us, we'll try to move forward to a public version, for you to try out.

*t3bootstrap uses version **2.2.1** of twitter Bootstrap*

## Requirements
* TYPO3 stable version **4.7.7** Source - Get it here: [http://get.typo3.org/4.7](http://get.typo3.org/4.7)
* PHP version 5.3.x
* MySQL version 5.0.x-5.1.x
* A webserver to run TYPO3 on

## Installation
### Follow the installation steps carefully and as described here. Only then, we can guarantee, that t3bootstrap will work for you. Please use t3bootstrap only on a fresh TYPO3 installation.

1) Just install your TYPO3 source. For that go through the 1-2-3 installer, and when finished log in to the backend with:

> Username: *admin*  
> Password: *password*  
> Install Tool Password: *joh316*

You don't need to configure anything else; t3bootstrap will do that for you later.

2) Then get the latest t3bootstrap version, unzip the downloaded file and put all the containing files and folders inside your TYPO3 installation. This brings some basic configuration. You have to clear the TYPO3 cache and reload the backend.

3) After the reload you will need to install the **medbootstraptools** extensions in the extension manager in order to configure t3bootstrap. Go to the extension manager and install the extension. You can ignore the dependencies and you don't need to configure anything else, just install it. :)

4) After the installation of medbootstraptools, you will find a new backend module called **T3Bootstrap configuration** in the admin tools section. Click on the module and go through the settings. When finished, click the **Save** button at the bottom of the page.

5) Write down the admin password and install tool password (and all the other generated passwords, if you created users with the configurator) and reload the page. You will have to log in the backend again with the new password, which was just generated automatically.

6) Now, if you click on the page module, you should see a dummy page tree with some content elements.

### Be aware that you can only do the settings the first time you call the module; after hitting the save button, you won't be able to configure anything through the backend module anymore.


## Features

* Pre-configured default language **German** and second languange **English**
* The default image and text with image content elements are deactivated as they bring problems to twitter Bootstrap; there are two new flexible content elements, where the name ***flexible*** is really appropriate :)
* Flexible content elements for **buttons**, with customizable styles, **containers** with customizable colors (all via the backend and records)
* Flexible content elements for **columns** (1,2,3,4) where you can dynamically set the **width** and **offset**, according to twitter Bootstrap guidelines
* **A backend module to configure the project after installation:**
	* You can set the project name, the basedomains, a copyright notice, which appears in the source code, backend users and choose if the project will be a responsive website or not
	* Passwords of the backend users and install tool are generated dynamically
	* The default databases and contents are imported automatically
* A backend module to test your site on different device sizes (powered by [The Responsinator](http://www.responsinator.com/))
* All TypoScript (setup, constants, FCE, TSConfig) is **outsourced** into files for better **versioning with SVN or GIT**
* German and English **language packs** for extensions and the TYPO3 Backend
* Pre-configured **SEO optimizations** like Google Analytics, Canonical URL, facebook OG Tags and Google Sitemap
* You can access all basic configurations via the **TypoScript Contant Editor** in the TYPO3 Backend module
* **A list of integrated extensions:** All the extensions come pre-configured and with custom settings
	* [news](http://typo3.org/extensions/repository/view/news)
	* [powermail](http://typo3.org/extensions/repository/view/powermail)
	* [kickstarter](http://typo3.org/extensions/repository/view/kickstarter)
	* [jftcaforms](http://typo3.org/extensions/repository/view/jftcaforms)
	* medmobilehide - *an extension to set the display mode for every content element, according twitter Bootstrap*
	* [lorem_ipsum](http://typo3.org/extensions/repository/view/lorem_ipsum) *(customized)*
	* [rzpagetreetools](http://typo3.org/extensions/repository/view/rzpagetreetools)
	* [realurl_clearcache](http://typo3.org/extensions/repository/view/realurl_clearcache)
	* [realurl](http://typo3.org/extensions/repository/view/realurl)
	* [ad_rtepasteplain](http://typo3.org/extensions/repository/view/ad_rtepasteplain)
	* [listmodule_extraedit](http://typo3.org/extensions/repository/view/listmodule_extraedit)
	* [extension_builder](http://typo3.org/extensions/repository/view/extension_builder)
	* [l10nmgr](http://typo3.org/extensions/repository/view/l10nmgr) *(not installed)*
	* [medresponsinator](https://github.com/medialis/medresponsinator)
	* [ws_404](http://typo3.org/extensions/repository/view/ws_404)
	* [scriptmerger](http://typo3.org/extensions/repository/view/scriptmerger)
	* mc_googlesitemapmod *(A modification of the original extension)*
	* [fl_realurl_image](http://typo3.org/extensions/repository/view/fl_realurl_image)
	* [ods_seo](http://typo3.org/extensions/repository/view/ods_seo)
	* [scriptmergerbless](http://typo3.org/extensions/repository/view/scriptmergerbless)
	* [sourceopt](http://typo3.org/extensions/repository/view/sourceopt)
	* [cl_jquery_fancybox](http://typo3.org/extensions/repository/view/cl_jquery_fancybox	)
	* [accessible_is_browse_results](http://typo3.org/extensions/repository/view/accessible_is_browse_results)
	* [medfancyboxcontent](https://github.com/medialis/medfancyboxcontent)
	* [rzgooglemaps2](https://github.com/rafu1987/rzgooglemaps2)
	* [t3_less](http://typo3.org/extensions/repository/view/t3_less)
	* [macina_searchbox](http://typo3.org/extensions/repository/view/macina_searchbox)
	* [in2facebook](http://typo3.org/extensions/repository/view/in2facebook)
	* medbootstraptools
	* [t3jquery](http://typo3.org/extensions/repository/view/t3jquery)
	* [rzdummyimage](http://typo3.org/extensions/repository/view/rzdummyimage)
	* [templavoila](http://typo3.org/extensions/repository/view/templavoila)
	* [medmarkdown](https://github.com/medialis/medmarkdown)
	
## ToDo's
* Implement a way to change the default language in the configurator (right now, German is configured as the default language; of course you can change that with a few steps in the backend by yourself).

**Powered by [medialis.net UG (haftungsbeschränkt)](http://www.medialis.net)**

Developped by **@rafu1987** [github](https://github.com/rafu1987), [twitter](https://twitter.com/rafu1987), [facebook](https://facebook.com/rafu1987)