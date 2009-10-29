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
		
		$operation = array('UpdateNxcaretakerservicesAction', array('info'=>'true'));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		if (!$this->isCommandResultSuccessful($commandResult)) {
			return 'error '. $commandResult->getMessage();
		}

		$results = $commandResult->getOperationResults();
		$operationResult = $results[0];		
 		
		$message = $operationResult->getValue();
		
		$testResult = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, 0,  $message);

		return $testResult;
	}	
	
	public function versionUpdate($data = false) {

	
		$rev='';
		$rep='';
		if($data){
			$data = split(',', $data);
			$rev = $data[0];
			$rep = $data[1];			
		}
		
		$operation = array('UpdateNxcaretakerservicesAction', array('rev'=>$rev,'rep'=>$rep));
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
	
	public function doAction($params, &$ajaxObj)
	{
		$method = t3lib_div::_GP('method');
		$Result="";
		
		switch ( $method ){
				case "update":
					$Result=$this->versionUpdate();
				break;				
				default:
					if($method == ',') $Result=$this->versionUpdate();
					else $Result=$this->versionUpdate($method);
				break;
			}
		return $Result;
	}
		
	public function getView($params, &$ajaxObj) {
		
		$node_id = t3lib_div::_GP('node');
		$back_path = t3lib_div::_GP('back_path');
		$service = t3lib_div::_GP('service');
		$actionId = t3lib_div::_GP('actionid');
		
		$message ='[{text:"Update head revision",
						icon    : "../res/icons/arrow_refresh.png",
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
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'",
               							method: "update"               							             							
            								}
        							});
    						}
						},
						{
						text:"Update from/to",
						icon    : "../res/icons/arrow_refresh.png",
						handler:
							function (){								
        						
								var revisionId = Ext.id();
            					var repositoryId = Ext.id();
								var win = new Ext.Window({						                
						                layout:"fit",
						                closeAction:"hide",						                
						                width: 450,
						                height:150,						                
						                plain: true,
						                modal: true,
										title: "Choose revision and repository",
						                items: new Ext.FormPanel({
										        labelWidth: 100,											        								        
										        frame:true,										        
										        bodyStyle:"padding:5px 5px 0",										        
										        defaults: {width: 300},
										        defaultType: "textfield",										
										        items: [{
										                fieldLabel: "Revision",
										                id: revisionId,
										                name: "revision"
										            },{
										                fieldLabel: "Repository",
										                name: "repository",
										                id: repositoryId,
										                vtype:"url"
										            }
										        ],
						
						                buttons: [{
						                    text:"Ok",
						                    handler: function(){
						                    	var rev = Ext.getCmp(revisionId);
						                    	var rep = Ext.getCmp(repositoryId);
						                    							                    							                    	
						                    	if( rep.isValid()) {
						                    		win.hide();
						                    		
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
					               							back_path : "'.$back_path.'",
					               							node:   "'.$node_id.'",
					               							service:   "'.$service.'",
					               							method: rev.getRawValue()+","+rep.getRawValue()               							             							
					            								}
					        							});
					        							
												}
						                    }
						                },{
						                    text: "Cancel",
						                    handler: function(){
						                        win.hide();
						                    }
						                }]
										        
										    })
						            });
						        
						        win.show(this);
							
    						}
	}]';
		
		$jsonData = 'new Ext.Panel({
        									id              : "node-added-action",
        									title            : "SVN - Update of the client nxcaretakerservices:",
        									autoHeight      : true   ,
        									autoLoad 	: "'.$back_path.'" + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + "'.$node_id.'" + "&actionid='.$actionId.'",
											bbar 			: [
											 '.$message .'
											]
    									})';
		return $jsonData;
		
		
	}
}

?>