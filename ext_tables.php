<?php 

if (!defined ('TYPO3_MODE')) {
	die('Access denied.');
}

if (t3lib_extMgm::isLoaded('caretaker') ){
	include_once(t3lib_extMgm::extPath('caretaker') . 'classes/class.tx_caretaker_ServiceHelper.php');
	tx_caretaker_ServiceHelper::registerCaretakerService($_EXTKEY, 'classes/services', 'tx_nxcaretakerservices_InstallTool',  'TYPO3 -> Check for an open Install Tool', 'Look for ENABLE_INSTALL_TOOL');
	

}
	

?>