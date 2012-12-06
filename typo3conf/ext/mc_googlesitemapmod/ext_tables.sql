#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_mcgooglesitemapmod_objective tinytext NOT NULL,
	tx_mcgooglesitemapmod_lastmod int(11) unsigned DEFAULT '0' NOT NULL,
	tx_mcgooglesitemapmod_pageuid blob NOT NULL,
	tx_mcgooglesitemapmod_url text NOT NULL,
	tx_mcgooglesitemapmod_changefreq int(11) unsigned DEFAULT '0' NOT NULL,
	tx_mcgooglesitemapmod_priority double DEFAULT '0' NOT NULL
);



#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_mcgooglesitemapmod_priority double DEFAULT '0' NOT NULL,
	tx_mcgooglesitemapmod_changefreq int(11) unsigned DEFAULT '0' NOT NULL
);



#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_mcgooglesitemapmod_lastmod int(11) unsigned DEFAULT '0' NOT NULL
);
