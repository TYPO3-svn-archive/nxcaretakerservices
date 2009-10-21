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

class tx_nxcaretakerservices_ExtensionManagerActionService extends tx_caretakerinstance_RemoteTestServiceBase{
	
	public function runTest() {
		
		return tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, 0, '');
	}
	
	public function Action($action,$ids)
	{
		$operation = array('ExtensionManagement', array('action' => $action,'ids' => $ids));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		
		$results = $commandResult->getOperationResults();
		
		$operationResult = $results[0]->getValue();
		
		
		return $operationResult;
	}
	
	public function doAction($method)
	{
		$Result="";
		
		if(substr($method, 0, 7) == 'svninfo') 
		{			
			$Result = $this->Action('svninfo',substr($method, 8));
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
			$Result  = $this->Action('add',substr($method, 4));
		}
		
		return $Result;
	}
		
	public function getView($service, $actionId) {
		
		$location_list = array('system','global','local');
		
		$operation = array('GetExtensionList', array('locations' => $location_list));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		if (!$this->isCommandResultSuccessful($commandResult)) {
			return $this->getFailedCommandResultTestResult($commandResult);
		}

		$results = $commandResult->getOperationResults();
		$operationResult = $results[0];

		if (!$operationResult->isSuccessful()) {
			return  'error: Remote operation failed: ' . $operationResult->getValue();
		} 

		$extensionList = $operationResult->getValue();

		$data ="[";
				
		foreach($extensionList as $extension){
			if($data != '[') $data = $data . ',';
			$data = $data . $this->getRows($extension);
		}
		$data = $data . ']';
		
		$grid = '
	
		   new Ext.grid.GridPanel({
        		id:"button-grid",
        		store: new Ext.data.Store({
            		reader: new Ext.data.ArrayReader({}, [
       					{name: "extKey"},	
            			{name: "version"},
       					{name: "installed"},
       					{name: "secure"},       					
       					{name: "scope"}       					
    					]),
            		data: 
            			'.$data.'
            		
        			}),
        		cm: new Ext.grid.ColumnModel([            			
    				{header: "Extensionkey", width: 7, sortable: true, dataIndex: "extKey"},
            		{header: "Version", width: 3, sortable: true, dataIndex: "version"},
            		{header: "is installed", width: 3, sortable: true, dataIndex: "installed"},
            		{header: "is secure", width: 3, sortable: true, dataIndex: "secure"},
            		{header: "Scope", width: 3, sortable: true, dataIndex: "scope"}
        			]),
        		sm: new Ext.grid.CheckboxSelectionModel({singleSelect:true}),
        		viewConfig: { forceFit:true },
        		columnLines: true,
        
        		buttons: [
        			{
            			text:"Refresh",
            			tooltip:"Reload all extensions",
            			icon    : 	"../res/icons/arrow_refresh_small.png"   ,
            			handler:	 function (){	

            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();	
								
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});
    						}         			            
        			}
        		],
        		buttonAlign:"center",        		
        		
		        tbar:[{
        		    	text:"SVN Info",
            			tooltip:"Gets SVN Info, if exists",
            			icon    : 	tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'gfx/button_unhide.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					var selection = grid.getSelectionModel().getSelected().get("extKey");
								            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
            					
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										alert(response.responseText);
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        																		
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});          																       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'",
               							method: "svninfo" + "," + selection           							             							
            								}
        							});
    							}
							}
        			},  {
        		    	text:"Disable",
            			tooltip:"Disable all selected users",
            			icon    : 	tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'gfx/button_hide.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					var selections = grid.getSelectionModel().getSelections();
								var ids = "";
								var count = selections.length;
								var i = 0;
            					while(i<count)
            					{
            						ids = ids + "," + selections[i].get("uid");
            						i++;
            					}
            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
								
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        																		
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});           																       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'",
               							method: "Disable" + ids           							             							
            								}
        							});
    							}
							}
        			},"-",{
        		    	text:"Enable Admin",
            			tooltip:"Enable Admin to all selected users",
            			icon    : 	tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'gfx/i/be_users_admin.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					var selections = grid.getSelectionModel().getSelections();
								var ids = "";
								var count = selections.length;
								var i = 0;
            					while(i<count)
            					{
            						ids = ids + "," + selections[i].get("uid");
            						i++;
            					}
            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
            					
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        																		
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});           																       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'",
               							method: "Admin" + ids           							             							
            								}
        							});
    							}
							}
        			},{
        		    	text:"Disable Admin",
            			tooltip:"Disable Admin to all selected users",
            			icon    : 	tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'gfx/i/be_users.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					var selections = grid.getSelectionModel().getSelections();
								var ids = "";
								var count = selections.length;
								var i = 0;
            					while(i<count)
            					{
            						ids = ids + "," + selections[i].get("uid");
            						i++;
            					}
            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
            					
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});           																       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'",
               							method: "NoAdmin" + ids           							             							
            								}
        							});
    							}
							}
        			},"-",{
            			text:"Delete",
            			tooltip:"Delete the selected users",
            			icon    : 	tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'gfx/garbage.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					var selections = grid.getSelectionModel().getSelections();
								var ids = "";
								var count = selections.length;
								var i = 0;
            					while(i<count)
            					{
            						ids = ids + "," + selections[i].get("uid");
            						i++;
            					}
            					Ext.MessageBox.confirm("Confirm", "Are you sure you want to do that?", function(btn)
            					{
            					if(btn == "yes"){
            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
            					
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																		
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        																		
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});           																       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$service.'",
               							method: "Delete" + ids           							             							
            								}
        							});
        							}});
    							}
							}		            
        			},{
            			text:"Add User",
            			tooltip:"Add new user",
            			icon    : 	tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'gfx/new_el.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					
            						Ext.apply(Ext.form.VTypes, {									    
									    password : function(val, field) {
									        if (field.initialPassField) {
									            var pwd = Ext.getCmp(field.initialPassField);
									            return (val == pwd.getValue());
									        }
									        return true;
									    },
									
									    passwordText : "Passwords do not match"
									});
									            					
            						var userId = Ext.id();
            						var passId = Ext.id();
            						var pass2Id = Ext.id();
            						var nameId = Ext.id();
            						var emailId = Ext.id();
            						
            					 	var win = new Ext.Window({						                
						                layout:"fit",
						                closeAction:"hide",						                
						                width: 375,
						                height:250,						                
						                plain: true,
						                modal: true,
										title: "Fill in user data",
						                items: new Ext.FormPanel({
										        labelWidth: 100,											        								        
										        frame:true,										        
										        bodyStyle:"padding:5px 5px 0",										        
										        defaults: {width: 230},
										        defaultType: "textfield",										
										        items: [{
										                fieldLabel: "Username",
										                id: userId,
										                name: "username",
										                allowBlank:false
										            },{
												        fieldLabel: "Password",
												        inputType:"password",
												        name: "pass",
												        id: passId,
												        allowBlank:false
												    },{
												        fieldLabel: "Confirm Password",
												        name: "pass-cfrm",
												        id: pass2Id,
												        inputType:"password",
												        vtype: "password",
												        initialPassField: passId,
												        allowBlank:false
												    },{
										                fieldLabel: "Name",
										                id: nameId,
										                name: "name"
										            },{
										                fieldLabel: "Email",
										                name: "email",
										                id: emailId,
										                vtype:"email"
										            }
										        ],
						
						                buttons: [{
						                    text:"Submit",
						                    handler: function(){
						                    	var username = Ext.getCmp(userId);
						                    	var password = Ext.getCmp(passId);
						                    	var password2 = Ext.getCmp(pass2Id);						                    	
						                    	var name = Ext.getCmp(nameId);
						                    	var email = Ext.getCmp(emailId);
						                    							                    	
						                    	if(username.isValid() && email.isValid() && password.isValid() && password2.isValid()) {
						                    		win.hide();

						                    		var viewpanel = Ext.getCmp("nxcaretakerAction");
													viewpanel.removeAll()	;
													viewpanel.add({	html : "<img src="+tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
													viewpanel.doLayout();
						                    		
					        						Ext.Ajax.request({
					           							url: tx.caretaker.back_path + "ajax.php",
					           							success : function (response, opts){											
					      										
																						
					        						Ext.Ajax.request({
					           							url: tx.caretaker.back_path + "ajax.php",
					           							success : function (response, opts){											
					      									           								
					        								var jsonData = Ext.util.JSON.decode(response.responseText);
					        																							
															viewpanel.removeAll();
															viewpanel.add(jsonData);
															viewpanel.doLayout(); 	
																				       								       								
					    									}     , 
					           							params: { 
					               							ajaxID: "tx_nxcaretakerservices::doaction",
					               							node:   tx.caretaker.node_info.id,
					               							service:   "'.$service.'" ,
					               							actionid:      "'.$actionId.'"        							               							             							
					            								}
					        							});           																       								       								
					    									}     , 
					           							params: { 
					               							ajaxID: "tx_nxcaretakerservices::doaction",
					               							node:   tx.caretaker.node_info.id,
					               							service:   "'.$service.'",
					               							method: "Add,"+ username.getRawValue() +","+password.getRawValue() +","+ name.getRawValue() +","+ email.getRawValue()           							             							
					            								}
					        							});
					        							
												}
						                    }
						                },{
						                    text: "Close",
						                    handler: function(){
						                        win.hide();
						                    }
						                }]
										        
										    })
						            });
						        
						        win.show(this);
            					            					            					
							}		            
        			}
        			],

        		
        		autoHeight      : true   ,
        		frame:true,
        		title:"Extension management"
    		})
		
		';
	
		return $grid;
	}
		
	public function getRows($row) {
		$ter_info = $this->getExtensionTerInfos($row['ext_key'], $row['version']);
		if ($ter_info) {
			if ($ter_info['reviewstate'] > -1) {
				$secure = 'reviewed';
			}else{
				$secure = 'unsecure';
			}
		}else{
			$secure = 'unknown';
		}
		$scope = '';
		if($row['scope']['system']){
			$scope = 'system'; 
		}
		if($row['scope']['global']){
			$scope = 'global'; 
		}
		if($row['scope']['local']){
			$scope = 'local'; 
		}
		return  '["'.$row['ext_key'].'","'.$row['version'].'","'.($row['installed'] ? 'yes':'no').'","'.$secure.'","'.$scope.'"]';
	}

	public function getExtensionTerInfos( $ext_key, $ext_version ){
		$ext_infos = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('extkey, version, reviewstate','cache_extensions','extkey = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($ext_key,'cache_extensions' ).' AND version = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($ext_version,'cache_extensions'), '', '' , 1 );
		if (count($ext_infos)==1){
			return $ext_infos[0];
		} else {
			return false;
		}
	}



}



?>