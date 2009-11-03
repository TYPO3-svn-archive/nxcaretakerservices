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

class tx_nxcaretakerservices_ActionTestTestService extends tx_caretakerinstance_RemoteTestServiceBase{
	
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
//		$message =  'testaction (GetInstallTool)';
//		
//		if (!$operationResult->isSuccessful()) {	
//			
//			return tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, 0, $message);
//		}
		
		global $TYPO3_CONF_VARS;

		//$strippedExtensionList = $this->stripNonFrontendExtensions($newExtList);
		require_once(PATH_t3lib.'class.t3lib_install.php');
		// Instance of install tool
		$instObj = new t3lib_install;
		$instObj->allowUpdateLocalConf =1;
		$instObj->updateIdentity = 'nxcaretakerservices';

		// Get lines from localconf file
		$lines = $instObj->writeToLocalconf_control();
		$instObj->setValueInLocalconfFile($lines, '$TYPO3_CONF_VARS[\'TestLocalCofEditing\']', 'Testtext');		
		$instObj->writeToLocalconf_control($lines);
		
	//	$test = $this->evalPassword($test);
		
//		$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, array('username'=>'test', 'password'=>$test,'realName'=>'test','email'=>'test@netlogix.de') );
		
		
//		foreach($dataStruct['ROOT']['el'] as $key => $dsConf)
//		{
//			$result = $result . $dsConf['TCEforms']['config']['eval'] . ", \n";
//		}

//		$data = array('be_users' => array('NEWuser'=>array('username'=>'test', 'password'=>$test,'realName'=>'test','email'=>'test@netlogix.de')));
//		
//		$this->includeTCA();
//		
//		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
//		$GLOBALS['BE_USER'] = t3lib_div::makeInstance('t3lib_beUserAuth');
//		$GLOBALS['BE_USER']->user['uid'] = 9999;
//		$GLOBALS['BE_USER']->user['username'] = 'tempAdmin';
//		$GLOBALS['BE_USER']->user['admin'] = true;
//   	 	$tce->stripslashes_values = 0;
//
//    	$tce->start($data,array());
//		$tce->BE_USER = t3lib_div::makeInstance('t3lib_beUserAuth');
//		$tce->BE_USER->user['admin'] = 1;
//    	$tce->process_datamap();
//		
//		$testResult = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, 0,'');

		return $testResult;
	}	
	
	protected function includeTCA() {
		require_once(PATH_tslib.'class.tslib_fe.php');

			// require some additional stuff in TYPO3 4.1
		require_once(PATH_t3lib.'class.t3lib_cs.php');
		require_once(PATH_t3lib.'class.t3lib_userauth.php');
		require_once(PATH_tslib.'class.tslib_feuserauth.php');

			// Make new instance of TSFE object for initializing user:
		$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
		$TSFE = new $temp_TSFEclassName($TYPO3_CONF_VARS,0,0);
		$TSFE->includeTCA();
	}
	

	public function getValueDescription() {
		return 'testaction';
	}
}

?>