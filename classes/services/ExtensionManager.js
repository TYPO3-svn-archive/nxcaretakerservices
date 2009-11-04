
	 var reader = new Ext.data.JsonReader({		 
        idProperty: "extkey, version",
       	fields: ["title", "extkey", "version", "authorname","reviewstate","description", "state", "uploadcomment"],		
       	root: "exts",
       totalProperty: "totalCount",
        remoteGroup:true        
    });
	 
	   var store =   new Ext.data.GroupingStore({
  			storeId : "jstore",         
  			reader: reader,												
           proxy: new Ext.data.HttpProxy({url: backpath + "ajax.php?ajaxID=tx_nxcaretakerservices::doaction&node=" + nodeid + "&service=" + service + "&method=TER&actionid=" + action}),
           
           sortInfo: {field: "extkey", direction: "ASC"},									            	
           groupField: "extkey"
       });
	   
	   var expander = new Ext.grid.RowExpander({
	        tpl : new Ext.Template(
	            '<br><p><b> Description:</b> {description}<br><br><b> Uploadcomment:</b> {uploadcomment}</p><br>'	            
	        )
	    });

	   
	   var grid = new Ext.grid.GridPanel({	
		   	id			:	"terGrid",
	        height		:	600,
	        forceFit	:	true,
	        title		:	"Typo3 extension repository",
	        store		: 	store,								        								        
	        loadMask	: 	true,
	        plugins		:	expander,
	        columns		:	[ 
	          expander,
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
	       
	        view: new Ext.grid.GroupingView({
	            forceFit			:	true,	            
	            startCollapsed 		: 	true,
	            hideGroupedColumn	: 	true,
	            enableRowBody 		:	true
//	            showPreview 		:	false,
//	            getRowClass 		: 	function(record, rowIndex, p, store){
//	                if(this.showPreview){
//	                    p.body = "<p><Div style=\"padding:10px 5px 5px 15px;\"><h4>Description</h4>"+record.data.description+ (record.data.uploadcomment ? "<h4>Uploadcomment</h4>"+record.data.uploadcomment : "")+"</Div></p>";								                    
//	                }
//	                
//	            }
	        }),
	    	sm: new Ext.grid.CheckboxSelectionModel({
	    				singleSelect	:	true,
	    				listeners		: 	{ selectionchange: function(sm) {				              
				           				var addButton = Ext.getCmp("addFromTerButton");
				           				if(sm.selections.items.length > 0) 	addButton.enable();
				           				else addButton.disable();
	    								}
	    							}
	    						}),
	        bbar: new Ext.PagingToolbar({
	            pageSize: 100,
	            store: store,
	            displayInfo: true,
	            displayMsg: "Displaying extensions {0} - {1} of {2}",
	            emptyMsg: "No extensions to display",
//				 items:[
//                "-", {
//	                pressed: false,
//	                enableToggle:true,
//	                text: "Show description",								                
//	                toggleHandler: function(btn, pressed){
//	                    var view = grid.getView();
//	                    view.showPreview = pressed;
//	                    view.refresh();
//	                }
//	            }]
	            
	        })
	    });
	   
	    // render it
	    var win = new Ext.Window({
	    	layout:"fit",
           closeAction:"hide",						                
           width: 775,
           height:750,						                
           plain: true,
           modal: true,										
           items: new Ext.FormPanel({					       												       											        								        
			        frame:true,				
			        itemId: "filterIdpp",	
			        items: [grid,
			        {		
			        			style:{marginTop : "15px", marginLeft:"15px"},
				                columnWidth:.5,
				                itemId: "filterIdp",	
				                labelWidth: 100,
				                defaultType: "textfield",	
				                layout: "form",
				                items: [{
			        
							        	width		:	230,
							        	fieldLabel	: 	"Filter",
										itemId: "filterId",														        	
							            name		: 	"filter",
										anchor		:	"95%"
			      						}]	
						   
			        }],

           buttons: [
           {
               text		: 	"Filter",
               handler	: 	function(){
		               	var filter = win.getComponent("filterIdpp").getComponent("filterIdp").getComponent("filterId");
		               	var filterstr = filter.getRawValue();
		               	store.baseParams.filter = filterstr;
		               	store.load({params:{start:0, limit:100}});    
               		}
           },{
               text		: 	"Add selected extension",
               id		:	"addFromTerButton",
               disabled	:	true,
               handler	: 	function(){
                  
                   
					if(grid.getSelectionModel().hasSelection()){				            					
					
					var selection = grid.getSelectionModel().getSelected().get("extkey") +","+ grid.getSelectionModel().getSelected().get("version");
                   
                   var viewpanel = Ext.getCmp("nxcaretakerAction");
						viewpanel.removeAll()	;
						viewpanel.add({	html : "<img src="+ img +"style=\"width:16px;height:16px;\" align=\"absmiddle\">" });				
						viewpanel.doLayout();
               	win.hide();
						Ext.Ajax.request({
  							url: backpath + "ajax.php",
  							success : function (response, opts){											
										Ext.MessageBox.alert("Result:", response.responseText);
															
						Ext.Ajax.request({
  							url: backpath + "ajax.php",
  							success : function (response, opts){											
									           								
								var jsonData = Ext.util.JSON.decode(response.responseText);
																							
								viewpanel.removeAll();
								viewpanel.add(jsonData);
								viewpanel.doLayout(); 	
													       								       								
								}     , 
  							params: { 
      							ajaxID: "tx_nxcaretakerservices::doaction",
      							back_path 	: 	backpath,
      							node		:   nodeid,
      							service		:  	service,
      							actionid	:   action        							               							             							
   								}
							});           																       								       								
								}     , 
  							params: { 
      							ajaxID: "tx_nxcaretakerservices::doaction",
      							back_path 	: 	backpath,
      							node		:   nodeid,
      							service		:   service,
      							actionid	:   action,
      							method		: 	"fetch," + selection           							             							
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
					store.load({params:{start:0, limit:100}});
				