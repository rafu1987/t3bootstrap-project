#
# Table structure for table 'tx_flrealurlimage_cache'
#
CREATE TABLE `tx_flrealurlimage_cache` (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	image_path varchar(255) default '',
	realurl_path varchar(255) default '',
	page_id varchar(255) default '',

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY realurl_p (realurl_path)
);