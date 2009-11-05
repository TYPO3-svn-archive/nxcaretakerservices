
Ext.namespace('tx','tx.nxcaretakerservices');


tx.nxcaretakerservices.DeprecationLog = function(backpath, nodeid, service, actionid, nxparams) {
	
	var DeprecationLogClickHandler = function (btn){
		
		Ext.Ajax.request({
				url		: 	backpath + "ajax.php",
				success : 	function (response, opts){											
						//Ext.MessageBox.alert("Status", response.responseText);
						var DeprecationLogPanel = Ext.getCmp("DeprecationLogPanel");
						var DeprecationLogLinks = Ext.getCmp("DeprecationLogLinks");
						DeprecationLogPanel.load( backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid);
						DeprecationLogLinks.load( backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node=" + nodeid + "&actionid=" + actionid + "&service=" + service + "&method=show");
							}, 
				params	: 	{ 
					ajaxID	: 	"tx_nxcaretakerservices::doaction",
					node	:   nodeid,
					service	:   service,
					method	: 	btn.text               							             							
					}
			});
	};
	
	var DeprecationLogDisableButton = new Ext.Button({
		text		:	"disable",
		icon    	: 	nxparams["garbageImg"],
		handler		:	DeprecationLogClickHandler
	});
	
	var DeprecationLogEnableButton = new Ext.Button({
		text		:	"enable",
		icon    	: 	"../res/icons/test.png",
		handler		:	DeprecationLogClickHandler
	});
	
	var DeprecationLogPanel = new Ext.Panel({									
		items		: [
						new Ext.Panel({										
							id          : 	"DeprecationLogPanel",
							title 		:	"Deprecation Log:",																		
							autoLoad 	: 	backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid,
							bbar 		: 	[DeprecationLogDisableButton,DeprecationLogEnableButton]												
						}),
						new Ext.Panel({
							id			:	"DeprecationLogLinks",
							title 		:	"Links:",																		
							autoLoad 	: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node=" + nodeid + "&actionid=" + actionid + "&service=" + service + "&method=show"
						})
					]	
	});
	
	var viewpanel = Ext.getCmp("nxcaretakerAction");
	viewpanel.removeAll();
	viewpanel.add(DeprecationLogPanel);
	viewpanel.doLayout(); 
};