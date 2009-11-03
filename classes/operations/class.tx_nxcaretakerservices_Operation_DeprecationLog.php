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

/**
 * A simple look after the existence of a file named ENABLE_INSTALL_TOOL in the /typo3con subdirectory
 * 
 * @author Matthias Elbert <matthias.elbert@netlogix.de>
 * @package		TYPO3
 * @subpackage	tx_caretakerinstance
 */
class tx_nxcaretakerservices_Operation_DeprecationLog implements tx_caretakerinstance_IOperation {

		
	/**
	 *	  
	 * @return TRUE, if no ENABLE_INSTALL_TOOL was found, or FALSE, if it was found
	 */
	public function execute($parameter = array()) {										

		global $TYPO3_CONF_VARS;
		
		$action = $parameter['action'];
		
		if($action == 'get')
		{
			if($GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] && $GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] == '1') return new tx_caretakerinstance_OperationResult(FALSE, 'Deprecation Log enabled');
			else return  new tx_caretakerinstance_OperationResult(TRUE, 'Deprecation Log disabled');
		}
		
		require_once(PATH_t3lib.'class.t3lib_install.php');
				
		if($action == 'enable')
		{
			if($GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] && $GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] == '1')
			{
				return new tx_caretakerinstance_OperationResult(FALSE, 'Deprecation Log already enabled!');
			}
			else{								
				$instObj = new t3lib_install;
				$instObj->allowUpdateLocalConf =1;
				$instObj->updateIdentity = 'nxcaretakerservices->DeprecationLogAction';				
				
				$lines = $instObj->writeToLocalconf_control();
				$instObj->setValueInLocalconfFile($lines, '$TYPO3_CONF_VARS[\'SYS\'][\'enableDeprecationLog\']', '1');		
				$instObj->writeToLocalconf_control($lines);
				
				return new tx_caretakerinstance_OperationResult(TRUE, 'Deprecation Log enabled.');
				
			}
		}
		
		if($action == 'disable')
		{
			if($GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] && $GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] == '1')
			{				
				$instObj = new t3lib_install;
				$instObj->allowUpdateLocalConf =1;
				$instObj->updateIdentity = 'nxcaretakerservices->DeprecationLogAction';
				
				$lines = $instObj->writeToLocalconf_control();
				$instObj->setValueInLocalconfFile($lines, '$TYPO3_CONF_VARS[\'SYS\'][\'enableDeprecationLog\']', '0');		
				$instObj->writeToLocalconf_control($lines);
				
				return new tx_caretakerinstance_OperationResult(TRUE, 'Deprecation Log disabled.');
				
			}
			else{
				return new tx_caretakerinstance_OperationResult(FALSE, 'Deprecation Log already disabled!');
			}
		}			
		
		if($action == 'show')
		{
			$retval = array();
			$links=	glob(PATH_typo3conf . 'deprecation_*.log');
			foreach ($links as &$filename) {
//				$filename = t3lib_div::locationHeaderUrl(substr($filename, strlen(PATH_site)));
				$retval[t3lib_div::locationHeaderUrl(substr($filename, strlen(PATH_site)))] = filemtime($filename);
			}
			if($retval) return new tx_caretakerinstance_OperationResult(TRUE, $retval);
			else return new tx_caretakerinstance_OperationResult(False, 'No logs found yet.');
		}
	}
}
?>