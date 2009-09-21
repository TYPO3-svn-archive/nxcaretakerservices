<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Register default caretaker Operations
foreach (array('GetInstallTool') as $operationKey) {
	$TYPO3_CONF_VARS['EXTCONF']['caretaker_instance']['operations'][$operationKey] =
		'EXT:nxcaretakerservices/classes/operations/class.tx_nxcaretakerservices_Operation_' . $operationKey . '.php:&tx_nxcaretakerservices_Operation_' . $operationKey;
	
}
foreach (array('tx_nxcaretakerservices_InstallTool') as $viewKey) {
	$TYPO3_CONF_VARS['EXTCONF']['caretaker']['views'][$viewKey] = 
    	'EXT:nxcaretakerservices/classes/views/class.' . $viewKey . '_View.php:&' . $viewKey . '_View';
}


?>