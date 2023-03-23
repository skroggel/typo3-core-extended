#
# Table structure for table 'pages'
#
CREATE TABLE pages
(
	tx_coreextended_alternative_title    varchar(255) DEFAULT '' NOT NULL,
	tx_coreextended_fe_layout_next_level int(11) DEFAULT '0' NOT NULL,
	tx_coreextended_preview_image        int(11) unsigned NOT NULL default '0',
	tx_coreextended_og_image             int(11) unsigned NOT NULL default '0',
	tx_coreextended_file	               int(11) unsigned NOT NULL default '0',
	tx_coreextended_cover                int(11) unsigned NOT NULL default '0',

);

#
# Table structure for table 'sys_file_metadata'
#
CREATE TABLE sys_file_metadata
(

	tx_coreextended_publisher varchar(255) DEFAULT '' NOT NULL,
	tx_coreextended_source    int(11) unsigned DEFAULT '0',
);


#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content
(

	tx_coreextended_images_no_copyright tinyint(1) unsigned DEFAULT '0' NOT NULL,

);


#
# Table structure for table 'tx_coreextended_domain_model_mediasources'
#
CREATE TABLE tx_coreextended_domain_model_mediasources
(

	uid              int(11) NOT NULL auto_increment,
	pid              int(11) DEFAULT '0' NOT NULL,

	name             varchar(255) DEFAULT '' NOT NULL,
	url              varchar(255) DEFAULT '' NOT NULL,
	internal         tinyint(1) unsigned DEFAULT '0' NOT NULL,

	tstamp           int(11) unsigned DEFAULT '0' NOT NULL,
	crdate           int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id        int(11) unsigned DEFAULT '0' NOT NULL,
	deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime        int(11) unsigned DEFAULT '0' NOT NULL,
	endtime          int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid        int(11) DEFAULT '0' NOT NULL,
	t3ver_id         int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid       int(11) DEFAULT '0' NOT NULL,
	t3ver_label      varchar(255) DEFAULT '' NOT NULL,
	t3ver_state      tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage      int(11) DEFAULT '0' NOT NULL,
	t3ver_count      int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp     int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id    int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent      int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource  mediumblob,

	PRIMARY KEY (uid),
	KEY              parent (pid),
	KEY              t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);
