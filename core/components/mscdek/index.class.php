<?php

/**
 * Class msCDEKMainController
 */
abstract class msCDEKMainController extends modExtraManagerController {
	/** @var msCDEK $msCDEK */
	public $msCDEK;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('mscdek_core_path', null, $this->modx->getOption('core_path') . 'components/mscdek/');
		require_once $corePath . 'model/mscdek/mscdek.class.php';

		$this->msCDEK = new msCDEK($this->modx);
		$this->addCss($this->msCDEK->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->msCDEK->config['jsUrl'] . 'mgr/mscdek.js');
		$this->addHtml('
		<script type="text/javascript">
			msCDEK.config = ' . $this->modx->toJSON($this->msCDEK->config) . ';
			msCDEK.config.connector_url = "' . $this->msCDEK->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('mscdek:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends msCDEKMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}