<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Matthias Elbert <matthias.elbert@netlogix.de>
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

require_once(t3lib_extMgm::extPath('caretaker_instance', 'services/class.tx_caretakerinstance_RemoteTestServiceBase.php'));
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/repositories/class.tx_caretaker_NodeRepository.php');

class tx_nxcaretakerservices_SSLKeyCreatorActionService extends tx_caretakerinstance_RemoteTestServiceBase{
	
	public function runTest() {		
		
//		$operation = array('GetInstallTool', array());
//		$operations = array($operation);
//
//		$commandResult = $this->executeRemoteOperations($operations);
//		if (!$this->isCommandResultSuccessful($commandResult)) {
//			return $this->getFailedCommandResultTestResult($commandResult);
//		}
//
//		$results = $commandResult->getOperationResults();
//		$operationResult = $results[0];		
// 		
//		$message = $operationResult->getValue();
//		
//		if (!$operationResult->isSuccessful()) {	
//			
//			return tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, 0, $message);
//		}
//
//		$testResult = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, 0, $message);
//
//		return $testResult;
		return tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, 0, 'nothing');
	}	
	
	public function action($action, $password='') {

//		$operation = array('SSLKeyCreator', array());
//		$operations = array($operation);
//
//		$commandResult = $this->executeRemoteOperations($operations);
//		if (!$this->isCommandResultSuccessful($commandResult)) {
//			// error!
//		}
//
//		$results = $commandResult->getOperationResults();
//		$operationResult = $results[0];		
// 		
//		$message = $operationResult->getValue();
//				
//		$node_id = t3lib_div::_GP('node');
//				
//		$node_repository = tx_caretaker_NodeRepository::getInstance();
//		if ($node_id && $node = $node_repository->id2node( $node_id , true) ){
//			$currentInstance = $node->getInstance();
//			if($currentInstance) {
//				$uid = $currentInstance->getUid();
//				
//				$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_caretaker_instance', 'uid = '.(int)$uid, array('public_key' => $message));
//	
//			}
//		}
		
		
		$privKey = str_replace("|", "\n", "-----BEGIN RSA PRIVATE KEY-----|MIICXQIBAAKBgQCw06+ma8tPh0tLr6zuMgyV+7oJi8sZpGDyxCnNRTNezbx4NV0Z|kaPUfxikzFGZk9KKga2JRFlYwT3BrSBYeo32q/yNXgZ4r5LOkYdOgdHi52A0J/Tk|35XN+pQM4nR+DQM47r4GEFd2M5E/2fdwV+U1PDM84Vy7+zvpdw11Q3vWdwIDAQAB|AoGAYJvnVceDFvpWOw3KL4GMus0DgX+tAV970Gc4Z3wtatiA5jRRn0yg89JUxoUS|+BN5bk8XXu3G2uUJNq2+BFlBAeHLDs2gN1X4vPiGtaLNvqv8CVcOFnBOHubAuPRK|dsyI73v95+ZcfxsX8OVbAh+KSpqEJho7PypY3cXGPvaidzECQQDWRAtt5zscadxV|OqXf7aclWJnaooxPfpPP/Bi7Rfqw4/wF9w2IEPqHiTXBrwaJIcfRM5+eg+zFhWJm|AJ5FTCalAkEA00TSd3UZz53kr2HgsGU7IXVBI2LO26TlVjkuIR96DuS9S781BDCm|4yl0+QLSi8SvSfngONA/muT8L/q3pIFZ6wJAQE8P9x6NuUt0nAgMPReBMU5UbzCW|WE2vY59QdPTd9zWWMNwjrZEbAI8IGWfE2GfRJ1MNN3B1IhuUmvTYjAf9GQJBALpu|OOuBQk2bn3nEfWoraoqT1e9L+g6I7Hex7ar9A9CwuPpmuHoCFMLQipBSlUkRPz2g|auS3n+knuAL+058vJhECQQCb7QELENX68nv08Gs3yx+rDdElkNHxnysaJa2Arvl0|pcq4rEvSbj6+sh/x5z8i4Zn4oYernmn74xRs4ut2jQQb|-----END RSA PRIVATE KEY-----|");
		$pubKey = str_replace("|", "\n", "-----BEGIN PUBLIC KEY-----|MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCw06+ma8tPh0tLr6zuMgyV+7oJ|i8sZpGDyxCnNRTNezbx4NV0ZkaPUfxikzFGZk9KKga2JRFlYwT3BrSBYeo32q/yN|XgZ4r5LOkYdOgdHi52A0J/Tk35XN+pQM4nR+DQM47r4GEFd2M5E/2fdwV+U1PDM8|4Vy7+zvpdw11Q3vWdwIDAQAB|-----END PUBLIC KEY-----|");		
		
		$priv = openssl_pkey_get_private($privKey);
		$pub = openssl_pkey_get_public($pubKey);

//		openssl_public_encrypt("hallo welt", $encryptedData, $pub);
//
//		openssl_private_decrypt($encryptedData, $sensitiveData, $priv);

		openssl_open(base64_decode("LIrfr1bs2TK7amHRh/6bLcqLfrdHXaVTj4k9eYX0FdUoQq1qP58KwKcxCk0H0TyDXVgmXWIQPfmS/xgSyrXXICqh0IFHAdrAwakXxw=="), $decrypted, base64_decode("BfdmUzwtgv8PROkX7Fh9d7DsOaRlZ4f4lm8KaqiLaPUIGEfNu+/SnId2MCNIUPxNGnZVIVlfmMMtQ2s+/pMBXsHbq0llj/RogZYtc74AGCAfrqke+eRrpquYPtn+1Mef14QMPnsnzCnIVxOjnRiRkXqpNXtABgQ8agVyCBj/GuA="), $priv);
		
		return $decrypted;
	}
	
	public function doAction($params, &$ajaxObj)
	{
		$method = t3lib_div::_GP('method');
		$Result="";
		
		switch ( $method ){
				
				case "create":
					$Result=$this->action('create');
				break;
				
				default:
					$Result = "none";
				break;
			}
		return $Result;
	}
		
	public function getView($params, &$ajaxObj) {
		
		
		
		
	}
}

?>