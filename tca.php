<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Tobias Liebig		<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek 	<hlubek@networkteam.com>
 * @Author	Patrick Kollodzik	<patrick@work.de>  
 * 
 * $$Id: tca.php 46 2008-06-19 16:09:17Z martin $$
 */

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');



$TCA['tx_caretaker_action'] = array (
	'ctrl' => $TCA['tx_caretaker_action']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,exec_interval,testservice,testconf,name,last_exec'
	),
	'feInterface' => $TCA['tx_caretaker_action']['feInterface'],
	'columns' => Array (
		'hidden' => Array (        
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config'  => Array (
				'type'    => 'check',
				'default' => '0'
			),
		),
		'starttime' => Array (        
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
            'config' => Array (
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            )
        ),
        'endtime' => Array (        
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
            'config' => Array (
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => Array (
                    'upper' => mktime(0,0,0,12,31,2020),
                    'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
                )
            )
        ),
        'fe_group' => Array (        
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
            'config' => Array (
                'type' => 'select',
                'items' => Array (
                    Array('', 0),
                    Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
                    Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
                    Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
                ),
                'foreign_table' => 'fe_groups'
            )
        ),
        'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.description',
			'config' => Array (		
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			)
		),
		'test_interval' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.test_interval',
			'config' => Array (
				'type'     => 'select',
				'items'    =>Array(
					Array('1 Minute',       60),
					Array('5 Minutes',     300),
					Array('10 Minutes',    600),
					Array('15 Minutes',    900),
					Array('20 Minutes',   1200),
					Array('30 Minutes',   1800),
					Array('45 Minutes',   2700),
					Array('1 Hour',       3600),
					Array('2 Hours',      7200),
					Array('4 Hours',     14400),
					Array('8 Hours',     28800),
					Array('10 Hours',    36000),
					Array('12 Hours',    43200),
					Array('1 Day',       86400),
					Array('2 Days',     172800),
					Array('1 Week',     604800),
				),
				'default' => 0
			)
		),
		'test_interval_start_hour' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.test_interval_start_hour',
			'config' => Array (
				'type'     => 'select',
				'items'    =>Array(
					Array('',0),Array(1,1),Array(2,2),Array(3,3),Array(4,4),	Array(5,5),Array(6,6),Array(7,7),Array(8,8),Array(9,9),Array(10,10),Array(11,11),Array(12,12),
					Array(13,13),Array(14,14),Array(15,15),Array(16,16),Array(17,17),Array(18,18),Array(19,19),Array(20,20),Array(21,21),Array(22,22),Array(23,23),Array(24,24),					
				)
			)
		),
		'test_interval_stop_hour' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.test_interval_stop_hour',
			'config' => Array (
				'type'     => 'select',
				'items'    =>Array(
					Array('',0),Array(1,1),Array(2,2),Array(3,3),Array(4,4),	Array(5,5),Array(6,6),Array(7,7),Array(8,8),Array(9,9),Array(10,10),Array(11,11),Array(12,12),
					Array(13,13),Array(14,14),Array(15,15),Array(16,16),Array(17,17),Array(18,18),Array(19,19),Array(20,20),Array(21,21),Array(22,22),Array(23,23),Array(24,24),					
				)
			)
		),
		'test_service' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.test_service',
			'config' => Array (
				'type' => 'select',
				'items' => array (
					0 => array('LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.test_service.select_service', '')
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'test_conf' => Array (
			'displayCond' => 'FIELD:test_service:REQ:true',
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.test_conf',
			'config' => Array (
				'type' => 'flex',
				'ds_pointerField' => 'test_service',
				'ds' => array()
			)
		),
		'groups' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.groups',
		
			'config' => Array (
				'type'          => 'select',
				'form_type'     => 'user',
				'userFunc'      => 'tx_ttaddress_treeview->displayGroupTree',
				'treeView'      => 1,
				'foreign_table' => 'tx_caretaker_testgroup',
				'size'          => 5,
				'autoSizeMax'   => 25,
				'minitems'      => 0,
				'maxitems'      => 50,
				'MM'            => 'tx_caretaker_testgroup_test_mm',
			),
			
		),
		'instances' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.instances',
		
			'config' => Array (
				'type'          => 'select',
				'foreign_table' => 'tx_caretaker_instance',
				'size'          => 5,
				'autoSizeMax'   => 25,
				'minitems'      => 0,
				'maxitems'      => 50,
				'MM'            => 'tx_caretaker_instance_test_mm',
			),
			
		),
		'notifications' => Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.notifications',
			'config' => Array (
				'type'          => 'group',
				'internal_type' => 'db',
 				'allowed'       => 'tt_address',
				'size'          => 5,
				'autoSizeMax'   => 25,
				'minitems'      => 0,
				'maxitems'      => 50,
			),
		)
		
	),
	'types' => array (
		'0' => array('showitem' => 'test_service;;;;1-1-1, title;;1;;2-2-2,test_interval;;2, description;;;;3-3-3, test_conf;;;;4-4-4,
					--div--;LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.tab.relations, groups, instances,
					--div--;LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.tab.notifications, notifications'
					)
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden, starttime,endtime,fe_group'),
		'2' => array('showitem' => 'test_interval_start_hour,test_interval_stop_hour'),
	)
);


?>