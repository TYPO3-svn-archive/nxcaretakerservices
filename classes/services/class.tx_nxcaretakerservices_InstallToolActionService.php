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

class tx_nxcaretakerservices_InstallToolActionService extends tx_caretakerinstance_RemoteTestServiceBase{
	
	public function runTest() {		
		
		$operation = array('GetInstallTool', array());
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
	
	public function delete() {
		
		$operation = array('InstallToolAction', array('action' => 'delete'));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		if (!$this->isCommandResultSuccessful($commandResult)) {
			// error!
		}

		$results = $commandResult->getOperationResults();
		$operationResult = $results[0];		
 		
		$message = $operationResult->getValue();
		
		return $message;
	}
	
	public function create() {

		$operation = array('InstallToolAction', array('action' => 'create'));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		if (!$this->isCommandResultSuccessful($commandResult)) {
			// error!
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
				case "delete":
					$Result=$this->delete();
				break;
				case "create":
					$Result=$this->create();
				break;
				default:
					$Result = "none";
				break;
			}
		return $Result;
	}
		
	public function getView($service, $actionId) {
		
		$message ='[{text:"disable",
						icon    : 	tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'gfx/garbage.gif', '', 1).'",
						handler:
							function (){

								var node_info_panel = Ext.getCmp("node-added-action");								
								node_info_panel.removeAll()	;
								node_info_panel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								node_info_panel.doLayout();
							
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									Ext.MessageBox.alert("Status", response.responseText);
      									
        								node_info_panel.load( tx.caretaker.back_path + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + tx.caretaker.node_info.id + "&action='.$actionId.'");        																					       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'",
               							method: "delete"               							             							
            								}
        							});
    						}
					},{text:"enable",
						icon    : "../res/icons/test.png",
						handler:
							function (){

								var node_info_panel = Ext.getCmp("node-added-action");								
								node_info_panel.removeAll()	;
								node_info_panel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								node_info_panel.doLayout();
							
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									Ext.MessageBox.alert("Status", response.responseText);
           							    
        								node_info_panel.load( tx.caretaker.back_path + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + tx.caretaker.node_info.id + "&action='.$actionId.'");        																											       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'",
               							method: "create"               							             							
            								}
        							});
    						}
	}]';
		
		$jsonData = 'new Ext.Panel({
        									id              : "node-added-action",        									
        									autoHeight      : true   ,
											title 	:	"Install Tool:",																		
											autoLoad 	: tx.caretaker.back_path + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + tx.caretaker.node_info.id + "&action='.$actionId.'",
        									bbar 			: [
											 '.$message .'
												]
    									})';
		return $jsonData;
		
		
	}
}

?>