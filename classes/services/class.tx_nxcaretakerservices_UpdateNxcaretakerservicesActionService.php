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

class tx_nxcaretakerservices_UpdateNxcaretakerservicesActionService extends tx_caretakerinstance_RemoteTestServiceBase{
	
	public function runTest() {		
		
		$operation = array('GetExtensionVersion', array('extensionKey' => 'nxcaretakerservices'));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);

		if (!$this->isCommandResultSuccessful($commandResult)) {
			return $this->getFailedCommandResultTestResult($commandResult);
		}
		
		$results = $commandResult->getOperationResults();
		$operationResult = $results[0];
		if ($operationResult->isSuccessful()) {
			$extensionVersion = $operationResult->getValue();
		} else {
			$extensionVersion = FALSE;
		}

		$testResult = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, 0, 'Clientversion: ' . $extensionVersion);

		return $testResult;
	}	
	
	public function versionUpdate() {

		$operation = array('UpdateNxcaretakerservicesAction', array('version' => json_encode($EM_CONF['nxcaretakerservices']['version'])));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		if (!$this->isCommandResultSuccessful($commandResult)) {
			return 'error '. $commandResult->getMessage();
		}

		$results = $commandResult->getOperationResults();
		$operationResult = $results[0];		
 		
		$message = $operationResult->getValue();
		
		return $message;
	}
	
	public function doAction($method)
	{
		$Result="";
		
		switch ( $method ){
				case "update":
					$Result=$this->versionUpdate();
				break;				
				default:
					$Result = "none";
				break;
			}
		return $Result;
	}
		
	public function getView($service, $actionId) {
		
		$message ='[{text:"Update",handler:
							function (){								
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									Ext.MessageBox.alert("Status", response.responseText);										       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'",
               							method: "update"               							             							
            								}
        							});
    						}
	}]';
		
		$jsonData = 'new Ext.Panel({
        									id              : "node-added-action",
        									html            : "Actions:",
        									autoHeight      : true   ,
											bbar 			: [
											{			
											text	:	"refresh",
											icon    : 	"../res/icons/arrow_refresh_small.png",
											handler	:	function (){
         										var node_info_panel = Ext.getCmp("node-info-action");
        										node_info_panel.load( tx.caretaker.back_path + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + tx.caretaker.node_info.id + "&action='.$actionId.'");
        										}
											}, "-" , '.$message .'
											]
    									})';
		return $jsonData;
		
		
	}
}

?>