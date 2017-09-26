<?php

/**
 * The base class for msCDEK.
 */
class msCDEK {
    /* @var modX $modx */
    public $modx;
    /** @var minishop2 ms2 */
    public $ms2;
    /** @var CalculatePriceDeliveryCdek $cdek */
    public $cdek = null;
    public $deliveryHandler = 'msCDEKHandler';
    public $cityNames;
    public $cityNamesLower;
    public $cityIds = array();
    public $countryNames;
    public $countryNamesLower;
    public $country = '';
    public $countryDefault = 'RUS';
    public $initialized = false;
    public $dpTariffs = array(136, 138, 234, 301, 302, 291, 295, 243, 245, 247, 5, 10, 15, 17, 62, 63);

    /* NEW */

    public $countries;
    public $cities;


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('mscdek_core_path', $config, $this->modx->getOption('core_path') . 'components/mscdek/');
        $assetsUrl = $this->modx->getOption('mscdek_assets_url', $config, $this->modx->getOption('assets_url') . 'components/mscdek/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'name_prefix' => $this->modx->getOption('mscdek_name_prefix'),
            'weight_in_kg' => $this->modx->getOption('ms2_delivery_weight_in_kg'),
            'default_weight' => $this->modx->getOption('mscdek_default_weight', null, 1),
            'default_size' => $this->modx->getOption('mscdek_default_size', null, 1),
            'find_size' => $this->modx->getOption('mscdek_find_size', null, 0),
            'field_width' => $this->modx->getOption('mscdek_field_width', null, 1),
            'field_height' => $this->modx->getOption('mscdek_field_height', null, 1),
            'field_depth' => $this->modx->getOption('mscdek_field_depth', null, 1),
            'cities_list' => $corePath . $this->modx->getOption('mscdek_cities_list'),
            'countries_list' => $corePath . $this->modx->getOption('mscdek_countries_list'),
            'login' => $this->modx->getOption('mscdek_login', null, ''),
            'password' => $this->modx->getOption('mscdek_password', null, ''),
            'isAjax' => !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest',
            'returnTime' => $this->modx->getOption('mscdek_return_time', null, true),

        ), $config);

        $this->modx->addPackage('mscdek', $this->config['modelPath']);
        $this->modx->lexicon->load('mscdek:default');

        $this->initialize();
    }

    public function initialize() {
        if (!$this->initialized) {
            $this->ms2 = $this->modx->getService('minishop2');
            if (!$this->ms2->initialized) {
                $this->ms2->initialize($this->modx->context->get('key'), array('json_response' => true));
            }

            $this->loadCountries();
            $this->loadCities();

            if (is_object($this->ms2->order)) {
                $orderData = $this->ms2->order->get();
            } else {
                $orderData = array();
            }
            $countries = array_flip($this->countryNamesLower);
//
            if (isset($orderData['country']) and $orderData['country'] != '') {
                $this->country = strtoupper($countries[strtolower($orderData['country'])]);
            } else {
                $this->country = $this->countryDefault;
            }
//            $this->modx->log(1, '$orderData: '.print_r($_POST,1));
//            $this->modx->log(1, 'this->country: '.$this->country);
//            $this->modx->log(1, '$orderData: '.print_r($orderData,1));

            if (isset($orderData['city']) and !empty($orderData['city']) and !in_array(strtolower($orderData['city']), $this->cityNames[$this->country])) {
//                $this->modx->log(1,'incorrect country <--> city');
                $orderData['city'] = '';
                $this->ms2->order->set($orderData);
            }
            $deliveryDpIds = array();
//            $deliveryDp = array();
            $deliveryIds = array();
            /** @var msDelivery[] $deliveries */
            $deliveries = $this->modx->getCollection('msDelivery', array('class' => $this->deliveryHandler));
            foreach ($deliveries as $d) {
                $deliveryIds[] = $d->get('id');
                $p = $d->get('properties');
                if (isset($p['tariffId']) and in_array($p['tariffId'], $this->dpTariffs)) {
                    $deliveryDpIds[] = $d->get('id');
                }
            }

            $this->modx->regClientScript('<script type="text/javascript">msCDEKConfig = ' . $this->modx->toJSON(array(
                'deliveries' => $deliveryIds,
                'deliveriesDP' => $deliveryDpIds,
            )) . '</script>', true);

            if ($js = trim($this->modx->getOption('mscdek_frontend_js'))) {
                if (!empty($js) && preg_match('/\.js/i', $js)) {
//                $this->modx->regClientScript(preg_replace(array('/^\n/', '/\t{7}/'), '', '
//							<script type="text/javascript">
//								if(typeof jQuery == "undefined") {
//									document.write("<script src=\"'.$this->config['jsUrl'].'web/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
//								}
//							</script>
//							'), true);
                    $this->modx->regClientScript(str_replace('[[+jsUrl]]', $this->config['jsUrl'], $js));
                }
            }

            if ($css = trim($this->modx->getOption('mscdek_frontend_css'))) {
                if (!empty($css) && preg_match('/\.css/i', $css)) {
//                $this->modx->regClientScript(preg_replace(array('/^\n/', '/\t{7}/'), '', '
//							<script type="text/javascript">
//								if(typeof jQuery == "undefined") {
//									document.write("<script src=\"'.$this->config['jsUrl'].'web/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
//								}
//							</script>
//							'), true);
                    $this->modx->regClientCSS(str_replace('[[+cssUrl]]', $this->config['cssUrl'], $css));
                }
            }
            $this->initialized = true;
        }
    }

    public function deliveryOwner() {

        $orderData = $this->ms2->order->get();
        if ($delivery = $this->modx->getObject('msDelivery', $orderData['delivery'])) {
            if ($delivery->get('class') == $this->deliveryHandler) {
                if ($delivery->get('country')) $this->country = $delivery->get('country');
                return true;
            }
        }

        return false;
    }



    public function getCost(msOrderInterface $order, msDelivery $delivery, $cart_cost = 0) {

        if ($delivery->get('class') !== $this->deliveryHandler) return '';

        $cart = $this->ms2->cart->get();
        $goodsDimensions = $this->getGoodsDimensions($cart);
        $orderData = $order->get();
        $sendingData = $this->getSendingData($this->getTariffId($delivery), $goodsDimensions, $orderData['city']);
        if (is_array($sendingData)) {
            if(empty($sendingData['cost'])) $sendingData['cost'] = 0;
            $cart_cost += $sendingData['cost'];
        }
        return $cart_cost;
    }


    public function getTime()
    {

        $orderData = $this->ms2->order->get();
        $result = '';
        $success = false;
        /** @var msDelivery $delivery */
        if ($orderData['city'] and $delivery = $this->modx->getObject('msDelivery', $orderData['delivery'])) {
            if ($delivery->get('class') !== $this->deliveryHandler) {
                return false;
            }
            $cart = $this->ms2->cart->get();
            $goodsDimensions = $this->getGoodsDimensions($cart);
            $sendingData = $this->getSendingData($this->getTariffId($delivery), $goodsDimensions, $orderData['city']);
            if(empty($sendingData['error'])) {
                if ($sendingData['deliveryPeriodMin'] != -1) {
                    if ($this->config['returnTime']) {
                        $time = $sendingData['deliveryPeriodMin'] == $sendingData['deliveryPeriodMax']
                            ? $sendingData['deliveryPeriodMin']
                            : $sendingData['deliveryPeriodMin'] . ' - ' . $sendingData['deliveryPeriodMax'];
                        $result = $this->modx->lexicon(
                            'mscdek_time',
                            array('time' => $time, 'city' => $orderData['city'])
                        );
                        $result .= '<br>Стоимость доставки: ' . $sendingData['cost'] . ' ' . $this->modx->lexicon('ms2_frontend_currency');
                        $success = true;
                    }
                } else {
                    $result = $this->modx->lexicon('mscdek_time_nf', array('city' => $orderData['city']));
                }
            } else {
                $result = $sendingData['error'];
            }
        }
        if ($success) {
            return $this->success(array(), $result);
        } else {
            return $this->error(array(), $result);
        }
    }


    /**
     * @param msDelivery $delivery
     * @return string
     */
    public function getTariffId($delivery) {

        $props = $delivery->get('properties');

        if (isset($props['tariffId'])) return $props['tariffId'];
        else return 0;

    }


    public function getSendingData($tariffId, $weightOrGoodsDimensions, $to) {

        if (is_array($weightOrGoodsDimensions)) {
            $goodsDimensions = $weightOrGoodsDimensions;
        } else {
            $goodsDimensions = array('weight' => $weightOrGoodsDimensions);
        }
        $error = null;
        $errorFields = array();
//        $size = $this->config['default_size'];
        $result = array();

        $tariffId = (int) $tariffId;
        $cityFrom = $this->modx->getOption('mscdek_from_cityid');
        $cityFromId = (int) $cityFrom;
        $cityToId = is_numeric($to) ? $to : (int) $this->getCityId($to);

        if(!$this->checkCityStatus($cityFromId, $cityToId, $tariffId)) {
            $result = array('error' => $this->modx->lexicon('mscdek_frontend_err_city'), 'block_submit_btn' => true);
        } else {
            if (!($cdek = & $this->cdek)) {
                $this->getCdek();
                $cdek = & $this->cdek;
            }

            if ($tariffId) {
                $cdek->setTariffId($tariffId);
            } else {
                $errorFields['tariff'] = array('orig' => $tariffId, 'real' => $tariffId);
            }
            if ($cityFromId)  {
                $cdek->setSenderCityId($cityFromId);
            } else {
                $errorFields['from'] = array('orig' => $cityFrom, 'real' => $cityFromId);
            }

            if ($cityToId) $cdek->setReceiverCityId($cityToId);
            else $errorFields['to'] = array('orig' => $to, 'real' => $cityToId);

            if ($this->config['login'] and $this->config['password']) $this->cdek->setAuth($this->config['login'], $this->config['password']);

            $cdekGoods = $cdek->getGoodslist();
            if (!empty($cdekGoods)) {
                $cdek->clearGoodslist();
            }
            foreach ($goodsDimensions as $good) {
                //        $this->cdek->addGoodsItemByVolume($weight, $this->config['default_size']);
                $cdek->addGoodsItemBySize($good['weight'], $good['depth'], $good['width'], $good['height']);
            }


//            $this->log('to: '. $to);
//            $this->log('to: '. $cityToId);
//            $this->log('tariffId: '. $tariffId);
//            $this->log('senderCity: '. $cityFrom);
//            $this->log('weight: '. $weight);
//            $this->log('size: '. $size);
            if (empty($errorFields)) {
                if ($cdek->calculate()) {

                    $delivery = $cdek->getResult();
                    $result = $delivery['result'];
                    $result['cost'] = $result['price'];
                    if ($result['deliveryPeriodMin'] == $result['deliveryPeriodMax']) $result['time'] = $result['deliveryPeriodMin'];
                    else $result['time'] = $result['deliveryPeriodMin'] . ' - ' . $result['deliveryPeriodMax'];
                    unset($result['price']);
                } else {
                    $error = $cdek->getError();
 //                   if($error['error'][0]['code'] == 3) {
 //                       $this->disableCity($cityFromId, $cityToId, $tariffId);
 //                       $result = array('error' => 'В данный город доставка не доступна!');
 //                   } else {
                        $this->log('Произошла ошибка при получении данных от СДЕК: '. print_r($error['error'][0]['text'],1));
                        $result = array('error' => $this->modx->lexicon('mscdek_time_na')); // Код ошибки: '.$error['error'][0]['code']);
 //                   }
                }
            } else {

//                $this->log('Errors: '.print_r($errorFields, 1));
                foreach ($errorFields as $k => $v) {
                    $this->log('Доставка СДЕК. Неправильно указано поле '. $k .': '.$v['orig'].' ('. $v['real'] .')');
                    $result['error_'.$k] = 'Неправильно указано поле '. $k .': '.$v['orig'].' ('. $v['real'] .')';
                }
                $result['error'] = $this->modx->lexicon('mscdek_time_na');
            }
        }
    //        $this->modx->log(1, $this->cdek->getLog());

        return $result;

    }


    public function getGoodsDimensions($cart)
    {
        $defaultSize = $this->config['default_size'];
        $defaultWeight = $this->config['default_weight'];
        $goodsDimensions = array();

        foreach ($cart as $good) {
            $dimensions = array();
            $weight = $good['weight'];
            if (!$this->config['weight_in_kg']) {
                $weight = $weight / 1000;
            }
            if (!$weight) {
                $weight = $defaultWeight;
            }
            $dimensions['weight'] = $weight;
            foreach (array('width', 'height', 'depth') as $f) {
                $value = 0;
                $field = $this->config['field_' . $f];
                if ($this->config['find_size']) {
//                    $this->modx->log(1, print_r($this->config, 1));
                    if (isset($good['options'][$field])) {
                        $value = $good['options'][$field];
                    }
                    /** @var msProduct $product */
                    if (!$value and $product = $this->modx->getObject('msProduct', $good['id'])) {
                        $value = $product->get($field);
                        $criteria = array('product_id' => $good['id'], 'key' => $field);
                        if (!$value and $option = $this->modx->getObject('msProductOption', $criteria)) {
                            $value = $option->get('value');
                            if (!$value) {
                                $value = $product->getTVValue($field);
                            }
                        }
                    }
                }
                $dimensions[$field] = !empty($value) ? $value: $defaultSize;
            }
            $goodsDimensions[] = $dimensions;

        }
        return $goodsDimensions;

    }

    public function getCities(/*$limit = 0*/) {

        if ($this->country) {
            return $this->cityNames[$this->country];
        } else return array();

    }

    public function getCityId($cityName) {

        return array_search(mb_strtolower($cityName), $this->cityNamesLower[strtolower($this->country)]);

    }

    public function getAreas($type = 'city', $query = '') {
        $limit = 50;
        $output = array();
        $foundEqual = false;
        switch ($type) {
            case 'country':
                $output = $this->countryNames;
                break;

            case 'city':
                if (!empty($query)) {
                    foreach ($this->cityNames[$this->country] as $city) {
                        if (mb_stripos($city, $query) !== false) {
                            if (mb_strlen($city) == mb_strlen($query)) {
                                $foundEqual = true;
                                $output[] = $city;
                            } elseif (count($output) < $limit) {
                                $output[] = $city;
                            }
                        }
                        if (count($output) >= $limit and $foundEqual) {
                            break;
                        }
                    }
                } else {
                    $output = $this->cityNames[$this->country];
                }
                break;
            default:
                break;
        }
        return $output;
    }


    // *deprecated function //
    public function initCountries() {
        if ($countries = file_get_contents($this->config['countries_list'])) {
            $this->countryNames = unserialize($countries);
            foreach($this->countryNames as $id => $name) {
                $this->countryNamesLower[$id] = strtolower($name);
            }
            return true;
        } else return false;
    }

    // *deprecated function //
    public function initCities() {

        if ($cities = file_get_contents($this->config['cities_list'])) {

            $this->cityNames = unserialize($cities);
//            exit('<pre>'.print_r($this->cityNames,1));
            foreach ($this->cityNames as $country => $cities) {
                $countryCities = array();
                foreach ($cities as $id => $name) {
                    $countryCities[$id] = mb_strtolower($name);
                }
                $this->cityNamesLower[$country] = $countryCities;
//            $this->modx->log(1,print_r($this->cityNamesLower,1));

//            $this->cityNamesLower = array_walk(&$this->cityNames, 'strtolower');
                //$this->cityIds = array_flip($this->cityNames);
            }
            return true;

        } else return false;

    }

    /**
     * get CDEK Handler
     *
     * @return null
     */
    public function getCdek() {

        if (!$this->cdek) {
            $this->cdek = $this->modx->getService('calculatepricedeliverycdek', 'CalculatePriceDeliveryCdek', $this->config['corePath'].'libs/');
        }
        return $this->cdek;

    }

    /**
     * load from DB countries list
     *
     * @return array
     */
    public function loadCountries() {
        if(!isset($this->countryNames)) {
            $this->countryNames = array();

            foreach($this->modx->getCollection('msCDEKCountry') as $country) {
                $this->countryNames[$country->alias] = $country->name;
            }

            $this->countryNamesLower = $this->countryNames;
        }

        return $this->countryNames;
    }

    /**
     *  Load from DB cities list
     *
     * @param null $countries
     * @return mixed
     */
    public function loadCities($countries = null) {
        if(!isset($this->cityNames)) {
            if(!$countries) {
                $countries = array_keys($this->loadCountries());
            }
            if(!is_array($countries)) $countries = (array)$countries;

            $this->cities = array();

            foreach($countries as & $key) {
                if ($country = $this->modx->getObject('msCDEKCountry', array('alias' => $key))) {
                    $cities = $country->loadCities();
                    $citiesLower = array();
                    foreach ($cities as $id => $city) {
                        $citiesLower[$id] = mb_strtolower($city);
                    }
                    $this->cityNamesLower[$key] = $citiesLower;
                    $key = $this->prepareKey($key);
                    $this->cityNames[$key] = $cities;
                }
            }
        }

        return $this->cityNames;
    }

    /**
     * Preparing Country or City key for something
     *
     * @param string $key
     * @return string
     */
    public function prepareKey($key = 'rus') {
        return strtoupper($key);
    }

    public function enableCity($city_from, $city_to, $tariff) {
        $this->setCityStatus($city_from, $city_to, $tariff, 1);
    }

    public function disableCity($city_from, $city_to, $tariff) {
        $this->setCityStatus($city_from, $city_to, $tariff, 0);
    }

    /**
     * Установка возможности доставки в город для текущего тарифа
     *
     * @param $city_from
     * @param $city_to
     * @param $tariff
     * @param $status
     */
    public function setCityStatus($city_from, $city_to, $tariff, $status) {
        $city_attr = $this->getCityStatus($city_from, $city_to, $tariff);
        $city_attr->set('active', $status);
//        $city_attr->save();
    }

    /**
     * Проверка возможности доставки в город по текущему тарифу
     *
     * @param $city_from
     * @param $city_to
     * @param $tariff
     * @return mixed
     */
    public function checkCityStatus($city_from, $city_to, $tariff) {
//        $city_attr = $this->getCityStatus($city_from, $city_to, $tariff);
//        return $city_attr->get('active');
        return true;
    }

    /**
     * Получение объекта из базы возможности доставки
     * На будущее, в текущем состоянии она не учитывает параметры заказа, вроде веса и прочего.
     *
     * @param $city_from
     * @param $city_to
     * @param $tariff
     * @return null|object
     */
    public function getCityStatus($city_from, $city_to, $tariff) {
//        if(!$city_attr = $this->modx->getObject('msCDEKDeliveryCity', array('city_from'=>$city_from, 'city_to' => $city_to, 'tariff'=>$tariff))) {
            $city_attr = $this->modx->newObject('msCDEKDeliveryCity');
            $city_attr->fromArray(array(
                'city_from' => $city_from,
                'city_to' => $city_to,
                'tariff' => $tariff,
                'active' => true,
            ));
 //       }
        return $city_attr;
    }


    /**
     * Получение списка пунктов выдачи в городе
     *
     * @param $city
     * @return array
     */
    public function getCityPoints($city) {
        /** @var msCDEKCity $city */
        if($city = $this->modx->getObject('msCDEKCity', array('city_id'=> $city))) {
            return $city->getPoints();
        } else {
            return array();
        }
    }

    /**
     * Проверка доставки для понимания необходимости вывода списка пунктов выдачи
     *
     * @param $delivery
     */
    public function checkDeliveryForPoints($delivery) {
        $good_deliveries = array();
        return true;
    }

        /**
     * This method returns an error of the order
     *
     * @param string $message A lexicon key for error message
     * @param array $data .Additional data, for example cart status
     *
     * @return array|string $response
     */

    public function error($data = array(), $message = '')
    {
        $response = array(
            'success' => false,
            'message' => $message,
            'data' => $data,
        );

        return json_encode($response);
    }


    /**
     * This method returns an success of the order
     *
     * @param string $message A lexicon key for success message
     * @param array $data .Additional data, for example cart status
     *
     * @return array|string $response
     */
    public function success($data = array() ,$message = '')
    {
        $response = array(
            'success' => true,
            'message' => $message,
            'data' => $data,
        );

        return json_encode($response);
    }

    /**
     * add anything in modx logs.
     *
     * @param $message
     */
    public function log($message, $error_type = 1) {
        $this->modx->log($error_type, $message);
    }
}