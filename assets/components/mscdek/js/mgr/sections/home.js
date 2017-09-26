msCDEK.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'mscdek-panel-home', renderTo: 'mscdek-panel-home-div'
		}]
	});
	msCDEK.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(msCDEK.page.Home, MODx.Component);
Ext.reg('mscdek-page-home', msCDEK.page.Home);