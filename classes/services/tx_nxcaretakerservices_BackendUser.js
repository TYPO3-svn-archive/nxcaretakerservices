
Ext.namespace('tx','tx.nxcaretakerservices');


tx.nxcaretakerservices.BackendUser = function(backpath, nodeid, service, actionid, nxparams) {

	var reader = new Ext.data.JsonReader({
        idProperty	: 	"uid",
       	fields		: 	["uid", "username", "admin", "disable","llogin","email","realName"],		
       	root		: 	"users",		
       	totalProperty: 	"total"
    });
	
	var store = new Ext.data.GroupingStore({
		reader		: 	reader,
		sortInfo	: 	{field: "username", direction: "ASC"},		
		groupField	: 	"admin",
		proxy		: 	new Ext.data.HttpProxy({url: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node=" + nodeid + "&actionid=" + actionid + "&service=" + service + "&method=getUser"})         		
	});
	
	var BackendUserCheckboxSelectionModel = new Ext.grid.CheckboxSelectionModel();
	
	var BackendUserColumnModel = new Ext.grid.ColumnModel({
		columns		:	[
				   			BackendUserCheckboxSelectionModel,
							{header: "ID", width: 4, sortable: true, dataIndex: "uid"},
				    		{header: "Username", width: 10, sortable: true, dataIndex: "username"},
				    		{header: "Name", width: 16, sortable: true, dataIndex: "realName"},
				    		{header: "Admin", width: 6, sortable: false, dataIndex: "admin"},
				    		{header: "is disabled", width: 6, sortable: false, dataIndex: "disable"},            		
				    		{header: "Last Login", width: 12, sortable: true, dataIndex: "llogin"},
				    		{header: "Email", width: 16, sortable: true, dataIndex: "email"}
						]
	});
	
	var BackendUserRefreshButton = ({
		text		:	"Refresh",
		tooltip		:	"Reload all users",
		icon    	: 	"../res/icons/arrow_refresh_small.png"   ,
		handler		:	function (){	
							store.load();
						}         			            
	});
	
	var BackendUserSelectionClickHandler = function (btn) {
		var grid = Ext.getCmp("button-grid");
		if(grid.getSelectionModel().hasSelection()){
			
			var selections = grid.getSelectionModel().getSelections();
			var ids = "";			
			var i = 0;
			while(i < selections.length)
			{
				ids = ids + "," + selections[i].get("uid");
				i++;
			}			

			Ext.Ajax.request({
					url			: 	backpath + "ajax.php",
					success 	: 	function (response, opts){
											store.load();							       								       								
										}, 
					params		: 	{ 
						ajaxID		: 	"tx_nxcaretakerservices::doaction",
						back_path 	: 	backpath,
						node		:   nodeid,
						service		:   service,
						method		: 	btn.cmd + ids           							             							
						}
				});
		}
	};
	
	var BackendUserConfirmHandler = function(btn) {
		
		Ext.MessageBox.confirm("Confirm", "Are you sure you want to do that?", function(confirmbtn)
    					{
           					if(confirmbtn == "yes"){BackendUserSelectionClickHandler(btn);}
           				});
	};
	
	Ext.getBody().createChild({tag: "script", src: nxparams["md5src"]});
	
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
	
	var BackendUserGeneratePasswordButton = ({
        text	: 	"Generate password",
        handler	: 	function(){
        
           	var charSet = "";
			charSet += "0123456789";
			charSet += "abcdefghijklmnopqrstuvwxyz";
			charSet += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			charSet += "\`\~\!\@\#\$\%\^\&\*\(\)\-\_\=\+\[\{\]\}\\\|\'\;\:\"\,\.\/\?";
			
			var passwordList = "<table cellspacing=\"10\">";												
			for (var rowi = 0; rowi < 10; ++rowi) {
				passwordList = passwordList +"<tr>";
				for (var passwords = 0; passwords < 8; ++passwords) {
					var rc = "";
					for (var idx = 0; idx < 8; ++idx) {
						rc = rc + charSet.charAt(Math.floor(Math.random() * charSet.length ));
					}
					passwordList = passwordList +"<td>"+ rc + "</td>";													
				}	
				passwordList = passwordList +"</tr>";										
			}
       		passwordList = passwordList + "</table>";
       		
			Ext.MessageBox.show({
		           title	: 	"Passwords",
		           msg		: 	passwordList,					         
		           buttons	:  	Ext.MessageBox.OK											           
		    });			
        }
    });
	
	var BackendUserAddHandler = function(btn) {
		            			
		var grid = Ext.getCmp("button-grid");
			            					
			var userId = Ext.id();
			var passId = Ext.id();
			var pass2Id = Ext.id();
			var nameId = Ext.id();
			var emailId = Ext.id();
			
		 	var win = new Ext.Window({
		 		title		: 	"Fill in user data",
	            layout		:	"fit",
	            closeAction	:	"hide",						                
	            width		: 	375,
	            height		:	250,						                
	            plain		:	true,
	            modal		: 	true,				
	            items		: 	new Ext.FormPanel({
				        labelWidth	: 	100,											        								        
				        frame		:	true,										        
				        bodyStyle	:	"padding:5px 5px 0",										        
				        defaults	: 	{width: 230},
				        defaultType	: 	"textfield",										
				        items		: 	[{
					                fieldLabel			: 	"Username",
					                id					: 	userId,
					                name				:	"username",
					                allowBlank			:	false
					            },{
							        fieldLabel			: 	"Password",
							        inputType			:	"password",												       
							        name				: 	"pass",
							        id					:	passId,
							        allowBlank			:	false
							    },{
							        fieldLabel			: 	"Confirm Password",
							        name				: 	"pass-cfrm",												        
							        id					: 	pass2Id,
							        inputType			:	"password",
							        vtype				: 	"password",
							        initialPassField	: 	passId,
							        allowBlank			:	false
							    },{
					                fieldLabel			: 	"Name",
					                id					: 	nameId,
					                name				: 	"name"
					            },{
					                fieldLabel			: 	"Email",
					                name				: 	"email",
					                id					: 	emailId,
					                vtype				:	"email"
					            }
					        ],	
			            buttons		: 	[ BackendUserGeneratePasswordButton,
			                   	{
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
					   							url		: 	backpath + "ajax.php",
					   							success : 	function (response, opts){																																					
															store.load();           																       								       								
															}, 
					   							params	: 	{ 
					       							ajaxID		: 	"tx_nxcaretakerservices::doaction",
					       							back_path 	: 	backpath,
					       							node		:   nodeid,
					       							service		:   service,
					       							addusername	:	username.getRawValue(),
					       							addpassword	: 	MD5(password.getRawValue()),
					       							addname		: 	name.getRawValue() ,
					       							addemail	:	email.getRawValue() ,
					       							method		: 	"Add"       							             							
					    						}
											});												
										}
					                 }
					            },{
					                text: "Close",
					                handler: function(){
					                    win.hide();
					                }
					            }
					        ]				        
				    })
	        });
	    
	    win.show(this);		            					
	};
	
	var BackendUserResetHandler = function(btn){
		
		var grid = Ext.getCmp("button-grid");            					
		if(grid.getSelectionModel().hasSelection()){
				
			var selections = grid.getSelectionModel().getSelections();
			var ids = "";										
			var i = 0;
			while(i < selections.length)
			{
				ids = ids + "," + selections[i].get("uid");
				i++;
			}
				
			var passId = Ext.id();
			var pass2Id = Ext.id();
			            						
		 	var win = new Ext.Window({						                
		 		title		: 	"Fill in new password",
		 		layout		: 	"fit",
                closeAction	: 	"hide",						                
                width		: 	375,
                height		:	150,						                
                plain		: 	true,
                modal		: 	true,				
                items		: 	new Ext.FormPanel({
				        labelWidth	: 	100,											        								        
				        frame		:	true,										        
				        bodyStyle	:	"padding:5px 5px 0",										        
				        defaults	: 	{width: 230},
				        defaultType	: 	"textfield",										
				        items		: 	[{
						        fieldLabel			: 	"Password",
						        inputType			:	"password",												       
						        name				: 	"pass",
						        id					: 	passId,
						        allowBlank			:	false
						    },{
						        fieldLabel			: 	"Confirm Password",
						        name				: 	"pass-cfrm",												        
						        id					: 	pass2Id,
						        inputType			:	"password",
						        vtype				: 	"password",
						        initialPassField	: 	passId,
						        allowBlank			:	false
						    }
				        ],

		                buttons		: 	[ BackendUserGeneratePasswordButton,
		                {
		                    text:"Submit",
		                    handler: function(){						                    	
		                    	var password = Ext.getCmp(passId);
		                    	var password2 = Ext.getCmp(pass2Id);						                    	
		                    							                    	         	
		                    	if( password.isValid() && password2.isValid()) {
		                    		win.hide();
				                    		
		    						Ext.Ajax.request({
		       							url		: 	backpath + "ajax.php",
		       							success : 	function (response, opts){											
		  										
				    								store.load();													       								       								
													}, 
		       							params	: 	{ 
		           							ajaxID: "tx_nxcaretakerservices::doaction",
		           							back_path : backpath,
		           							node:   nodeid,
		           							service:   service,					               							
		           							password: MD5(password.getRawValue()),					               							
		           							method: "reset" + ids      							             							
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
	};
	
	var BackendUserEnableButton = ({
		text		:	"Enable",
		tooltip		:	"Enable all selected users",
		icon    	: 	nxparams["unhideImg"],
		cmd			:	"Enable",
		handler		:	BackendUserSelectionClickHandler
	});
	
	var BackendUserDisableButton = ({
		text		:	"Disable",
		tooltip		:	"Disable all selected users",
		icon    	: 	nxparams["hideImg"],
		cmd			:	"Disable",
		handler		:	BackendUserSelectionClickHandler
	});
	
	var BackendUserEnableAdminButton = ({
		text		:	"Enable Admin",
		tooltip		:	"Enable Admin to all selected users",
		icon    	: 	nxparams["adminImg"],
		cmd			:	"Admin",
		handler		:	BackendUserSelectionClickHandler
	});
	
	var BackendUserDisableAdminButton = ({
		text		:	"Disable Admin",
		tooltip		:	"Disable Admin to all selected users",
		icon    	: 	nxparams["userImg"],
		cmd			:	"NoAdmin",
		handler		:	BackendUserSelectionClickHandler
	});
	
	var BackendUserDeleteButton = ({
		text		:	"Delete",
		tooltip		:	"Delete the selected users",
		icon    	: 	nxparams["garbageImg"],
		cmd			:	"Delete",
		handler		:	BackendUserConfirmHandler
	});
	
	var BackendUserAddButton = ({
		text		:	"Add User",
		tooltip		:	"Add new user",
		icon    	: 	nxparams["addImg"],		
		handler		:	BackendUserAddHandler
	});
	
	var BackendUserResetPasswordButton = ({
		text		:	"Reset Password",
		tooltip		:	"reset password of selected users",
		icon    	: 	"../res/icons/arrow_refresh_small.png",		
		handler		:	BackendUserResetHandler
	});
	
	var BackendUserGrid = new Ext.grid.GridPanel({
	        		id				:	"button-grid",
	        		title			:	"User management",
	        		store			: 	store,        		
	        		cm				: 	BackendUserColumnModel,	 
	        		sm				: 	BackendUserCheckboxSelectionModel,	        		       		
	        		columnLines		: 	true,       		
	        		autoHeight      : 	true,
	        		frame			:	true,	        		
	        		loadMask		: 	true,
	        		onResize		: 	function() {
						        			var viewport = Ext.getCmp("viewport");	        			
						        			viewport.doLayout();	        			
						        		},        
	        		view			: 	new Ext.grid.GroupingView({
								            forceFit			:	true,
								            hideGroupedColumn	: 	true,
								            groupTextTpl		: 	'{[values.gvalue == 1 ? "Admins" : "Users"]} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
							            }),
	        		buttons			: 	[  BackendUserRefreshButton ],
	        		buttonAlign		:	"center",   
			        tbar			:	[  BackendUserEnableButton, 
			            			 	   BackendUserDisableButton, 
			            			 	   "-", 
			            			 	   BackendUserEnableAdminButton, 
			            			 	   BackendUserDisableAdminButton, 
			            			 	   "-", 
			            			 	   BackendUserDeleteButton,
			            			 	   BackendUserAddButton,
			            			 	   "-",
			            			 	   BackendUserResetPasswordButton	
			            			 ]
	
	    		});

		
	var viewpanel = Ext.getCmp("nxcaretakerAction");
	viewpanel.removeAll();
	viewpanel.add(BackendUserGrid);
	viewpanel.doLayout(); 
	
	store.load();
		
};