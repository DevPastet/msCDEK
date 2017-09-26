<?php
/** @var msCDEK $mscdek */
$mscdek = $modx->getService('mscdek', 'msCDEK', MODX_CORE_PATH.'components/mscdek/model/mscdek/');
$ms2 = $modx->getService('minishop2');
$city = $modx->getOption('city', $scriptProperties, null);
$output = '';
$resPoints = array();
$msg = '';

if (empty($city)) {
    $order = $ms2->order->get();
    $city = $order['city'];
}

if(!empty($city) && !empty($mscdek)) {
    if(!is_int($city)) {
        $city = $mscdek->getCityId($city);
    }

    $dps = file_get_contents('https://integration.cdek.ru/pvzlist.php?type=PVZ&cityid=' . $city);
    $xml = new SimpleXMLElement($dps);
    foreach ($xml->Pvz as $pvz) {
        $resPoints[] = array('address' => (string) $pvz['Address']);
    }
    if(empty($resPoints)) {
        $msg = 'В данном городе нет пунктов выдачи';
    }
}

return json_encode(array(
    'success' => empty($msg) ? 1 : 0,
    'points' => $resPoints,
    'message' => $msg,
));