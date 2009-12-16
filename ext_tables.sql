



#
# Table structure for table 'tx_caretaker_action'
#
CREATE TABLE tx_caretaker_action (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    fe_group int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	description text DEFAULT '' NOT NULL,

	test_interval int(11) DEFAULT '0' NOT NULL,
	test_interval_start_hour tinyint(4) DEFAULT '0' NOT NULL,
	test_interval_stop_hour tinyint(4) DEFAULT '0' NOT NULL,
	test_service varchar(255) DEFAULT '' NOT NULL,
	test_conf text NOT NULL,
	
	notifications varchar(255) DEFAULT '' NOT NULL,

	groups int(11) DEFAULT '0' NOT NULL,
	instances int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_caretaker_instance_test_mm'
#
CREATE TABLE tx_nxcaretakerservices_instance_action_mm (

	uid int(11) NOT NULL auto_increment,
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	sorting_foreign int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# add Table structure for table 'tx_caretaker_instance'
#
CREATE TABLE tx_caretaker_instance (
	tx_nxcaretakerservices_actions text NOT NULL,	
);
