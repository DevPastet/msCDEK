<?php
class msCDEKCountry extends xPDOSimpleObject {
    public $cities = array();

    /**
     * GETTING CITIES LIST!
     * @return array
     */
    public function loadCities() {
        if(empty($this->cities)) {
            $q = $this->xpdo->newQuery('msCDEKCity');
            $q->where(array('country' => $this->get('id')));
            $q->prepare();
            $q->stmt->execute();
            $cities = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($cities as $city) {
                $this->cities[$city['msCDEKCity_city_id']] = $city['msCDEKCity_name'];
            }
        }

        return $this->cities;
    }

    /**
     * @param array|string $k
     * @param null $format
     * @param null $formatTemplate
     * @return array|mixed
     */
    public function get($k, $format = null, $formatTemplate = null)
    {
        if(is_array($k)) {
            $value = array();
            foreach($k as $key) {
                $value[$key] = $this->get($key, $format, $formatTemplate);
            }

            return $value;
        }

        if($k == 'cities') {
            return $this->loadCities();
        } else {
            return parent::get($k, $format, $formatTemplate);
        }
    }
}