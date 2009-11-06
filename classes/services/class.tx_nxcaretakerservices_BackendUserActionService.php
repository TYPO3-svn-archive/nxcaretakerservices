<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Christopher Hlubek <hlubek@networkteam.com>
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


class tx_nxcaretakerservices_BackendUserActionService extends tx_caretakerinstance_RemoteTestServiceBase{
	
	public function runTest() {
		/*
		$blacklistedUsernames = explode(chr(10), $this->getConfigValue('blacklist'));

		$operations = array();
		foreach ($blacklistedUsernames as $username) {
			$operations[] = array('GetRecord', array('table' => 'be_users', 'field' => 'username', 'value' => $username, 'checkEnableFields' => TRUE));
		}

		$commandResult = $this->executeRemoteOperations($operations);

		if (!$this->isCommandResultSuccessful($commandResult)) {
			return $this->getFailedCommandResultTestResult($commandResult);
		}

		$usernames = array();
		
		$results = $commandResult->getOperationResults();
		foreach ($results as $operationResult) {
			if ($operationResult->isSuccessful()) {
				$user = $operationResult->getValue();
				if ($user !== FALSE) {
					$usernames[] = $user['username'];
				}
			} else {
				return $this->getFailedOperationResultTestResult($operationResult);
			}
		}

		foreach ($blacklistedUsernames as $username) {
			if (in_array($username, $usernames)) {
				return tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, 0, 'User [' . $username . '] is blacklisted and should not be active.');
			}
		}*/

		return tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, 0, '');
	}
	
	public function Action($action,$ids, $params)
	{
		$operation = array('GetBeusers', array('action' => $action,'ids' => $ids, 'params'=>$params));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		
		$results = $commandResult->getOperationResults();
		
		$operationResult = $results[0]->getValue();
		
		
		return $operationResult;
	}
	
	public function doAction($params, &$ajaxObj)
	{
		$method = t3lib_div::_GP('method');
		$Result="";
		
		if(substr($method, 0, 7) == 'getUser') 
		{			
			$Result = $this->getUser($params, $ajaxObj);
		}
		if(substr($method, 0, 6) == 'Enable') 
		{			
			$Result = $this->Action('enable',substr($method, 7));
		}
		if(substr($method, 0, 7) == 'Disable') 
		{			
			$Result = $this->Action('disable',substr($method, 8));
		}
		if(substr($method, 0, 5) == 'Admin') 
		{			
			$Result = $this->Action('enableAdmin',substr($method, 6));
		}
		if(substr($method, 0, 7) == 'NoAdmin') 
		{			
			$Result = $this->Action('disableAdmin',substr($method, 8));
		}
		if(substr($method, 0, 6) == 'Delete') 
		{			
			$Result = $this->Action('delete',substr($method, 7));
		}
		if(substr($method, 0, 3) == 'Add') 
		{			
			$addusername = t3lib_div::_GP('addusername');
			$addpassword = t3lib_div::_GP('addpassword');
			$addname = t3lib_div::_GP('addname');
			$addemail = t3lib_div::_GP('addemail');
			$Result  = $this->Action('add','',array('addusername'=>$addusername,'addpassword'=>$addpassword,'addname'=>$addname,'addemail'=>$addemail));
		}
		if(substr($method, 0, 5) == 'reset') 
		{						
			$password = t3lib_div::_GP('password');			
			$Result  = $this->Action('reset',substr($method, 6),$password);
		}
		
		return $Result;
	}
		
	public function getUser($params, &$ajaxObj) {
		
		$node_id = t3lib_div::_GP('node');
		$back_path = t3lib_div::_GP('back_path');
		$service = t3lib_div::_GP('service');
		$actionId = t3lib_div::_GP('actionid');
		
		$operation = array('GetBeusers', array());
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);

		$message = array();
		
		$results = $commandResult->getOperationResults();
		
		if (!$this->isCommandResultSuccessful($commandResult)) {
			return 'error '. $commandResult->getMessage();
		}
		
		$operationResult = $results[0]->getValue();
		
		$total=0;
		foreach($operationResult as $row){			
			$message[] = $row;
			$total++;
		}
				
		$retval = array('total' => $total, 'users' => $message);
		$ajaxObj->setContent($retval);
        $ajaxObj->setContentFormat('jsonbody');
	}
	
	public function getView($params, &$ajaxObj) {
		
	}
	
}



?>