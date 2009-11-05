
Ext.namespace('tx','tx.nxcaretakerservices');


tx.nxcaretakerservices.InstallTool = function(backpath, nodeid, service, actionid, nxparams) {
	
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
	
	var InstallToolGeneratePasswordButton = ({
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
	
	var InstallToolResetHandler = function(btn){
				
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

		                buttons		: 	[ InstallToolGeneratePasswordButton,
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
		    											Ext.MessageBox.alert("Status", response.responseText);				    																					       								       								
													}, 
		       							params	: 	{ 
		           							ajaxID		: 	"tx_nxcaretakerservices::doaction",
		           							back_path 	: 	backpath,
		           							node		:   nodeid,
		           							service		:   service,					               							
		           							password	:	MD5(password.getRawValue()),					               							
		           							method		: 	"reset"       							             							
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
		
	};
	
	var InstallToolClickHandler = function (btn){
		Ext.Ajax.request({
				url: backpath + "ajax.php",
				success : function (response, opts){											
					//Ext.MessageBox.alert("Status", response.responseText);				
					InstallToolPanel.load( backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid);        																					       								       								
				}     , 
				params: { 
					ajaxID: "tx_nxcaretakerservices::doaction",
					node:   nodeid,
					service:   service,
					method: btn.postmethod               							             							
					}
			});
	};
	
	var InstallToolDisableButton = ({
		text		:	"disable",
		tooltip		:	"disable installtool",
		postmethod	:	"delete",
		icon    	: 	nxparams["garbageImg"],
		handler		: 	InstallToolClickHandler		
	});	
	
	var InstallToolEnableButton = ({	
		text		:	"enable",
		tooltip		:	"enable installtool",
		postmethod	:	"create",
		icon    	: 	"../res/icons/test.png",
		handler		:	InstallToolClickHandler
	});
	
	var InstallToolResetPasswordButton = ({
		text		:	"reset Password",
		tooltip		:	"reset installtool password",
		icon    	: 	"../res/icons/arrow_refresh_small.png",		
		handler		:	InstallToolResetHandler
	});
	
	var InstallToolPanel = new Ext.Panel({		
				autoHeight      : true   ,
				title 	:	"Install Tool:",																		
				autoLoad 	: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid,
				bbar 			: 	[				 		
					 	 	InstallToolDisableButton,
							InstallToolEnableButton,
							"-",
							InstallToolResetPasswordButton
					]
	 });
	
	var viewpanel = Ext.getCmp("nxcaretakerAction");
	viewpanel.removeAll();
	viewpanel.add(InstallToolPanel);
	viewpanel.doLayout(); 
};