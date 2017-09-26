<?php
/**
 * Created by PhpStorm.
 * User: mvoevodskiy
 * Date: 13.06.15
 * Time: 1:33
 */

if (!class_exists('msDeliveryInterface')) {
    require_once dirname(dirname(dirname(__FILE__))) . '/model/minishop2/msdeliveryhandler.class.php';
}

class msCDEKHandler extends msDeliveryHandler implements msDeliveryInterface {

    public $localServiceName = 'msCDEK';
    /** @var msCDEK $localService */
    public $localService = null;


    public function getLocalService() {
        if (!$this->localService) {
            $srvName = $this->localServiceName;
            $srvNameLC = strtolower($srvName);
            if (!$this->localService = $this->modx->getService($srvNameLC, $srvName, $this->modx->getOption($srvNameLC . '_core_path', null, $this->modx->getOption('core_path') . 'components/' . $srvNameLC) . '/model/' . $srvNameLC. '/', array())) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not load ' . $srvName . ' class!');
                return false;
            }
        }
        return $this->localService;
    }


    /** @inheritdoc} */
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0) {
        $addCost = parent::getCost($order, $delivery, $cost);
        if ($this->getLocalService()) {
            return $this->localService->getCost($order, $delivery, $cost) + ($addCost - $cost);
        } else {
            return $addCost;
        }
    }

    public function getTime() {

        if ($this->getLocalService()) {
            return $this->localService->getTime();
        } else {
            return '';
        }
    }
}

