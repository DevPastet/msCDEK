<?php

/**
 * Remove an Items
 */
class msCDEKItemRemoveProcessor extends modObjectProcessor {
	public $objectType = 'msCDEKItem';
	public $classKey = 'msCDEKItem';
	public $languageTopics = array('mscdek');
	//public $permission = 'remove';


	/**
	 * @return array|string
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		$ids = $this->modx->fromJSON($this->getProperty('ids'));
		if (empty($ids)) {
			return $this->failure($this->modx->lexicon('mscdek_item_err_ns'));
		}

		foreach ($ids as $id) {
			/** @var msCDEKItem $object */
			if (!$object = $this->modx->getObject($this->classKey, $id)) {
				return $this->failure($this->modx->lexicon('mscdek_item_err_nf'));
			}

			$object->remove();
		}

		return $this->success();
	}

}

return 'msCDEKItemRemoveProcessor';