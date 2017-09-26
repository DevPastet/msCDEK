<?php

switch ($modx->event->name) {
    case 'OnHandleRequest':
        if (isset($_POST['mscdek_action'])) {
            $action = $_POST['mscdek_action'];
            $response = '';
            $deliveryHandler = null;

            /** @var minishop2 $ms2 */
            if ($ms2 = $modx->getService('minishop2') and $ms2->initialize() and $orderData = $ms2->order->get()) {

                $delivery = $modx->getObject('msDelivery', $orderData['delivery']);
                $deliveryHandlerClass = $delivery->get('class');
                $deliveryHandlerClassLC = strtolower($deliveryHandlerClass);

                if (!$deliveryHandlerClass) {
                    return;
                }

                if ($modx->loadClass($deliveryHandlerClass,$ms2->config['customPath'].'delivery/',false, true)) {
                    $deliveryHandler = new $deliveryHandlerClass ($delivery);
                }

                if ($deliveryHandler) {
                    switch ($action) {
                        case 'delivery/gettime':
                            if (method_exists($deliveryHandler, 'getTime')) {
                                $response = $deliveryHandler->getTime();
                            } else {
                                $modx->log(xPDO::LOG_LEVEL_ERROR, 'Method getTime() not exists in class '. $deliveryHandlerClass);
                            }
                            break;

                        case 'delivery/getcities':
                            $q = isset($_POST['cityName']) ? $modx->sanitizeString($_POST['cityName']) : '';
                            $format = isset($_POST['format']) ? $modx->sanitizeString($_POST['format']) : 'html';
                            $response = $modx->runSnippet('msCDEKAreas', array('type' => 'city', 'query' => $q, 'format' => $format));
                            break;
                        case 'delivery/getPoints':
                                $response = $modx->runSnippet(
                                    'msCDEKPoints',
                                    array('city'=>isset($_POST['city']) ? $_POST['city'] : '')
                                );
                            break;
                    }
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Class '.$deliveryHandlerClass. ' not exists');
                }

            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'Problem with getting minishop2 service or order data is false');
            }

            @session_write_close();
            exit($response);
        }
        break;

    case 'msOnBeforeCreateOrder':
        if (isset($_POST['deliveryPoint'])) {
            /** @var msOrder $msOrder */
            $comment = $msOrder->getOne('Address')->get('comment');
            $comment = $modx->sanitizeString($_POST['deliveryPoint']) . PHP_EOL . PHP_EOL . $comment;
            $msOrder->getOne('Address')->set('comment', $comment);
        }

}