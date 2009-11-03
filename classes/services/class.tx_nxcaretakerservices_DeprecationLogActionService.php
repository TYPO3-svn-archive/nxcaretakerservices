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

class tx_nxcaretakerservices_DeprecationLogActionService extends tx_caretakerinstance_RemoteTestServiceBase{
	
	public function runTest() {		
		
		$operation = array('DeprecationLog', array('action' => 'get'));
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
	
	public function changeLocalConf($action) {

		$operation = array('DeprecationLog', array('action' => $action));
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
	
	public function doAction($params, &$ajaxObj)
	{
		$method = t3lib_div::_GP('method');
		$Result="";
		
		switch ( $method ){
				case "disable":
					$Result=$this->changeLocalConf('disable');
				break;
				case "enable":
					$Result=$this->changeLocalConf('enable');
				break;
				case "show":
					$retval=$this->changeLocalConf('show');
					
					if(is_array($retval))
					{
						asort($retval);
						foreach($retval as $filename=>$date){
							$Result .= '<DIV><a href='.$filename.' target="_blank">'.$filename. ' ('. date('r', $date) .')</a> </DIV>';
						}
					}
					else $Result = $retval;
				break;
				default:
					$Result = "none";
				break;
			}
		return $Result;
	}
		
	public function getView($params, &$ajaxObj) {
		
		$node_id = t3lib_div::_GP('node');
		$back_path = t3lib_div::_GP('back_path');
		$service = t3lib_div::_GP('service');
		$actionId = t3lib_div::_GP('actionid');
		
		$message ='[{text:"disable",
						icon    : 	"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/garbage.gif', '', 1).'",
						handler:
							function (){

								var node_info_panel = Ext.getCmp("node-added-action");								
								node_info_panel.removeAll()	;
								node_info_panel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								node_info_panel.doLayout();
							
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      									Ext.MessageBox.alert("Status", response.responseText);
      									
        								node_info_panel.load( "'.$back_path.'" + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + "'.$node_id.'" + "&actionid='.$actionId.'");        																					       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'",
               							method: "disable"               							             							
            								}
        							});
    						}
					},{text:"enable",
						icon    : "../res/icons/test.png",
						handler:
							function (){

								var node_info_panel = Ext.getCmp("node-added-action");								
								node_info_panel.removeAll()	;
								node_info_panel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								node_info_panel.doLayout();
							
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      									Ext.MessageBox.alert("Status", response.responseText);
           							    
        								node_info_panel.load( "'.$back_path.'" + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node='.$node_id.'&actionid='.$actionId.'");        																											       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'",
               							method: "enable"               							             							
            								}
        							});
    						}
	}]';
		
		$jsonData = 'new Ext.Panel({
								items: [
											new Ext.Panel({										
        									id              : "node-added-action",        									
        									//autoHeight      : true   ,
											title 	:	"Deprecation Log:",																		
											autoLoad 	: "'.$back_path.'" + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node='.$node_id.'&actionid='.$actionId.'",
        									bbar 			: [
											 '.$message .'
												]
    									}),
    									new Ext.Panel({							
        									//autoHeight      : true   ,
											title 	:	"Links:",																		
											autoLoad 	: "'.$back_path.'" + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node='.$node_id.'&actionid='.$actionId.'&service='.$service.'&method=show"
        								})
        						]	
    						})
    									';
		return $jsonData;
		
		
	}
}

?>