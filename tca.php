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
				'MM'            => 'tx_nxcaretakerservices_instance_action_mm',
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
		'0' => array('showitem' => 'test_service;;;;1-1-1, title;;1;;2-2-2, description;;;;3-3-3, test_conf;;;;4-4-4,
					--div--;LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.tab.relations, instances,
					--div--;LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action.tab.notifications, notifications'
					)
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden,fe_group'),		
	)
);


?>