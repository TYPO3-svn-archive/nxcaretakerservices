
Ext.namespace('tx','tx.nxcaretakerservices');


tx.nxcaretakerservices.SSLKeyCreator = function(backpath, nodeid, service, actionid, nxparams) {
	

	
	var SSLKeyCreateClickHandler = function (btn){
		Ext.Ajax.request({
				url: backpath + "ajax.php",
				success : function (response, opts){											
									
					SSLKeyCreatePanel.load( backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid);
					
					Ext.MessageBox.alert("Status", response.responseText);	
				}     , 
				params: { 
					ajaxID: "tx_nxcaretakerservices::doaction",
					node:   nodeid,
					service:   service,
					method: btn.postmethod               							             							
					}
			});
	};
		
	var SSLKeyCreateButton = ({	
		text		:	"create",
		tooltip		:	"create new SSL Keys",
		postmethod	:	"create",
		icon    	: 	"../res/icons/test.png",
		handler		:	SSLKeyCreateClickHandler
	});
	

	
	var SSLKeyCreatePanel = new Ext.Panel({		
				autoHeight      : true   ,
				title 	:	"Create SSL Key:",																		
				autoLoad 	: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + nodeid + "&actionid=" + actionid,
				bbar 			: 	[	SSLKeyCreateButton	]
	 });
	
	var viewpanel = Ext.getCmp("nxcaretakerAction");
	viewpanel.removeAll();
	viewpanel.add(SSLKeyCreatePanel);
	viewpanel.doLayout(); 
};