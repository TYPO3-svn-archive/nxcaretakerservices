
Ext.namespace('tx','tx.nxcaretakerservices');


tx.nxcaretakerservices.AutoBELogin = function(backpath, nodeid, service, actionid, nxparams) {
	
//	Ext.getBody().createChild({tag: "script", src: nxparams["md5src"]});
//	
//	Ext.apply(Ext.form.VTypes, {									    
//	    password : function(val, field) {
//	        if (field.initialPassField) {
//	            var pwd = Ext.getCmp(field.initialPassField);
//	            return (val == pwd.getValue());
//	        }
//	        return true;
//	    },
//	
//	    passwordText : "Passwords do not match"
//	});
//	
//	var InstallToolGeneratePasswordButton = ({
//        text	: 	"Generate password",
//        handler	: 	function(){
//        
//           	var charSet = "";
//			charSet += "0123456789";
//			charSet += "abcdefghijklmnopqrstuvwxyz";
//			charSet += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//			charSet += "\`\~\!\@\#\$\%\^\&\*\(\)\-\_\=\+\[\{\]\}\\\|\'\;\:\"\,\.\/\?";
//			
//			var passwordList = "<table cellspacing=\"10\">";												
//			for (var rowi = 0; rowi < 10; ++rowi) {
//				passwordList = passwordList +"<tr>";
//				for (var passwords = 0; passwords < 8; ++passwords) {
//					var rc = "";
//					for (var idx = 0; idx < 8; ++idx) {
//						rc = rc + charSet.charAt(Math.floor(Math.random() * charSet.length ));
//					}
//					passwordList = passwordList +"<td>"+ rc + "</td>";													
//				}	
//				passwordList = passwordList +"</tr>";										
//			}
//       		passwordList = passwordList + "</table>";
//       		
//			Ext.MessageBox.show({
//		           title	: 	"Passwords",
//		           msg		: 	passwordList,					         
//		           buttons	:  	Ext.MessageBox.OK											           
//		    });			
//        }
//    });
//	
//	var InstallToolResetHandler = function(btn){
//				
//			var passId = Ext.id();
//			var pass2Id = Ext.id();
//			            						
//		 	var win = new Ext.Window({						                
//		 		title		: 	"Fill in new password",
//		 		layout		: 	"fit",
//                closeAction	: 	"hide",						                
//                width		: 	375,
//                height		:	150,						                
//                plain		: 	true,
//                modal		: 	true,				
//                items		: 	new Ext.FormPanel({
//				        labelWidth	: 	100,											        								        
//				        frame		:	true,										        
//				        bodyStyle	:	"padding:5px 5px 0",										        
//				        defaults	: 	{width: 230},
//				        defaultType	: 	"textfield",										
//				        items		: 	[{
//						        fieldLabel			: 	"Password",
//						        inputType			:	"password",												       
//						        name				: 	"pass",
//						        id					: 	passId,
//						        allowBlank			:	false
//						    },{
//						        fieldLabel			: 	"Confirm Password",
//						        name				: 	"pass-cfrm",												        
//						        id					: 	pass2Id,
//						        inputType			:	"password",
//						        vtype				: 	"password",
//						        initialPassField	: 	passId,
//						        allowBlank			:	false
//						    }
//				        ],
//
//		                buttons		: 	[ InstallToolGeneratePasswordButton,
//		                {
//		                    text:"Submit",
//		                    handler: function(){						                    	
//		                    	var password = Ext.getCmp(passId);
//		                    	var password2 = Ext.getCmp(pass2Id);						                    	
//		                    							                    	         	
//		                    	if( password.isValid() && password2.isValid()) {
//		                    		win.hide();
//				                    		
//		    						Ext.Ajax.request({
//		       							url		: 	backpath + "ajax.php",
//		       							success : 	function (response, opts){											
//		    											Ext.MessageBox.alert("Status", response.responseText);				    																					       								       								
//													}, 
//		       							params	: 	{ 
//		           							ajaxID		: 	"tx_nxcaretakerservices::doaction",
//		           							back_path 	: 	backpath,
//		           							node		:   nodeid,
//		           							service		:   service,					               							
//		           							password	:	MD5(password.getRawValue()),					               							
//		           							method		: 	"reset"       							             							
//		        							}
//		    						});    							
//								}
//		                    }
//		                },{
//		                    text: "Close",
//		                    handler: function(){
//		                        win.hide();
//		                    }
//		                }]
//				        
//				    })
//            });
//	        
//	        win.show(this);
//		
//	};
	
	var AutoBEClickHandler = function (btn){
		Ext.Ajax.request({
				url: backpath + "ajax.php",
				success : function (response, opts){											
					Ext.MessageBox.alert("Status", response.responseText);				
					//AutoBEPanel.load( backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid);        																					       								       								
				}     , 
				params: { 
					ajaxID: "tx_nxcaretakerservices::doaction",
					node:   nodeid,
					service:   service,
					method: btn.postmethod               							             							
					}
			});
	};
	

	
	var AutoBELoginButton = ({	
		text		:	"Login",
		tooltip		:	"Log into Backend",
		postmethod	:	"login",
		icon    	: 	"../res/icons/test.png",
		handler		:	AutoBEClickHandler
	});
	

	
	var AutoBEPanel = new Ext.Panel({		
				autoHeight      : true   ,
				title 	:	"Auto Backend Login:",																		
				//autoLoad 	: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid,
				html	:  	'<DIV><a href="http://dev3.internal.netlogix.de/~elbert/netlogix/typo3/index.php" target="_blank">link</a> </DIV>', //	'<form action="http://dev3.internal.netlogix.de/~elbert/netlogix/typo3/index.php" method="post" name="loginform" target="_blank" >				<input type="hidden" name="challenge" value="9a1b8c02178e99c5c14829a06556c26c" />				<input type="hidden" name="login_status" value="login" />				<input type="hidden" name="userident" value="5be6083b959441deaf469fed863a6ab6" />				<input type="hidden" name="redirect_url" value="backend.php" />				<input type="hidden" name="loginRefresh" value="" />				<input type="hidden" name="interface" value="backend" />				<input type="hidden" name="username" value="elbert" />									<input type="submit" name="commandLI" id="t3-login-submit" value="Login" class="t3-login-submit" tabindex="4" />				</form>',
				bbar 			: 	[				 		
				     			  	 AutoBELoginButton
					]
	 });
	
	var viewpanel = Ext.getCmp("nxcaretakerAction");
	viewpanel.removeAll();
	viewpanel.add(AutoBEPanel);
	viewpanel.doLayout(); 
};