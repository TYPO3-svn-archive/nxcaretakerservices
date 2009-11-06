
Ext.namespace('tx','tx.nxcaretakerservices');


tx.nxcaretakerservices.UpdateNxcaretakerservices = function(backpath, nodeid, service, actionid, nxparams) {
	
	var UpdateToolClickHandler = function (btn){
		Ext.Ajax.request({
				url: backpath + "ajax.php",
				success : function (response, opts){											
					Ext.MessageBox.alert("Status", response.responseText);				
					UpdateToolPanel.load( backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid);        																					       								       								
				}     , 
				params: { 
					ajaxID: "tx_nxcaretakerservices::doaction",
					node:   nodeid,
					service:   service,
					method: btn.postmethod               							             							
					}
			});
	};
	
	var UpdateToolHeadButton = ({
		text		:	"Update head revision",
		tooltip		:	"Update head revision",
		postmethod	:	"update",
		icon    	: 	"../res/icons/arrow_refresh.png",
		handler		: 	UpdateToolClickHandler		
	});	
	
	var UpdateToolFromToButton = ({	
		text		:	"Update from/to",
		tooltip		:	"Update from repository / to revision",
		postmethod	:	"create",
		icon    	: 	"../res/icons/arrow_refresh.png",
		handler		:	function (){								
		
							var revisionId = Ext.id();
							var repositoryId = Ext.id();
							var win = new Ext.Window({						                
									title		: 	"Choose revision and repository",    
									layout		:	"fit",
					                closeAction	:	"hide",						                
					                width		: 	450,
					                height		:	150,						                
					                plain		:	true,
					                modal		: 	true,									
					                items		: 	new Ext.FormPanel({
									        labelWidth	: 	100,											        								        
									        frame		:	true,										        
									        bodyStyle	:	"padding:5px 5px 0",										        
									        defaults	: 	{width: 300},
									        defaultType	: 	"textfield",										
									        items		: 	[{
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
					
									       buttons		: 	[{
							                    text	:	"Ok",							                    
							                    handler	: 	function(){
							                    	var rev = Ext.getCmp(revisionId);
							                    	var rep = Ext.getCmp(repositoryId);
							                    							                    							                    	
							                    	if( rep.isValid()) {
							                    		win.hide();
							                    		
							                    		Ext.Ajax.request({
							                				url: backpath + "ajax.php",
							                				success : function (response, opts){											
							                					Ext.MessageBox.alert("Status", response.responseText);				
							                					UpdateToolPanel.load( backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid);        																					       								       								
							                				}     , 
							                				params: { 
							                					ajaxID: "tx_nxcaretakerservices::doaction",
							                					node:   nodeid,
							                					service:   service,
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
	});
			
	var UpdateToolPanel = new Ext.Panel({		
				autoHeight      : true   ,
				title 	:	"SVN - Update of the client nxcaretakerservices:",																		
				autoLoad 	: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid,
				bbar 			: 	[				 		
					 	 	UpdateToolHeadButton,
					 	 	"-",
					 	 	UpdateToolFromToButton
					]
	 });
	
	var viewpanel = Ext.getCmp("nxcaretakerAction");
	viewpanel.removeAll();
	viewpanel.add(UpdateToolPanel);
	viewpanel.doLayout(); 
};