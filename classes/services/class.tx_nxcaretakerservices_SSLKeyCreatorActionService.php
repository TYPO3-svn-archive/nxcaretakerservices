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
		
		$blacklisted = explode(chr(10), $this->getConfigValue('blacklist'));
		
		$operation =  array('SSLKeyCreator', array('blacklistet' => $blacklisted));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		if (!$this->isCommandResultSuccessful($commandResult)) {
			return $this->getFailedCommandResultTestResult($commandResult);
		}

		$results = $commandResult->getOperationResults();
		$operationResult = $results[0];		
 		
		$message = $operationResult->getValue();
		
		if (!$operationResult->isSuccessful()) {	
			
			return tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, 0, $message);
		}

		$testResult = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, 0, $message);

		return $testResult;
		
	}	
	
	public function action($action, $password='') {

		$operation = array('SSLKeyCreator', array('action' => 'create' ));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		if (!$this->isCommandResultSuccessful($commandResult)) {
			// error!
		}

		$results = $commandResult->getOperationResults();
		$operationResult = $results[0];		
 		
		$message = $operationResult->getValue();

		if($operationResult->getValue() == 'key generation failed') return 'key generation failed';
		
		$node_id = t3lib_div::_GP('node');
				
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		if ($node_id && $node = $node_repository->id2node( $node_id , true) ){
			$currentInstance = $node->getInstance();
			if($currentInstance) {
				$uid = $currentInstance->getUid();
				
				$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_caretaker_instance', 'uid = '.(int)$uid, array('public_key' => $message));
	
			}
		}
		
		if($res) return 'Successful!';
		else return 'Error!';
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