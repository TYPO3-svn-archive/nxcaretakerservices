<?php 

if (!defined ('TYPO3_MODE')) {
	die('Access denied.');
}

$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['nxcaretakerservices']);
if($confArray['enableServerExt'])
{

	$TCA['tx_caretaker_action'] = array (
		'ctrl' => array (
			'title'     => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_action',
			'label'     => 'title',
			'tstamp'    => 'tstamp',
			'crdate'    => 'crdate',
			'cruser_id' => 'cruser_id',
			'default_sortby' => 'ORDER BY title',
			'delete' => 'deleted',
			'requestUpdate' => 'test_service',
			'dividers2tabs'=> 1,
		    'enablecolumns' => array (        
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
	    	),
			'type' => 'testservice',
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/test.png',
		),
		'feInterface' => array (
			'fe_admin_fieldList' => '',
		),
	);
	
	
	if (t3lib_extMgm::isLoaded('caretaker') ){
		include_once(t3lib_extMgm::extPath('caretaker') . 'classes/class.tx_caretaker_ServiceHelper.php');
			
		tx_caretaker_ServiceHelper::registerCaretakerService($_EXTKEY, 'classes/services', 'tx_nxcaretakerservices_InstallTool',  'TYPO3 -> Check for an open Install Tool', 'Look for ENABLE_INSTALL_TOOL');
		
	}

	t3lib_div::loadTCA('tx_caretaker_instance');
	$tempColumns = array('tx_nxcaretakerservices_actions' => Array (
	      'exclude' => 1,
	      'label' => 'LLL:EXT:nxcaretakerservices/locallang_db.xml:tx_caretaker_instanceaction.actions',
	      'config' => Array (
			'type'          => 'select',
			'foreign_table' => 'tx_caretaker_action',
			'size'          => 5,
			'autoSizeMax'   => 25,
			'minitems'      => 0,
			'maxitems'      => 50,
			'MM'            => 'tx_nxcaretakerservices_instance_action_mm',
	   		'MM_opposite_field' => 'instances',
				'size'          => 5,
				'autoSizeMax'   => 10,
				'minitems'      => 0,
				'maxitems'      => 99,
				'wizards' => Array( 
					'_PADDING' => 1, 
					'_VERTICAL' => 1, 
					'edit' => Array( 
						'type' => 'popup', 
						'title' => 'Edit Action', 
						'script' => 'wizard_edit.php', 
						'icon' => 'edit2.gif', 
						'popup_onlyOpenIfSelected' => 1, 
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1', 
					), 
					'add' => Array( 
						'type' => 'script', 
						'title' => 'Create new Action', 
						'icon' => 'add.gif', 
						'params' => Array( 
							'table'=>'tx_caretaker_action', 
							'pid' => '###CURRENT_PID###', 
							'setValue' => 'prepend' 
						), 
						'script' => 'wizard_add.php', 
					),
				), 
	      )
	    )
)
;
	
  t3lib_extMgm::addTCAcolumns("tx_caretaker_instance",$tempColumns,1);
  t3lib_extMgm::addToAllTCAtypes("tx_caretaker_instance","tx_nxcaretakerservices_actions;;;;1-1-1",'',"before:notifications,--palette--;;4,after:test");
	
	
}
if (t3lib_extMgm::isLoaded('nxcaretakerservices') ){
	include_once(t3lib_extMgm::extPath('nxcaretakerservices') . 'classes/class.tx_nxcaretakerservices_ActionServiceHelper.php');

	tx_nxcaretakerservices_ActionServiceHelper::registerCaretakerActionService($_EXTKEY, 'classes/services', 'tx_nxcaretakerservices_ActionTest',  'TYPO3 -> test Action', 'test');
	tx_nxcaretakerservices_ActionServiceHelper::registerCaretakerActionService($_EXTKEY, 'classes/services', 'tx_nxcaretakerservices_InstallTool',  'TYPO3 -> Install Tool Action', 'create and delete the install tool');
	tx_nxcaretakerservices_ActionServiceHelper::registerCaretakerActionService($_EXTKEY, 'classes/services', 'tx_nxcaretakerservices_BackendUser',  'TYPO3 -> User management', 'enable an disable backend users');
	tx_nxcaretakerservices_ActionServiceHelper::registerCaretakerActionService($_EXTKEY, 'classes/services', 'tx_nxcaretakerservices_UpdateNxcaretakerservices',  'TYPO3 -> Update nxcaretakerservices', 'If the nxcaretakerservices extension of the client is older than the servers one, this will update it.');
	tx_nxcaretakerservices_ActionServiceHelper::registerCaretakerActionService($_EXTKEY, 'classes/services', 'tx_nxcaretakerservices_ExtensionManager',  'TYPO3 -> Extension Manager', 'Manage clients extensions.');
	
}





?>