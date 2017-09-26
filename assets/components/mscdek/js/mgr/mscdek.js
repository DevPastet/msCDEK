var msCDEK = function (config) {
	config = config || {};
	msCDEK.superclass.constructor.call(this, config);
};
Ext.extend(msCDEK, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('mscdek', msCDEK);

msCDEK = new msCDEK();