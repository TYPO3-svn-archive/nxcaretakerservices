Ext.namespace("tx","tx.caretaker");

tx.caretaker.Action = Ext.extend(Ext.Panel, {

    constructor: function(config) {
		config = Ext.apply({
			autoScroll: true,
			autoHeight      : true			
		}, config);

		tx.caretaker.NodeInfo.superclass.constructor.call(this, config);
	}
});

Ext.reg( "nxcaretakerservicesaction", tx.caretaker.Action );


