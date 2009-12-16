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
class tx_nxcaretakerservices_Operation_ClearCacheAction implements tx_caretakerinstance_IOperation {

		
	/**
	 *	  
	 * @return TRUE, if no ENABLE_INSTALL_TOOL was found, or FALSE, if it was found
	 */
	public function execute($parameter = array()) {										
					
		$action = $parameter['action'];
			
		$this->includeTCA();
			
		require_once (PATH_t3lib."class.t3lib_userauthgroup.php");
		$GLOBALS['BE_USER'] = t3lib_div::makeInstance('t3lib_beUserAuth');
		$GLOBALS['BE_USER']->user['admin'] = true;
		
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');							
		$tce->stripslashes_values = 0;		
		$tce->start(array(),array());	

		if($action == 'all')
		{
			$tce->clear_cacheCmd('all');
						
			return new tx_caretakerinstance_OperationResult(TRUE, 'cleared all caches.');			
		}
		
		if($action == 'page')
		{
			$tce->clear_cacheCmd('pages');
						
			return new tx_caretakerinstance_OperationResult(TRUE, 'cleared pages cache.');	
		}		
		if($action == 'conf')
		{						
			$tce->clear_cacheCmd('temp_CACHED');
						
			return new tx_caretakerinstance_OperationResult(TRUE, 'cleared temp_CACHED cache.');	
		}
	}
	
	protected function includeTCA() {
		require_once(PATH_tslib.'class.tslib_fe.php');
		
		require_once(PATH_t3lib.'class.t3lib_cs.php');
		require_once(PATH_t3lib.'class.t3lib_userauth.php');
		require_once(PATH_tslib.'class.tslib_feuserauth.php');
		

			// Make new instance of TSFE object for initializing user:
		$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
		$TSFE = new $temp_TSFEclassName($TYPO3_CONF_VARS,0,0);
		$TSFE->includeTCA();
	}
}
?>