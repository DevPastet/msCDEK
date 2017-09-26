<?php

/**
 * The home manager controller for msCDEK.
 *
 */
class msCDEKHomeManagerController extends msCDEKMainController {
	/* @var msCDEK $msCDEK */
	public $msCDEK;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('mscdek');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addCss($this->msCDEK->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->msCDEK->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addJavascript($this->msCDEK->config['jsUrl'] . 'mgr/misc/utils.js');
		$this->addJavascript($this->msCDEK->config['jsUrl'] . 'mgr/widgets/items.grid.js');
		$this->addJavascript($this->msCDEK->config['jsUrl'] . 'mgr/widgets/items.windows.js');
		$this->addJavascript($this->msCDEK->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->msCDEK->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "mscdek-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->msCDEK->config['templatesPath'] . 'home.tpl';
	}
}