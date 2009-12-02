
Ext.namespace('tx','tx.nxcaretakerservices');


tx.nxcaretakerservices.ExtensionManager = function(backpath, nodeid, service, actionid, nxparams) {
	
	var ExtensionManagerTERInit = function(response){
		
		eval(response.responseText);
		
		var ExtensionManagerTERGridReader = new Ext.data.JsonReader({		 
	        idProperty		: 	"extkey, version",
	       	fields			: 	["title", "extkey", "version", "authorname","reviewstate","description", "state", "uploadcomment"],		
	       	root			: 	"exts",
	       	totalProperty	: 	"totalCount",
	        remoteGroup		:	true        
	    });
		 
		var ExtensionManagerTERGridStore =   new Ext.data.GroupingStore({
  			storeId 		: 	"jstore",         
  			reader			: 	ExtensionManagerTERGridReader,												
  			proxy			: 	new Ext.data.HttpProxy({url: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node=" + nodeid + "&service=" + service + "&method=TER&actionid=" + actionid}),           
  			sortInfo		: 	{field: "extkey", direction: "ASC"},									            	
  			groupField		: 	"extkey"
	    });
		   
	   var ExtensionManagerTERGridExpander = new Ext.grid.RowExpander({
	        tpl 			: 	new Ext.Template('<br><p><b> Description:</b> {description}<br><br><b> Uploadcomment:</b> {uploadcomment}</p><br>')
	    });
	   
	   var ExtensionManagerTERGrid = new Ext.grid.GridPanel({	
		   	id				:	"terGrid",
	        height			:	600,
	        forceFit		:	true,
	        title			:	"Typo3 extension repository",
	        store			: 	ExtensionManagerTERGridStore,								        								        
	        loadMask		: 	true,
	        plugins			:	ExtensionManagerTERGridExpander,
	        columns			:	[ 
						          ExtensionManagerTERGridExpander,
						          {								            
						            header: "Extension name",
						            dataIndex: "title",
						            width: 10,								            
						            sortable: true
						        },{
						            header: "Extension key",
						            dataIndex: "extkey",
						            width: 8,								            
						            sortable: true
						        },{
						            header: "Version",
						            dataIndex: "version",
						            width: 4,								            
						            sortable: true
						        },{								            
						            header: "Author",
						            dataIndex: "authorname",
						            width: 6,								            
						            sortable: true
						        },{								            
						            header: "Review state",
						            dataIndex: "reviewstate",
						            width: 4,								            
						            sortable: true
						        },{								            
						            header: "Stable",
						            dataIndex: "state",
						            width: 4,								            
						            sortable: true
						        }],
	       
	        view			: 	new Ext.grid.GroupingView({
						            forceFit			:	true,	            
						            startCollapsed 		: 	true,
						            hideGroupedColumn	: 	true,
						            enableRowBody 		:	true
						        }),
	        
	    	sm				:	new Ext.grid.CheckboxSelectionModel({
				    				singleSelect	:	true,
				    				listeners		: 	{ selectionchange: function(sm) {				              
										           				var addButton = Ext.getCmp("addFromTerButton");
										           				if(sm.selections.items.length > 0) 	{
										           					addButton.enable();
										           					console.log(ExtensionManagerTERGridExpander);
										           				}
										           				else addButton.disable();
						    								}
				    									}
	    						}),
	        bbar			: 	new Ext.PagingToolbar({
						            pageSize		: 	100,
						            store			: 	ExtensionManagerTERGridStore,
						            displayInfo		: 	true,
						            displayMsg		: 	"Displaying extensions {0} - {1} of {2}",
						            emptyMsg		: 	"No extensions to display"	            
						        })
	    });
	   	    
	    var ExtensionManagerTERWin = new Ext.Window({
	    	layout		:	"fit",
	    	closeAction	:	"hide",						                
	    	width		:	775,
	    	height		:	750,						                
	    	plain		: 	true,
	    	modal		: 	true,										
	    	items		: 	new Ext.FormPanel({					       												       											        								        
			        frame	:	true,				
			        itemId	: 	"filterIdpp",	
			        items	: 	[ExtensionManagerTERGrid,
			             	  	 {		
				        			style		:	{marginTop : "15px", marginLeft:"15px"},
					                columnWidth	:	.5,
					                itemId		: 	"filterIdp",	
					                labelWidth	: 	100,
					                defaultType	: 	"textfield",	
					                layout		: 	"form",
					                items		: 	[{				        
											        	width		:	230,
											        	fieldLabel	: 	"Filter",
														itemId		: 	"filterId",														        	
											            name		: 	"filter",
														anchor		:	"95%"
						      						}]								   
			             	  	 }],
		           buttons: [
		           {
		               text		: 	"Filter",
		               handler	: 	function(){
				               	var filter = ExtensionManagerTERWin.getComponent("filterIdpp").getComponent("filterIdp").getComponent("filterId");
				               	var filterstr = filter.getRawValue();
				               	ExtensionManagerTERGridStore.baseParams.filter = filterstr;
				               	ExtensionManagerTERGridStore.load({params:{start:0, limit:100}});    
		               		}
		           },{
		               text		: 	"Add selected extension",
		               id		:	"addFromTerButton",
		               disabled	:	true,
		               handler	: 	function(){	                  
		                   
							if(ExtensionManagerTERGrid.getSelectionModel().hasSelection()){				            					
								
								var selection = ExtensionManagerTERGrid.getSelectionModel().getSelected().get("extkey") +","+ ExtensionManagerTERGrid.getSelectionModel().getSelected().get("version");			                   
								ExtensionManagerTERWin.hide();
									
								Ext.Ajax.request({
		  							url		: 	backpath + "ajax.php",
		  							success : 	function (response, opts){											
													Ext.MessageBox.alert("Status", response.responseText, function(){ ExtensionManagerGridStore.load();});       								
												}, 
		  							params	: 	{ 
			      							ajaxID		: 	"tx_nxcaretakerservices::doaction",
			      							back_path 	: 	backpath,
			      							node		:   nodeid,
			      							service		:   service,
			      							actionid	:   actionid,
			      							method		: 	"fetch," + selection           							             							
		   								}
								});
								
							}
		               }
		           },{
		               text		: "Close",
		               handler	: function(){
		        	   					ExtensionManagerTERWin.hide();
					               }
		           }]			        
	    	})
       });
	
	
	    var ExtensionManagerGridReader = new Ext.data.JsonReader({
	    	idProperty	: 	"extKey, version",
	       	fields		: 	["title", "extKey", "version", "scope","secure","installed","category", "state","svn"],											
	       	root		: 	"exts"			        
	    });
	    
	    var ExtensionManagerGridStore = new  Ext.data.GroupingStore({
	   		reader		: 	ExtensionManagerGridReader,
			sortInfo	: 	{field: "extKey", direction: "ASC"},
			groupField	: 	"scope",
			proxy		: 	new Ext.data.HttpProxy({url: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node=" + nodeid + "&actionid=" + actionid + "&service=" + service + "&method=getClientExt"})
	    }) ;
		
	    var ExtensionManagerGridCheckboxSelectionModel = new Ext.grid.CheckboxSelectionModel({
			singleSelect	:	true,
			listeners		: 	{			           
					selectionchange	: 	function(sm) {
			          
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
		    				
		});
	    
	    var ExtensionManagerGridColumnModel = new Ext.grid.ColumnModel([        			            			
	                                                            		{header: "Name", 			width: 7, 	sortable: true, 	dataIndex: "title"},
	                                                            		{header: "Extensionkey", 	width: 7, 	sortable: true, 	dataIndex: "extKey"},
	                                                            		{header: "Version", 		width: 3, 	sortable: true, 	dataIndex: "version"},
	                                                            		{header: "is installed", 	width: 2, 	sortable: true, 	dataIndex: "installed"},
	                                                            		{header: "is secure", 		width: 3, 	sortable: true, 	dataIndex: "secure"},
	                                                            		{header: "Category", 		width: 3, 	sortable: true, 	dataIndex: "category"},
	                                                            		{header: "Stable", 			width: 3, 	sortable: false, 	dataIndex: "state"},
	                                                            		{header: "Scope", 			width: 2, 	sortable: true, 	dataIndex: "scope"},
	                                                            		{header: "is SVN", 			width: 2, 	sortable: true, 	dataIndex: "svn"}
	                                                            		]);
	    
	    var ExtensionManagerGridRefreshButton = ({
			text		:	"Refresh",
			tooltip		:	"Reload all extensions",
			icon    	: 	"../res/icons/arrow_refresh_small.png"   ,
			handler		:	function (){	
	    						ExtensionManagerGridStore.load();
							}         			            
		});
	    
	    var ExtensionManagerGridButtonHandler = function(btn) {
	    	
	    	var grid = Ext.getCmp("ExtensionManagerGrid");
			if(grid.getSelectionModel().hasSelection()){
	    	
				var selection = grid.getSelectionModel().getSelected().get("extKey");
				
		    	Ext.Ajax.request({
					url: backpath + "ajax.php",
					success 		: 	function (response, opts){	
		    																						
											Ext.MessageBox.alert("Status", response.responseText, function(){if(btn.gridReload) ExtensionManagerGridStore.load();});	
					}, 
					params: { 
						ajaxID		: 	"tx_nxcaretakerservices::doaction",
						back_path 	: 	backpath,
						node		:   nodeid,
						service		:   service,
						actionid	:   actionid,
						method		: 	btn.postParam + "," + selection       							             							
						}
				});
			}
	    };
	    
	    var ExtensionManagerGridSVNInfoButton = ({
	    	text		:	"SVN Info",
	    	id			:	"SVNInfo",
	    	postParam	:	"svninfo",
	    	gridReload	:	false,
	    	disabled	:	true,
			tooltip		:	"Gets SVN Info, if exists",
			icon    	: 	nxparams["unhideImg"],
			handler		:	ExtensionManagerGridButtonHandler
	    });
	    
	    var ExtensionManagerGridSVNUpdateButton = ({
	    	text		:	"SVN Update",
	    	id			:	"SVNUpdate",
	    	disabled	:	true,
	    	gridReload	:	true,
	    	postParam	:	"update",
			tooltip		:	"Execute a SVN update for the selected extension",
			icon    	: 	"../res/icons/arrow_refresh_small.png"   ,
			handler		:	ExtensionManagerGridButtonHandler
		});
	    
	    var ExtensionManagerGridSVNCheckoutButton = ({
			text		:	"SVN Checkout",
			icon    	: 	nxparams["addImg"],
			handler		:	function (){								
					var extId = Ext.id();								
					var repositoryId = Ext.id();
					var win = new Ext.Window({						                
							title		: 	"Fill in extension name and repository",    
							layout		:	"fit",
			                closeAction	:	"hide",						                
			                width		: 	470,
			                height		:	150,						                
			                plain		: 	true,
			                modal		: 	true,						
			                items		: 	new Ext.FormPanel(
			                		{
							        labelWidth	: 	120,											        								        
							        frame		:	true,										        
							        bodyStyle	:	"padding:5px 5px 0",										        
							        defaults	: 	{width: 300},
							        defaultType	: 	"textfield",										
							        items		: 	[{
							                fieldLabel	: 	"Extension name",
							                id			:	extId,
							                name		:	"extension",
							                allowBlank	:	false
							            },{
							                fieldLabel	: 	"Repository (Url)",
							                name		: 	"repository",
							                id			: 	repositoryId,
							                vtype		:	"url",
							                allowBlank	:	false
							            }],		
					                buttons		: 	[{
						                    text		:	"Ok",
						                    handler		: 	function(){
						                    	var extn = Ext.getCmp(extId);						                    	
						                    	var rep = Ext.getCmp(repositoryId);					                    							                    							                    	
						                    	if( rep.isValid() && extn.isValid()) {
						                    		win.hide();
						                    		
													Ext.Ajax.request({
					           							url: backpath + "ajax.php",
					           							success : function (response, opts){							
														
																			Ext.MessageBox.alert("Status", response.responseText, function(){ ExtensionManagerGridStore.load();});
					      												}, 
					           							params: { 
					               							ajaxID		: 	"tx_nxcaretakerservices::doaction",
					               							back_path 	: 	back_path,
					               							node		:   nodeid,
					               							service		:   service,
					               							actionid	:   actionId,
					               							method		: 	"checkout," + extn.getRawValue() + "," + rep.getRawValue()               							             							
					            								}
					        							});				        							
													}
							                    }
							                },{
						                    text		: 	"Cancel",
						                    handler		: 	function(){
											                        win.hide();
											                    }
							                }
							                ]						        
							    })
			            });
			        
			        win.show(this);
				
				}
			});
	    
	    var ExtensionManagerGridInstallButton =({
	    	text		:	"Install",
	    	id			:	"Install",
	    	disabled	:	true,
			tooltip		:	"Install the selected extension",
			icon    	: 	nxparams["unhideImg"],
			handler		:	function (){
					var grid = Ext.getCmp("ExtensionManagerGrid");				
					var selection = grid.getSelectionModel().getSelected().get("extKey");
					
					Ext.Ajax.request({
							url			: 	backpath + "ajax.php",
							success 	: 	function (response, opts){											
	
								if(response.responseText.indexOf("Before the extension can be installed the database needs to be updated with new tables or fields") >= 0){	      										
									Ext.MessageBox.confirm("Confirm", response.responseText, function(btn)
		            					{
		            					if(btn == "yes"){
											Ext.Ajax.request({
				           							url		: 	backpath + "ajax.php",
				           							success : 	function (response, opts){	
									            			
																	Ext.MessageBox.alert("Status", response.responseText, function(){ ExtensionManagerGridStore.load();});
									            			}, 
				           							params	: 	{ 
				               							ajaxID		: 	"tx_nxcaretakerservices::doaction",
				               							back_path 	: 	backpath,
				               							node		:   node_id,
				               							service		:   service,
				               							actionid	:	actionId,
				               							method		: 	"databaseUpdate" + "," + selection           							             							
				            								}
				        							});
		            					}  
		            					else ExtensionManagerGridStore.load();
									});
								} 
								else Ext.MessageBox.alert("Status", response.responseText, function(){ ExtensionManagerGridStore.load();});
							}     , 
							params		: 	{ 
									ajaxID		: 	"tx_nxcaretakerservices::doaction",
									back_path 	: 	backpath,
									node		:   nodeid,
									service		:   service,
									actionid	:   actionid,
									method		: 	"install" + "," + selection           							             							
								}
						});
					}
				
		});
	    
	    var ExtensionManagerGridUninstallButton = ({
	    	text		:	"Uninstall",
	    	id			:	"Uninstall",
	    	disabled	:	true,
	    	gridReload	:	true,
	    	postParam	:	"uninstall",
			tooltip		:	"Uninstall the selected extension",
			icon    	: 	nxparams["hideImg"],
			handler		:	ExtensionManagerGridButtonHandler
		});
	    
	    var ExtensionManagerGridDeleteButton = ({
			text		:	"Delete",
			id			:	"Delete",
	    	disabled	:	true,
	    	gridReload	:	true,
	    	postParam	:	"delete",
			tooltip		:	"Delete the selected extension",
			icon    	: 	nxparams["garbageImg"],
			handler		:	function (btn){				
					
					Ext.MessageBox.confirm("Confirm", "Are you sure you want to do that?", function(confirmbtn){
						if(confirmbtn == "yes"){
							
							ExtensionManagerGridButtonHandler(btn);
						}
					});				
				}		            
		});
	    
	    var ExtensionManagerGridAddTERButton = (	    			{
	    	text		:	"Add from TER",
			tooltip		:	"Fetch extension from TER",
			icon    	: 	nxparams["addImg"],
			handler		: 	function (){
			
							    	ExtensionManagerTERWin.show();
							    	ExtensionManagerTERGridStore.load({params:{start:0, limit:100}});							
								}
		});
	    
		var ExtensionManagerGrid = new Ext.grid.GridPanel({		   		
			id			:	"ExtensionManagerGrid",
			title		:	"Extension management",
			height		:	700,
			store		: 	ExtensionManagerGridStore,
			cm			: 	ExtensionManagerGridColumnModel,
			sm			: 	ExtensionManagerGridCheckboxSelectionModel,		
			columnLines	: 	true,
			frame		:	true,
			loadMask	: 	true,
			onResize	: 	function() {
								var viewport = Ext.getCmp("viewport");
								viewport.doLayout();	    			
							},						
			view		: 	new Ext.grid.GroupingView({
						            forceFit			:	true,
						            startCollapsed 		: 	true,
						            hideGroupedColumn	: 	true,
						            groupTextTpl		: 	'{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
								}),
			buttons		: 	[ ExtensionManagerGridRefreshButton ],
			buttonAlign	:	"center",        		
		    		
	        tbar		:	[ ExtensionManagerGridSVNInfoButton, 
	            		 	  ExtensionManagerGridSVNUpdateButton,
	            		 	  ExtensionManagerGridSVNCheckoutButton,
	            		 	  "-",
	            		 	  ExtensionManagerGridInstallButton,
	            		 	  ExtensionManagerGridUninstallButton,
	            		 	  "-",
	            		 	  ExtensionManagerGridDeleteButton,
	            		 	  ExtensionManagerGridAddTERButton
	            		 	 ]
	        
		});
		
		var viewpanel = Ext.getCmp("nxcaretakerAction");
		viewpanel.removeAll();
		viewpanel.add(ExtensionManagerGrid);
		viewpanel.doLayout(); 
		
		ExtensionManagerGridStore.load();
	};
	
	Ext.Ajax.request({
		url		: 	nxparams["ext_expander"],
		success : 	ExtensionManagerTERInit
	});
};