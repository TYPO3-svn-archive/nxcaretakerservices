  var view = new Ext.Viewport({
    	layout: "fit",
    	id: "viewport",
    	items: {
    	xtype    : "tabpanel",
    	id       : "node",
    	activeTab: 0,
    	autoScroll: true,
        title    : '' +  tx.caretaker.node_info.title + ' (' + (tx.caretaker.node_info.type_description ?  tx.caretaker.node_info.type_description : tx.caretaker.node_info.type ) + ')' ,
    	iconCls  : "icon-caretaker-type-" + tx.caretaker.node_info.type_lower,
    
    	items    : [                   
    	            {
    	            	id       : "defaultPanel",                   
    	            	title    : '' +  tx.caretaker.node_info.title + ' (' + tx.caretaker.node_info.type + ')' ,  
    	            	tbar     : tx.caretaker.node_toolbar,
    	            	autoScroll: true,
    	            	items    : [
    	            	            	node_information ,
    	            	            	node_charts,
    	            	            	node_log
    	            	            	]

    	            },
    	            {		
    	    			id:"nxcaretakerAction",
    	    			title:'Action',
    	    			xtype    : "panel",
    	    			width:450,    
    	    			autoScroll: true,
    	    			tbar: {	
    	    				id			:'actionToolbar',	
    	    				layout : "toolbar",
    	    		            items :  []
    	    			},
    	    			defaults:{autoHeight: true},
    	    			items:[],
    	    			listeners: {activate:	function(tab){
    	    				Ext.Ajax.request({
    	    					url : tx.caretaker.back_path + 'ajax.php',
    	    					method : 'GET',
    	    					success : function(result, request) {
    	    						var jsonData = Ext.util.JSON.decode(result.responseText);
    	    						var tb = Ext.getCmp('actionToolbar');
    	    						tb.removeAll();
    	    						tb.add( jsonData );
    	    						tb.doLayout();
    	    					},
    	    						params: 
    	    							{ 
    	    								ajaxID: 'tx_nxcaretakerservices::getActionButtons',
    	    								node:  tx.caretaker.node_info.id 
    	    							}
    	    				}); }
    	    			}
    	            }                  
    	            ]
    	}
    });