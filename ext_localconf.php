<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

//require_once(t3lib_extMgm::extPath('nxcaretakerservices').'classes/nodes/class.tx_nxcaretakerservices_getChildrenProc.php');

// Register default caretaker Operations
foreach (array('GetInstallTool') as $operationKey) {
	$TYPO3_CONF_VARS['EXTCONF']['caretaker_instance']['operations'][$operationKey] =
		'EXT:nxcaretakerservices/classes/operations/class.tx_nxcaretakerservices_Operation_' . $operationKey . '.php:&tx_nxcaretakerservices_Operation_' . $operationKey;
	
}

//register views for test or actions
//foreach (array('tx_nxcaretakerservices_InstallTool', 'tx_nxcaretakerservices_ActionTest') as $viewKey) {
//	$TYPO3_CONF_VARS['EXTCONF']['caretaker']['views'][$viewKey] = 
//    	'EXT:nxcaretakerservices/classes/views/class.' . $viewKey . '_View.php:&' . $viewKey . '_View';
//}

//register new Nodes
//foreach (array('actionNode') as $getChildrenFunctionKey) {
//	$TYPO3_CONF_VARS['EXTCONF']['caretaker']['getChildrenPostProc'][$getChildrenFunctionKey] = 
//    	'EXT:nxcaretakerservices/classes/nodes/class.tx_nxcaretakerservices_getChildrenProc.php:&tx_nxcaretakerservices_getChildrenProc->' . $getChildrenFunctionKey . 'GetChildren';	
//	
//}

$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::actioninfo']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->ajaxGetNodeInfo';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::doaction']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->doaction';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::getActionButtons']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->ajaxGetActionButtons';

$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::InstallToolActionDelete']    = 'EXT:nxcaretakerservices/classes/services/class.tx_nxcaretakerservices_InstallToolActionService.php:tx_nxcaretakerservices_InstallToolActionService->delete';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::InstallToolActionCreate']    = 'EXT:nxcaretakerservices/classes/services/class.tx_nxcaretakerservices_InstallToolActionService.php:tx_nxcaretakerservices_InstallToolActionService->create';

?>