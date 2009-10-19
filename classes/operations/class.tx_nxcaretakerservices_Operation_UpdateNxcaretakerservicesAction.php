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
class tx_nxcaretakerservices_Operation_UpdateNxcaretakerservicesAction implements tx_caretakerinstance_IOperation {

		
	/**
	 *	  
	 * @return TRUE, if no ENABLE_INSTALL_TOOL was found, or FALSE, if it was found
	 */
	public function execute($parameter = array()) {										
					
		$serverVersion = $parameter['version'];			

		$clientVersion = json_encode($EM_CONF['nxcaretakerservices']['version']);
		
		if($serverVersion == $clientVersion) return new tx_caretakerinstance_OperationResult(TRUE, 'ClientVersion is equal to the serverVersion.');
		
		$dirname = PATH_site . 'typo3conf/ext/nxcaretakerservices/';
		if(!is_dir($dirname)) return new tx_caretakerinstance_OperationResult(FALSE, $dirname .' not found.');			

		$svnCommand = 'svn';
		$params = array('co','https://svn.typo3.org/TYPO3v4/Extensions/nxcaretakerservices/trunk', $dirname);
		$result = exec($svnCommand, $params);
		
		return new tx_caretakerinstance_OperationResult(TRUE, $result);
	}
}
?>