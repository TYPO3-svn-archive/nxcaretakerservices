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
class tx_nxcaretakerservices_Operation_InstallToolAction implements tx_caretakerinstance_IOperation {

		
	/**
	 *	  
	 * @return TRUE, if no ENABLE_INSTALL_TOOL was found, or FALSE, if it was found
	 */
	public function execute($parameter = array()) {										
					
		$action = $parameter['action'];
		
		$filename = PATH_site . 'typo3conf/ENABLE_INSTALL_TOOL';

		if($action == 'create')
		{
			if(file_exists($filename))
			{
				return new tx_caretakerinstance_OperationResult(FALSE, $filename . ' file already exists!');
			}
			else{								
				$ourFileHandle = fopen($filename, 'w');
				fclose($ourFileHandle);				
				
				if(!file_exists($filename))
				{
					return new tx_caretakerinstance_OperationResult(FALSE, $filename . ' file creation failed!');
				}
				else
				{
					return new tx_caretakerinstance_OperationResult(TRUE, $filename . ' file creation successful!');
				}
			}
		}
		
		if($action == 'delete')
		{
			if(file_exists($filename))
			{				
				unlink($filename);
				
				if(file_exists($filename))
				{
					return new tx_caretakerinstance_OperationResult(FALSE, $filename . ' file deletion failed!');
				}
				else
				{
					return new tx_caretakerinstance_OperationResult(TRUE, $filename . ' file deletion successful!');
				}
			}
			else{
				return new tx_caretakerinstance_OperationResult(FALSE, $filename . ' file already is deleted!');
			}
		}	

		require_once(PATH_t3lib.'class.t3lib_install.php');
				
		if($action == 'reset')
		{						
			$password = $parameter['password'];
			if($password) {					
				$instObj = new t3lib_install;
				$instObj->allowUpdateLocalConf =1;
				$instObj->updateIdentity = 'nxcaretakerservices->InstallToolAction';				
				
				$lines = $instObj->writeToLocalconf_control();
				$instObj->setValueInLocalconfFile($lines, '$TYPO3_CONF_VARS[\'BE\'][\'installToolPassword\']', $password);		
				$instObj->writeToLocalconf_control($lines);
				
				return new tx_caretakerinstance_OperationResult(TRUE, 'Password reset.');
			}		
			else return new tx_caretakerinstance_OperationResult(FALSE, 'no password.');
		}
	}
}
?>