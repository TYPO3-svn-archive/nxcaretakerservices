
Ext.namespace('tx','tx.nxcaretakerservices');


tx.nxcaretakerservices.ClearCache = function(backpath, nodeid, service, actionid, nxparams) {
	
	
	
	var ClearCacheClickHandler = function (btn){
		Ext.Ajax.request({
				url: backpath + "ajax.php",
				success : function (response, opts){											
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
	
	var ClearCacheAllButton = ({
		text		:	"all",
		tooltip		:	"clear all caches",
		postmethod	:	"all",
		icon    	: 	nxparams["lred"],
		handler		: 	ClearCacheClickHandler		
	});	
	
	var ClearCachePageButton = ({
		text		:	"page content",
		tooltip		:	"clear page content cache",
		postmethod	:	"page",
		icon    	: 	nxparams["lyellow"],
		handler		: 	ClearCacheClickHandler		
	});	
	
	var ClearCacheConfigurationButton = ({
		text		:	"configuration",
		tooltip		:	"clear configuration cache",
		postmethod	:	"conf",
		icon    	: 	nxparams["lgreen"],
		handler		: 	ClearCacheClickHandler		
	});	
	
	var ClearCachePanel = new Ext.Panel({		
				autoHeight      : true   ,
				title 	:	"Clear Cache:",
				bbar 			: 	[				 		
				     			  	ClearCacheAllButton,		
				     			  	"-",
				     			  	ClearCachePageButton,
				     			  	"-",
				     			  	ClearCacheConfigurationButton
					]
	 });
	
	var viewpanel = Ext.getCmp("nxcaretakerAction");
	viewpanel.removeAll();
	viewpanel.add(ClearCachePanel);
	viewpanel.doLayout(); 
};