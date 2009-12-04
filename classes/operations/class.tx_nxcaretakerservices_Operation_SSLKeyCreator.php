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
require_once(t3lib_extMgm::extPath('caretaker_instance', 'classes/class.tx_caretakerinstance_OpenSSLCryptoManager.php'));

/**
 * A simple look after the existence of a file named ENABLE_INSTALL_TOOL in the /typo3con subdirectory
 * 
 * @author Matthias Elbert <matthias.elbert@netlogix.de>
 * @package		TYPO3
 * @subpackage	tx_caretakerinstance
 */
class tx_nxcaretakerservices_Operation_SSLKeyCreator implements tx_caretakerinstance_IOperation {

		
	/**
	 *	  
	 * @return TRUE, if no ENABLE_INSTALL_TOOL was found, or FALSE, if it was found
	 */
	public function execute($parameter = array()) {										

		$action = $parameter['action'];
		
		if($action == 'create'){
				
			$cryptoManager = new tx_caretakerinstance_OpenSSLCryptoManager();
			
			$keyPair = $cryptoManager->generateKeyPair();
	
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker_instance']);
			
			$extConf['crypto.']['instance.']['publicKey'] = $keyPair[0];
			$extConf['crypto.']['instance.']['privateKey'] = $keyPair[1];
					
			
			require_once(PATH_t3lib.'class.t3lib_install.php');
			
			$instObj = new t3lib_install;
					$instObj->allowUpdateLocalConf =1;
					$instObj->updateIdentity = 'nxcaretakerservices->SSLKeyCreator';				
					
					$lines = $instObj->writeToLocalconf_control();
					$instObj->setValueInLocalconfFile($lines, '$TYPO3_CONF_VARS[\'EXT\'][\'extConf\'][\'caretaker_instance\']', serialize($extConf));		
					$instObj->writeToLocalconf_control($lines);
			if( $keyPair[0])  	return new tx_caretakerinstance_OperationResult(TRUE, $keyPair[0]);
			else   	return new tx_caretakerinstance_OperationResult(FALSE, 'key generation failed');
		}
		
		else
		{
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker_instance']);
			
			$pub = $extConf['crypto.']['instance.']['publicKey'];
			if($pub == '-----BEGIN PUBLIC KEY-----|MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCw06+ma8tPh0tLr6zuMgyV+7oJ|i8sZpGDyxCnNRTNezbx4NV0ZkaPUfxikzFGZk9KKga2JRFlYwT3BrSBYeo32q/yN|XgZ4r5LOkYdOgdHi52A0J/Tk35XN+pQM4nR+DQM47r4GEFd2M5E/2fdwV+U1PDM8|4Vy7+zvpdw11Q3vWdwIDAQAB|-----END PUBLIC KEY-----')
			{
				return new tx_caretakerinstance_OperationResult(FALSE, 'blacklistet key found!');
			}
			else return new tx_caretakerinstance_OperationResult(TRUE, 'no blacklistet key');
		
		}
	}
}
?>