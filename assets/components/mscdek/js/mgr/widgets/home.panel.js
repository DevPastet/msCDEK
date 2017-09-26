msCDEK.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'mscdek-panel-home',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('mscdek') + '</h2>',
			cls: '',
			style: {margin: '15px 0'}
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			hideMode: 'offsets',
			items: [{
				title: _('mscdek_items'),
				layout: 'anchor',
				items: [{
					html: _('mscdek_intro_msg'),
					cls: 'panel-desc',
				}, {
					xtype: 'mscdek-grid-items',
					cls: 'main-wrapper',
				}]
			}]
		}]
	});
	msCDEK.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(msCDEK.panel.Home, MODx.Panel);
Ext.reg('mscdek-panel-home', msCDEK.panel.Home);
