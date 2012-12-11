#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_jfmulticontent_view varchar(30) DEFAULT '' NOT NULL,
	tx_jfmulticontent_pages text,
	tx_jfmulticontent_contents text,
	tx_jfmulticontent_irre text,

	tx_jfmulticontent_irre_parentid int(11) DEFAULT '0' NOT NULL,
	KEY jfmulticontent (tx_jfmulticontent_irre_parentid,sorting)
);

CREATE TABLE tt_content (
);
