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
		}

		return tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, 0, '');
	}

	public function test()
	{
			
		$operation = array('GetBeusers', array());
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);

		$message ="";
		
		$results = $commandResult->getOperationResults();
		
		$operationResult = $results[0]->getValue();
		
		foreach($operationResult as $row){
		
			$message = $message . $row['username'] . ': ' . ($row['disable'] ? 'disabled' : '') . "\n";
		}
			
		
		return $message;
	}
	
	public function Action($action,$ids)
	{
		$operation = array('GetBeusers', array('action' => $action,'ids' => $ids));
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		
		$results = $commandResult->getOperationResults();
		
		$operationResult = $results[0]->getValue();
		
		
		return $operationResult;
	}
	
	public function doAction($method)
	{
		$Result="";
		
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
			$Result  = $this->Action('add',substr($method, 4));
		}
		
		return $Result;
	}
		
	public function getView($service, $actionId) {
		
		$operation = array('GetBeusers', array());
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);

		$message ="[";
		
		$results = $commandResult->getOperationResults();
		
		if (!$this->isCommandResultSuccessful($commandResult)) {
			return 'error '. $commandResult->getMessage();
		}
		
		$operationResult = $results[0]->getValue();
				
		foreach($operationResult as $row){
			if($message != '[') $message = $message . ',';
			$message = $message . $this->getRows($service, $row);
		}
		$message = $message . ']';
		
		$grid = '
	
		   new Ext.grid.GridPanel({
        		id:"button-grid",
        		store: new Ext.data.Store({
            		reader: new Ext.data.ArrayReader({}, [
       					{name: "uid", type: "int"},	
            			{name: "username"},
       					{name: "admin", type: "int"},
       					{name: "disable", type: "int"},       					
       					{name: "llogin"},
       					{name: "email"},
       					{name: "realName"}
    					]),
            		data: 
            			'.$message.'
            		
        			}),
        		cm: new Ext.grid.ColumnModel([
            			new Ext.grid.CheckboxSelectionModel(),
    				{id:"uid",header: "ID", width: 4, sortable: true, dataIndex: "uid"},
            		{header: "Username", width: 10, sortable: true, dataIndex: "username"},
            		{header: "Name", width: 16, sortable: true, dataIndex: "realName"},
            		{header: "is Admin", width: 6, sortable: false, dataIndex: "admin"},
            		{header: "is disabled", width: 6, sortable: false, dataIndex: "disable"},            		
            		{header: "Last Login", width: 12, sortable: true, dataIndex: "llogin"},
            		{header: "Email", width: 16, sortable: true, dataIndex: "email"}
        			]),
        		sm: new Ext.grid.CheckboxSelectionModel(),
        		viewConfig: { forceFit:true },
        		columnLines: true,
        
        		buttons: [
        			{
            			text:"Refresh",
            			tooltip:"Reload all users",
            			icon    : 	"../res/icons/arrow_refresh_small.png"   ,
            			handler:	 function (){	
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										var viewpanel = Ext.getCmp("nxcaretakerAction");
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
        		    	text:"Enable",
            			tooltip:"Enable all selected users",
            			icon    : 	tx.caretaker.back_path+"'.t3lib_iconWorks::skinImg('', 'gfx/button_unhide.gif', '', 1).'",
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
            					
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										var viewpanel = Ext.getCmp("nxcaretakerAction");
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
               							method: "Enable" + ids           							             							
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
            					
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										var viewpanel = Ext.getCmp("nxcaretakerAction");
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
            					
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										var viewpanel = Ext.getCmp("nxcaretakerAction");
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
            					
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																	
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										var viewpanel = Ext.getCmp("nxcaretakerAction");
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
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      										
																		
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										var viewpanel = Ext.getCmp("nxcaretakerAction");
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
						                    							                    		
					        						Ext.Ajax.request({
					           							url: tx.caretaker.back_path + "ajax.php",
					           							success : function (response, opts){											
					      										
																						
					        						Ext.Ajax.request({
					           							url: tx.caretaker.back_path + "ajax.php",
					           							success : function (response, opts){											
					      									           								
					        								var jsonData = Ext.util.JSON.decode(response.responseText);
					        								
															var viewpanel = Ext.getCmp("nxcaretakerAction");
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
        		title:"User management"
    		})
		
		';
	
		return $grid;
	}
		
	public function getRows($service, $row) {
		return  '["'.$row['uid'].'","'.$row['username'].'","'.$row['admin'].'","'.$row['disable'].'","'.$row['llogin'].'","'.$row['email'].'","'.$row['realName'].'"]';
	}




}



?>