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
		$operation = array('ExtensionManagement', array('action' => $action,'extkey' => $ids));
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
		
		if(substr($method, 0, 12) == 'getClientExt') 
		{			
			$Result = $this->getClientExtensions($params, $ajaxObj);
		}
		if(substr($method, 0, 7) == 'svninfo') 
		{			
			$Result = $this->Action('svninfo',substr($method, 8));
		}
		if(substr($method, 0, 9) == 'uninstall') 
		{
			$Result = $this->Action('uninstall',substr($method, 10));
		}
		if(substr($method, 0, 7) == 'install') 
		{
			$Result = $this->Action('install',substr($method, 8));
		}
		if(substr($method, 0, 14) == 'databaseUpdate') 
		{
			$Result = $this->Action('databaseUpdate',substr($method, 15));
		}
		if(substr($method, 0, 6) == 'delete') 
		{			
			$Result = $this->Action('delete',substr($method, 7));
		}
		if(substr($method, 0, 6) == 'update') 
		{			
			$Result = $this->Action('update',substr($method, 7));
		}
		if(substr($method, 0, 8) == 'checkout') 
		{			
			$Result = $this->Action('checkout',substr($method, 9));
		}
		if(substr($method, 0, 5) == 'fetch') 
		{			
			$rep_url =  $this->getConfigValue('repurl');
			debug($rep_url);
			if(!$rep_url) $rep_url = 'http://typo3.org/fileadmin/ter/';
			$Result = $this->Action('fetch',substr($method, 6).','.$rep_url);
		}
		if(substr($method, 0, 3) == 'TER') 
		{			
			$Result = $this->getExtensionTer($params, $ajaxObj);
		}
		
		return $Result;
	}
		
	public function getClientExtensions($params, &$ajaxObj){
		
		
		$operation = array('ExtensionManagement', array('action' => 'getExtensions'));		
		$operations = array($operation);

		$commandResult = $this->executeRemoteOperations($operations);
		if (!$this->isCommandResultSuccessful($commandResult)) {
			return $commandResult;
		}

		$results = $commandResult->getOperationResults();
		$operationResult = $results[0];

		if (!$operationResult->isSuccessful()) {
			return  'error: Remote operation failed: ' . $operationResult->getValue();
		} 

		$extensionList = $operationResult->getValue();

		$data = array();
				
		foreach($extensionList as $extension){
			
			$data[] = $this->getRows($extension);
		}
		
		
		$retval = array('exts' => $data);
		$ajaxObj->setContent($retval);
        $ajaxObj->setContentFormat('jsonbody');
	}
	
	public function getView($params, &$ajaxObj) {
		
		$node_id = t3lib_div::_GP('node');
		$back_path = t3lib_div::_GP('back_path');
		$service = t3lib_div::_GP('service');
		$actionId = t3lib_div::_GP('actionid');
		
		
		
		$grid = '
	
		   new Ext.grid.GridPanel({		   		
        		id			:	"button-grid",
        		height		:	760,        		
        		onResize	: 	function() {
        			var viewport = Ext.getCmp("viewport");
        			viewport.doLayout();
        			var grid = Ext.getCmp("button-grid");
        			grid.store.load();
        		},        		        		
				forceFit	:	true,        		
        		title		:	"Extension management",
        		loadMask	: 	true,
        		store		: 	new  Ext.data.GroupingStore({
            		reader: new Ext.data.JsonReader({
									        idProperty: "extKey, version",
									       	fields: ["title", "extKey", "version", "scope","secure","installed","category", "state","svn"],
											// additional configuration for remote
									       	root: "exts",									      
									        remoteGroup:true									        
									    }),
    					sortInfo: {field: "extKey", direction: "ASC"},
    					groupField: "scope",
            		 proxy: new Ext.data.HttpProxy({url: "'.$back_path.'" + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node=" + "'.$node_id.'" + "&actionid='.$actionId.'&service='.$service.'&method=getClientExt"}),
									           
            		
        			}),
        		cm: new Ext.grid.ColumnModel([        			            			
        			{header: "Name", width: 7, sortable: true, dataIndex: "title"},
    				{header: "Extensionkey", width: 7, sortable: true, dataIndex: "extKey"},
            		{header: "Version", width: 3, sortable: true, dataIndex: "version"},
            		{header: "is installed", width: 2, sortable: true, dataIndex: "installed"},
            		{header: "is secure", width: 3, sortable: true, dataIndex: "secure"},
            		{header: "Category", width: 3, sortable: true, dataIndex: "category"},
            		{header: "Stable", width: 3, sortable: false, dataIndex: "state"},
            		{header: "Scope", width: 2, sortable: true, dataIndex: "scope"},
            		{header: "is SVN", width: 2, sortable: true, dataIndex: "svn"}
        			]),
        		sm: new Ext.grid.CheckboxSelectionModel({
        				singleSelect:true,
        				listeners: {			           
			            selectionchange: function(sm) {
			           //Ext.MessageBox.alert("dfghdfh", sm.selections.items[0].data.svn);   
			           var SVNInfo = Ext.getCmp("SVNInfo");
			           var SVNUpdate = Ext.getCmp("SVNUpdate");
			           var Install = Ext.getCmp("Install");
			           var Uninstall = Ext.getCmp("Uninstall");
			           var Delete = Ext.getCmp("Delete");
			           
			           if(sm.selections.items.length > 0 && sm.selections.items[0].data.svn == "yes"){
			            	SVNInfo.enable();
			            	SVNUpdate.enable();
			           }
			           else {
			           		SVNInfo.disable();
			           		SVNUpdate.disable();
			           }
			           if(sm.selections.items.length > 0 && sm.selections.items[0].data.installed == "yes" && sm.selections.items[0].data.scope == "local"){
			            	Install.disable();
			            	Uninstall.enable();
			            	Delete.disable();
			           }
			           else {
			           		Install.enable();
			           		Uninstall.disable();
			           		Delete.enable();
			           }
			           
			            }
			        }
			        				
				}),
        		
        		columnLines: true,
        		
        		buttons: [
        			{
            			text:"Refresh",
            			tooltip:"Reload all extensions",
            			icon    : 	"../res/icons/arrow_refresh_small.png"   ,
            			handler:	 function (){	

            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();	
								
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
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
        		    	id:"SVNInfo",
        		    	disabled:true,
            			tooltip:"Gets SVN Info, if exists",
            			icon    : 	"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/button_unhide.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					if(grid.getSelectionModel().getSelected().get("scope") == "system" || grid.getSelectionModel().getSelected().get("scope") == "global") return;  
            					var selection = grid.getSelectionModel().getSelected().get("extKey");
								
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      										Ext.MessageBox.alert("SVN Status", response.responseText);									       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'",
               							actionid:      "'.$actionId.'",
               							method: "svninfo" + "," + selection           							             							
            								}
        							});
    							}
							}
        			},{
        		    	text:"SVN Update",
        		    	id:"SVNUpdate",
        		    	disabled:true,
            			tooltip:"Execute a SVN update for the selected extension",
            			icon    : 	"../res/icons/arrow_refresh_small.png"   ,
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){   
            					if(grid.getSelectionModel().getSelected().get("scope") == "system" || grid.getSelectionModel().getSelected().get("scope") == "global") return;         					
            					var selection = grid.getSelectionModel().getSelected().get("extKey");
																            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
								
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      										Ext.MessageBox.alert("SVN Status", response.responseText);
																	
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        																		
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});           																       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'",
               							actionid:      "'.$actionId.'",
               							method: "update" + "," + selection           							             							
            								}
        							});
    							}
							}
        			},
        			{
						text:"SVN Checkout",
						icon    : 	"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/new_el.gif', '', 1).'",
						handler:
							function (){								
        						var extId = Ext.id();								
            					var repositoryId = Ext.id();
								var win = new Ext.Window({						                
						                layout:"fit",
						                closeAction:"hide",						                
						                width: 470,
						                height:150,						                
						                plain: true,
						                modal: true,
										title: "Fill in extension name and repository",
						                items: new Ext.FormPanel({
										        labelWidth: 120,											        								        
										        frame:true,										        
										        bodyStyle:"padding:5px 5px 0",										        
										        defaults: {width: 300},
										        defaultType: "textfield",										
										        items: [{
										                fieldLabel: "Extension name",
										                id: extId,
										                name: "extension",
										                allowBlank:false
										            },{
										                fieldLabel: "Repository (Url)",
										                name: "repository",
										                id: repositoryId,
										                vtype:"url",
										                allowBlank:false
										            }
										        ],
						
						                buttons: [{
						                    text:"Ok",
						                    handler: function(){
						                    	var extn = Ext.getCmp(extId);						                    	
						                    	var rep = Ext.getCmp(repositoryId);
						                    							                    							                    	
						                    	if( rep.isValid() && extn.isValid()) {
						                    		win.hide();
						                    		
						                    		var viewpanel = Ext.getCmp("nxcaretakerAction");
													viewpanel.removeAll();
													viewpanel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
													viewpanel.doLayout();
						                    		
													Ext.Ajax.request({
					           							url: "'.$back_path.'" + "ajax.php",
					           							success : function (response, opts){											
					      									Ext.MessageBox.alert("Status", response.responseText);
					      									
					      									Ext.Ajax.request({
							           							url: "'.$back_path.'" + "ajax.php",
							           							success : function (response, opts){											
							      									           								
							        								var jsonData = Ext.util.JSON.decode(response.responseText);
							        																		
																	viewpanel.removeAll();
																	viewpanel.add(jsonData);
																	viewpanel.doLayout(); 	
																						       								       								
							    									}     , 
							           							params: { 
							               							ajaxID: "tx_nxcaretakerservices::doaction",
							               							back_path : "'.$back_path.'",
							               							node:   "'.$node_id.'",
							               							service:   "'.$service.'" ,
							               							actionid:      "'.$actionId.'"        							               							             							
							            								}
							        							});  
					      									
					        								}     , 
					           							params: { 
					               							ajaxID: "tx_nxcaretakerservices::doaction",
					               							back_path : "'.$back_path.'",
					               							node:   "'.$node_id.'",
					               							service:   "'.$service.'",
					               							actionid:      "'.$actionId.'",
					               							method: "checkout," + extn.getRawValue()+","+rep.getRawValue()               							             							
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
						}
        			,"-",{
        		    	text:"Install",
        		    	id:"Install",
        		    	disabled:true,
            			tooltip:"Install the selected extension",
	            		icon    : 	"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/button_unhide.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					if(grid.getSelectionModel().getSelected().get("scope") == "system" || grid.getSelectionModel().getSelected().get("scope") == "global") return;  
            					if(grid.getSelectionModel().getSelected().get("installed") == "yes") return;
            					var selection = grid.getSelectionModel().getSelected().get("extKey");
																            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
								
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											

											if(response.responseText.indexOf("Before the extension can be installed the database needs to be updated with new tables or fields")>=0){	      										
												Ext.MessageBox.confirm("Confirm", response.responseText, function(btn)
					            					{
					            					if(btn == "yes"){
														Ext.Ajax.request({
							           							url: "'.$back_path.'" + "ajax.php",
							           							success : function (response, opts){	
												            			Ext.MessageBox.alert("Result:", response.responseText);
												            			
												            			Ext.Ajax.request({
											           							url: "'.$back_path.'" + "ajax.php",
											           							success : function (response, opts){											
											      									           								
											        								var jsonData = Ext.util.JSON.decode(response.responseText);
											        																		
																					viewpanel.removeAll();
																					viewpanel.add(jsonData);
																					viewpanel.doLayout(); 	
																										       								       								
											    									}     , 
											           							params: { 
											               							ajaxID: "tx_nxcaretakerservices::doaction",
											               							back_path : "'.$back_path.'",
											               							node:   "'.$node_id.'",
											               							service:   "'.$service.'" ,
											               							actionid:      "'.$actionId.'"        							               							             							
											            								}
											        							}); 
												            			}, 
							           							params: { 
							               							ajaxID: "tx_nxcaretakerservices::doaction",
							               							back_path : "'.$back_path.'",
							               							node:   "'.$node_id.'",
							               							service:   "'.$service.'",
							               							actionid:      "'.$actionId.'",
							               							method: "databaseUpdate" + "," + selection           							             							
							            								}
							        							});
					            					}  else {
					            						Ext.Ajax.request({
							           							url: "'.$back_path.'" + "ajax.php",
							           							success : function (response, opts){											
							      									           								
							        								var jsonData = Ext.util.JSON.decode(response.responseText);
							        																		
																	viewpanel.removeAll();
																	viewpanel.add(jsonData);
																	viewpanel.doLayout(); 	
																						       								       								
							    									}     , 
							           							params: { 
							               							ajaxID: "tx_nxcaretakerservices::doaction",
							               							back_path : "'.$back_path.'",
							               							node:   "'.$node_id.'",
							               							service:   "'.$service.'" ,
							               							actionid:      "'.$actionId.'"        							               							             							
							            								}
							        							});
					            					}
												})
											} else { 
											Ext.MessageBox.alert("Result:", response.responseText);

											Ext.Ajax.request({
				           							url: "'.$back_path.'" + "ajax.php",
				           							success : function (response, opts){											
				      									           								
				        								var jsonData = Ext.util.JSON.decode(response.responseText);
				        																		
														viewpanel.removeAll();
														viewpanel.add(jsonData);
														viewpanel.doLayout(); 	
																			       								       								
				    									}     , 
				           							params: { 
				               							ajaxID: "tx_nxcaretakerservices::doaction",
				               							back_path : "'.$back_path.'",
				               							node:   "'.$node_id.'",
				               							service:   "'.$service.'" ,
				               							actionid:      "'.$actionId.'"        							               							             							
				            								}
				        							}); 
					        					}
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'",
               							actionid:      "'.$actionId.'",
               							method: "install" + "," + selection           							             							
            								}
        							});
    							}
							}
        			},{
        		    	text:"Uninstall",
        		    	id:"Uninstall",
        		    	disabled:true,
            			tooltip:"Uninstall the selected extension",
            			icon    : 	"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/button_hide.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					if(grid.getSelectionModel().getSelected().get("scope") == "system" || grid.getSelectionModel().getSelected().get("scope") == "global") return;  
            					if(grid.getSelectionModel().getSelected().get("installed") == "no") return;
            					var selection = grid.getSelectionModel().getSelected().get("extKey");
																            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
								
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      										Ext.MessageBox.alert("Result:", response.responseText);
																	
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        																		
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});           																       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'",
               							actionid:      "'.$actionId.'",
               							method: "uninstall" + "," + selection           							             							
            								}
        							});
    							}
							}
        			},"-",{
            			text:"Delete",
            			id:"Delete",
        		    	disabled:true,
            			tooltip:"Delete the selected extension",
            			icon    : 	"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/garbage.gif', '', 1).'",
            			handler: 		function (){
            					var grid = Ext.getCmp("button-grid");
            					if(grid.getSelectionModel().hasSelection()){
            					if(grid.getSelectionModel().getSelected().get("scope") == "system" || grid.getSelectionModel().getSelected().get("scope") == "global") return;  
            					if(grid.getSelectionModel().getSelected().get("installed") == "yes") {
            						Ext.MessageBox.alert("Info", "Please uninstall the extension first!");
            						return;
            					}
            					var selection = grid.getSelectionModel().getSelected().get("extKey");
								
            					Ext.MessageBox.confirm("Confirm", "Are you sure you want to do that?", function(btn)
            					{
            					if(btn == "yes"){
            					
            					var viewpanel = Ext.getCmp("nxcaretakerAction");
								viewpanel.removeAll();
								viewpanel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
								viewpanel.doLayout();
            					
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      										Ext.MessageBox.alert("Result:", response.responseText);
																		
        						Ext.Ajax.request({
           							url: "'.$back_path.'" + "ajax.php",
           							success : function (response, opts){											
      									           								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        																		
										viewpanel.removeAll();
										viewpanel.add(jsonData);
										viewpanel.doLayout(); 	
															       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'" ,
               							actionid:      "'.$actionId.'"        							               							             							
            								}
        							});           																       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							back_path : "'.$back_path.'",
               							node:   "'.$node_id.'",
               							service:   "'.$service.'",
               							actionid:      "'.$actionId.'",
               							method: "delete" + "," + selection           							             							
            								}
        							});
        							}});
    							}
							}		            
        			},
        		
        				
						
        			{
        		    	text:"Add from TER",
            			tooltip:"Fetch extension from TER",
            			icon    : 	"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/new_el.gif', '', 1).'",
            			handler: 		function (){
            						
            					
            			Ext.getBody().createChild({
            					tag: "script", 
            					html : "var backpath = \"'.$back_path.'\"; var nodeid = \"'.$node_id.'\"; var service = \"'.$service.'\"; var action = \"'.$actionId.'\"; var img = \"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).'\";"
						});
						Ext.getBody().createChild({tag: "script", src: "' . $back_path . t3lib_extMgm::extRelPath('nxcaretakerservices') . 'classes/ajax/ext_expander.js"});
            			Ext.getBody().createChild({tag: "script", src: "' . $back_path . t3lib_extMgm::extRelPath('nxcaretakerservices') . 'classes/services/ExtensionManager.js"});
            			
//									 var reader = new Ext.data.JsonReader({
//									        idProperty: "extkey, version",
//									       	fields: ["title", "extkey", "version", "authorname","reviewstate","description", "state", "uploadcomment"],
//											// additional configuration for remote
//									       	root: "exts",
//									       totalProperty: "totalCount",
//									        remoteGroup:true
//									        
//									    });
//									            			
//
//									
//									   var store =   new Ext.data.GroupingStore({
//									   			storeId : "jstore",         
//									   			reader: reader,												
//									            proxy: new Ext.data.HttpProxy({url: "'.$back_path.'" + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node=" + "'.$node_id.'" + "&actionid='.$actionId.'&service='.$service.'&method=TER"}),
//									            
//									            sortInfo: {field: "extkey", direction: "ASC"},									            	
//									            groupField: "extkey"
//									        });
//
//								    var grid = new Ext.grid.GridPanel({								        
//								        height:600,
//								        forceFit:true,
//								        title:"Typo3 extension repository",
//								        store: store,								        								        
//								        loadMask: true,
//								
//								        // grid columns
//								        columns:[{								            
//								            header: "Extension name",
//								            dataIndex: "title",
//								            width: 10,								            
//								            sortable: true
//								        },{
//								            header: "Extension key",
//								            dataIndex: "extkey",
//								            width: 8,								            
//								            sortable: true
//								        },{
//								            header: "Version",
//								            dataIndex: "version",
//								            width: 4,								            
//								            sortable: true
//								        },{								            
//								            header: "Author",
//								            dataIndex: "authorname",
//								            width: 6,								            
//								            sortable: true
//								        },{								            
//								            header: "Review state",
//								            dataIndex: "reviewstate",
//								            width: 4,								            
//								            sortable: true
//								        },{								            
//								            header: "Stable",
//								            dataIndex: "state",
//								            width: 4,								            
//								            sortable: true
//								        }],
//								       
//								        view: new Ext.grid.GroupingView({
//								            forceFit:true,
//								            startCollapsed 	: 	true,
//								            hideGroupedColumn: true,
//								            enableRowBody 	:	true,
//								            showPreview 	:	false,
//								            getRowClass 	: 	function(record, rowIndex, p, store){
//								                if(this.showPreview){
//								                    p.body = "<p><Div style=\"padding:10px 5px 5px 15px;\"><h4>Description</h4>"+record.data.description+ (record.data.uploadcomment ? "<h4>Uploadcomment</h4>"+record.data.uploadcomment : "")+"</Div></p>";								                    
//								                }
//								                
//								            }
//								        }),
//								        
//								        
//										sm: new Ext.grid.CheckboxSelectionModel({singleSelect:true}),
//								     
//								       
//								        bbar: new Ext.PagingToolbar({
//								            pageSize: 100,
//								            store: store,
//								            displayInfo: true,
//								            displayMsg: "Displaying extensions {0} - {1} of {2}",
//								            emptyMsg: "No extensions to display",
//											 items:[
//							                "-", {
//								                pressed: false,
//								                enableToggle:true,
//								                text: "Show description",								                
//								                toggleHandler: function(btn, pressed){
//								                    var view = grid.getView();
//								                    view.showPreview = pressed;
//								                    view.refresh();
//								                }
//								            }]
//								            
//								        })
//								    });
//									
//				
//								    
//								    // render it
//								    var win = new Ext.Window({
//								    	layout:"fit",
//						                closeAction:"hide",						                
//						                width: 775,
//						                height:750,						                
//						                plain: true,
//						                modal: true,										
//						                items: new Ext.FormPanel({					       												       											        								        
//										        frame:true,				
//										        itemId: "filterIdpp",	
//										        items: [grid,
//										        {		
//										        			style:{marginTop : "15px", marginLeft:"15px"},
//											                columnWidth:.5,
//											                itemId: "filterIdp",	
//											                labelWidth: 100,
//											                defaultType: "textfield",	
//											                layout: "form",
//											                items: [{
//										        
//														        	width		:	230,
//														        	fieldLabel	: 	"Filter",
// 																	itemId: "filterId",														        	
//														            name		: 	"filter",
//																	anchor		:	"95%"
//										      						}]	
//            										   
//										        }],
//						
//						                buttons: [
//						                {
//						                    text: "Filter",
//						                    handler: function(){
//						                    	var filter = win.getComponent("filterIdpp").getComponent("filterIdp").getComponent("filterId");
//						                    	var filterstr = filter.getRawValue();
//						                    	store.baseParams.filter = filterstr;
//						                    	store.load({params:{start:0, limit:100}});    
//						                    }
//						                },{
//						                    text: "Add selected extension",
//						                    handler: function(){
//						                       
//						                        
//				            					if(grid.getSelectionModel().hasSelection()){				            					
//				            					
//				            					var selection = grid.getSelectionModel().getSelected().get("extkey") +","+ grid.getSelectionModel().getSelected().get("version");
//						                        
//						                        var viewpanel = Ext.getCmp("nxcaretakerAction");
//													viewpanel.removeAll()	;
//													viewpanel.add({	html : "<img src="+"'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).' style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
//													viewpanel.doLayout();
//						                    	win.hide();
//					        						Ext.Ajax.request({
//					           							url: "'.$back_path.'" + "ajax.php",
//					           							success : function (response, opts){											
//					      										Ext.MessageBox.alert("Result:", response.responseText);
//																						
//					        						Ext.Ajax.request({
//					           							url: "'.$back_path.'" + "ajax.php",
//					           							success : function (response, opts){											
//					      									           								
//					        								var jsonData = Ext.util.JSON.decode(response.responseText);
//					        																							
//															viewpanel.removeAll();
//															viewpanel.add(jsonData);
//															viewpanel.doLayout(); 	
//																				       								       								
//					    									}     , 
//					           							params: { 
//					               							ajaxID: "tx_nxcaretakerservices::doaction",
//					               							back_path : "'.$back_path.'",
//					               							node:   "'.$node_id.'",
//					               							service:   "'.$service.'" ,
//					               							actionid:      "'.$actionId.'"        							               							             							
//					            								}
//					        							});           																       								       								
//					    									}     , 
//					           							params: { 
//					               							ajaxID: "tx_nxcaretakerservices::doaction",
//					               							back_path : "'.$back_path.'",
//					               							node:   "'.$node_id.'",
//					               							service:   "'.$service.'",
//					               							actionid:      "'.$actionId.'",
//					               							method: "fetch,"+selection           							             							
//					            								}
//					        							});
//													
//					        					}
//						                    }
//						                },{
//						                    text: "Close",
//						                    handler: function(){
//						                        win.hide();
//						                    }
//						                }]
//										        
//										    })
//						            });
//						        
//						            
//						        win.show(this);
//						        
//						         
//								store.load({params:{start:0, limit:100}});
							   
           						          							
    							
    							}
							}
        			
        			],
        			        			
					view: new Ext.grid.GroupingView({
				            forceFit:true,
				            startCollapsed 	: 	true,
				            hideGroupedColumn: true     
				            
			        })
    		})
		
		';
	
		return $grid;
	}
		
	public function getRows($row) {
		$ter_info = $this->getExtensionTerInfos($row['ext_key'], $row['version']);
		$retval = array();
		$retval['extKey'] = $row['ext_key'];
		$retval['version'] = $row['version'];
		if ($ter_info) {
			if ($ter_info['reviewstate'] > -1) {				
				$retval['secure'] = 'reviewed';
			}else{
				$retval['secure'] = 'unsecure';
			}
		}else{			
			$retval['secure'] = 'unknown';
		}
		$scope = '';
		if($row['scope']['system']){
			$retval['scope']  = 'system'; 
		}
		if($row['scope']['global']){
			$retval['scope']  = 'global'; 
		}
		if($row['scope']['local']){
			$retval['scope']  = 'local'; 
		}
		$retval['installed'] = ($row['installed'] ? 'yes':'no');
		$retval['category'] = $row['category'];
		$retval['svn'] = $row['svn'] ? 'yes' : 'no';
		$retval['state'] = $ter_info['state'];
		$retval['title'] = $row['title'];
		return $retval;
	}

	public function getExtensionTerInfos( $ext_key, $ext_version ){
		$ext_infos = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('title, extkey, version, reviewstate, category, state','cache_extensions','extkey = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($ext_key,'cache_extensions' ).' AND version = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($ext_version,'cache_extensions'), '', '' , 1 );
		if (count($ext_infos)==1){
			return $ext_infos[0];
		} else {
			return false;
		}
	}

	public function getExtensionTer($params, &$ajaxObj){
		$start     = (int)t3lib_div::_GP('start');
        $limit     = (int)t3lib_div::_GP('limit');
        $filter     = t3lib_div::_GP('filter');
		
        if($filter) $whereClause = 'extkey LIKE "'.$filter.'%" ';
		else $whereClause = '1=1';
		
		$groupBy='';
		$orderBy='extkey ASC, version DESC';
		
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('extkey, version, description, reviewstate, state, title, authorname, uploadcomment','cache_extensions',$whereClause, $groupBy, $orderBy); 
		if ($result){
			$rows = array();
			$totalCount = 0;				
				while($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result))
				{
					if ($record !== FALSE) {				
						if($totalCount>= $start && $totalCount<$start+$limit){
							
//							$versionResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('version, description, reviewstate, state','cache_extensions','extkey = "'.$record['extkey'].'"','','version'); 
//							if ($versionResult){
//								while($versionRecord = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($versionResult))
//								{
//									if ($versionRecord !== FALSE) {
//										if($record['versions']) $record['versions'] .='; ';
//										$record['versions'] .= $versionRecord['version'];
//										$record['version'] = $versionRecord['version'];
//										$record['description'] = $versionRecord['description'];
//										if($versionRecord['reviewstate'] == -1)	{
//											$record['reviewstate'] = 'unsecure';
//											$record['versions'] .= '(unsecure)';
//										}
//										if($versionRecord['reviewstate'] == 0)	$record['reviewstate'] = 'unknown';
//										if($versionRecord['reviewstate'] == 1)	{
//											$record['reviewstate'] = 'reviewed';
//											$record['versions'] .= '(reviewed)';
//										}
//										$record['stable'] = $versionRecord['state'];		
//									}
//								}
//							}
							$rows[] = $record;												
						}
						$totalCount++;
					}
					
				}
				$retval = array('totalCount' => $totalCount, 'exts' => $rows);
		 	$ajaxObj->setContent($retval);
            $ajaxObj->setContentFormat('jsonbody');
		}
	}


}



?>