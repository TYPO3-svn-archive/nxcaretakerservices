<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Matthias Elbert (matthias.elbert@netlogix.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('caretaker_instance', 'classes/class.tx_caretakerinstance_OperationResult.php'));
//require_once(PATH_t3lib.'class.t3lib_beUserAuth.php');
/**
 * A simple look after the existence of a file named ENABLE_INSTALL_TOOL in the /typo3con subdirectory
 * 
 * @author Matthias Elbert <matthias.elbert@netlogix.de>
 * @package		TYPO3
 * @subpackage	tx_caretakerinstance
 */
class tx_nxcaretakerservices_Operation_BELogin implements tx_caretakerinstance_IOperation {

		
	/**
	 *	  
	 * @return TRUE, if no ENABLE_INSTALL_TOOL was found, or FALSE, if it was found
	 */
	public function execute($parameter = array()) {										
					
		
//		$BE_USER = t3lib_div::makeInstance('t3lib_beUserAuth');	// New backend user object
//		$BE_USER = new t3lib_beUserAuth();
//		$BE_USER->warningEmail = $TYPO3_CONF_VARS['BE']['warning_email_addr'];
//		$BE_USER->lockIP = $TYPO3_CONF_VARS['BE']['lockIP'];
//		$BE_USER->auth_timeout_field = intval($TYPO3_CONF_VARS['BE']['sessionTimeout']);
//		$BE_USER->OS = TYPO3_OS;
//		$BE_USER->start();			// Object is initialized
//		$BE_USER->checkCLIuser();
//		$BE_USER->backendCheckLogin();	// Checking if there's a user logged in
//		$BE_USER->trackBeUser($TYPO3_CONF_VARS['BE']['trackBeUser']);	// Tracking backend user script hits
//		
		
		

		if (!file_exists($filename)) {
    		return new tx_caretakerinstance_OperationResult(TRUE, $filename . ' file doesnt exist!');
		} else {
    		return new tx_caretakerinstance_OperationResult(FALSE, $filename . ' file exists!');
		}	
	}
}
?>