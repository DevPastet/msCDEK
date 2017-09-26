<?php

/**
 * Create an Item
 */
class msCDEKItemCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'msCDEKItem';
	public $classKey = 'msCDEKItem';
	public $languageTopics = array('mscdek');
	//public $permission = 'create';


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('mscdek_item_err_name'));
		}
		elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
			$this->modx->error->addField('name', $this->modx->lexicon('mscdek_item_err_ae'));
		}

		return parent::beforeSet();
	}

}

return 'msCDEKItemCreateProcessor';