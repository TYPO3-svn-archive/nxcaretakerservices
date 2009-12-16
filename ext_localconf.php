<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

//require_once(t3lib_extMgm::extPath('nxcaretakerservices').'classes/nodes/class.tx_nxcaretakerservices_getChildrenProc.php');

// Register default caretaker Operations
foreach (array('GetInstallTool', 'InstallToolAction', 'GetBeusers', 'UpdateNxcaretakerservicesAction', 'ExtensionManagement', 'UnsecureEncryptionKey', 'DeprecationLog', 'SSLKeyCreator', 'ClearCacheAction') as $operationKey) {
	$TYPO3_CONF_VARS['EXTCONF']['caretaker_instance']['operations'][$operationKey] =
		'EXT:nxcaretakerservices/classes/operations/class.tx_nxcaretakerservices_Operation_' . $operationKey . '.php:&tx_nxcaretakerservices_Operation_' . $operationKey;
	
}

$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::actioninfo']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->ajaxGetNodeInfo';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::doaction']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->doaction';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::getActionButtons']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->ajaxGetActionButtons';


$TYPO3_CONF_VARS['EXTCONF']['caretaker']['additionalTabs']['actionTab'] = '{		
    	    			id			:	"nxcaretakerAction",
    	    			title		:	"Actions",
    	    			xtype   	: 	"panel",    	    			
    	    			tbar		: 	{},
    	    			listeners	: 	{
    	    					beforerender :	function(tab){
					    	    				Ext.Ajax.request({
					    	    					url 	: 	back_path + "ajax.php",					    	    					
					    	    					success : 	function(result, request) {					    	    						    					
								    	    						var jsonData = Ext.util.JSON.decode(result.responseText);
								    	    						tab.getTopToolbar().add(jsonData);
								    	    						tab.doLayout();
								    	    					},
				    	    						params	:	{ 
						    	    								ajaxID: "tx_nxcaretakerservices::getActionButtons",
						    	    								node:  node_id,
						    	    								back_path: back_path 
						    	    							}
					    	    				}); 
					    	    			}
    	    						}
    	            	}';


//$TYPO3_CONF_VARS['BE']['loginSecurityLevel'] = 'normal';
//t3lib_extMgm::addService($_EXTKEY,  'auth' /* sv type */,  'tx_nxcaretakerservices_AuthService' /* sv key */,
//	array(
//
//		'title' => 'nxcaretakerservices-Authentication',
//		'description' => 'Authentication service for nxcaretakerservices',
//
//		'subtype' => 'getUserBE,authUserBE',
//
//		'available' => 1,
//		'priority' => 100,
//		'quality' => 50,
//
//		'os' => '',
//		'exec' => '',
//
//		'classFile' => t3lib_extMgm::extPath($_EXTKEY).'classes/auth/class.tx_nxcaretakerservices_AuthService.php',
//		'className' => 'tx_nxcaretakerservices_AuthService',
//	)
//);

?>