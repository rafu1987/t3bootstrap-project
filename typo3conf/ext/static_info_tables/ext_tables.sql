#
# Table structure for table "static_territories"
#
CREATE TABLE static_territories (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  tr_iso_nr int(11) unsigned DEFAULT '0' NOT NULL,
  tr_parent_iso_nr int(11) unsigned DEFAULT '0' NOT NULL,
  tr_name_en varchar(50) DEFAULT '' NOT NULL,
  PRIMARY KEY (uid)
);

#
# Table structure for table "static_countries"
#
CREATE TABLE static_countries (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) DEFAULT '0' NOT NULL,
  cn_iso_2 char(2) DEFAULT '' NOT NULL,
  cn_iso_3 char(3) DEFAULT '' NOT NULL,
  cn_iso_nr int(11) unsigned DEFAULT '0' NOT NULL,
  cn_parent_tr_iso_nr int(11) unsigned DEFAULT '0' NOT NULL,
  cn_official_name_local varchar(128) DEFAULT '' NOT NULL,
  cn_official_name_en varchar(128) DEFAULT '' NOT NULL,
  cn_capital varchar(45) DEFAULT '' NOT NULL,
  cn_tldomain char(2) DEFAULT '' NOT NULL,
  cn_currency_iso_3 char(3) DEFAULT '' NOT NULL,
  cn_currency_iso_nr int(10) unsigned DEFAULT '0' NOT NULL,
  cn_phone int(10) unsigned DEFAULT '0' NOT NULL,
  cn_eu_member tinyint(3) unsigned DEFAULT '0' NOT NULL,
  cn_uno_member tinyint(3) unsigned DEFAULT '0' NOT NULL,
  cn_address_format tinyint(3) unsigned DEFAULT '0' NOT NULL,
  cn_zone_flag tinyint(4) DEFAULT '0' NOT NULL,
  cn_short_local varchar(70) DEFAULT '' NOT NULL,
  cn_short_en varchar(50) DEFAULT '' NOT NULL,
  PRIMARY KEY (uid)
);


#
# Table structure for table "static_country_zones"
#
CREATE TABLE static_country_zones (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  zn_country_iso_2 char(2) DEFAULT '' NOT NULL,
  zn_country_iso_3 char(3) DEFAULT '' NOT NULL,
  zn_country_iso_nr int(11) unsigned DEFAULT '0' NOT NULL,
  zn_code varchar(45) DEFAULT '' NOT NULL,
  zn_name_local varchar(128) DEFAULT '' NOT NULL,
  zn_name_en varchar(50) DEFAULT '' NOT NULL,
  PRIMARY KEY (uid)
);


#
# Table structure for table "static_currencies"
#
CREATE TABLE static_currencies (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  cu_iso_3 char(3) DEFAULT '' NOT NULL,
  cu_iso_nr int(11) unsigned DEFAULT '0' NOT NULL,
  cu_name_en varchar(50) DEFAULT '' NOT NULL,
  cu_symbol_left varchar(12) DEFAULT '' NOT NULL,
  cu_symbol_right varchar(12) DEFAULT '' NOT NULL,
  cu_thousands_point char(1) DEFAULT '' NOT NULL,
  cu_decimal_point char(1) DEFAULT '' NOT NULL,
  cu_decimal_digits tinyint(3) unsigned DEFAULT '0' NOT NULL,
  cu_sub_name_en varchar(20) DEFAULT '' NOT NULL,
  cu_sub_divisor int(11) DEFAULT '1' NOT NULL,
  cu_sub_symbol_left varchar(12) DEFAULT '' NOT NULL,
  cu_sub_symbol_right varchar(12) DEFAULT '' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table "static_languages"
#
CREATE TABLE static_languages (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  lg_iso_2 char(2) DEFAULT '' NOT NULL,
  lg_name_local varchar(99) DEFAULT '' NOT NULL,
  lg_name_en varchar(50) DEFAULT '' NOT NULL,
  lg_typo3 char(2) DEFAULT '' NOT NULL,
  lg_country_iso_2 char(2) DEFAULT '' NOT NULL,
  lg_collate_locale varchar(5) DEFAULT '' NOT NULL,
  lg_sacred tinyint(3) unsigned DEFAULT '0' NOT NULL,
  lg_constructed tinyint(3) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table "tx_staticinfotables_hotlist"
#
CREATE TABLE tx_staticinfotables_hotlist (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  application varchar(20) DEFAULT '' NOT NULL,
  sorting bigint(20) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local)
);

