<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

//require_once(t3lib_extMgm::extPath('nxcaretakerservices').'classes/nodes/class.tx_nxcaretakerservices_getChildrenProc.php');

// Register default caretaker Operations
foreach (array('GetInstallTool', 'InstallToolAction', 'GetBeusers', 'UpdateNxcaretakerservicesAction', 'ExtensionManagement') as $operationKey) {
	$TYPO3_CONF_VARS['EXTCONF']['caretaker_instance']['operations'][$operationKey] =
		'EXT:nxcaretakerservices/classes/operations/class.tx_nxcaretakerservices_Operation_' . $operationKey . '.php:&tx_nxcaretakerservices_Operation_' . $operationKey;
	
}

$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::actioninfo']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->ajaxGetNodeInfo';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::doaction']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->doaction';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_nxcaretakerservices::getActionButtons']    = 'EXT:nxcaretakerservices/classes/ajax/class.tx_nxcaretakerservices_Action.php:tx_nxcaretakerservices_Action->ajaxGetActionButtons';




?>