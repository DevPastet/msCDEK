<?php
class msCDEKCity extends xPDOSimpleObject {

    public $points;

    /**
     * This function loading current city delivery points
     *
     * @return array
     */
    public function loadPoints() {
        if(!$this->points) {
            $this->points = array();
            /* @var msCDEKDeliveryPoint $point */
            $points = $this->getMany('Points');
            foreach($points as & $point) {
                $this->points[] = $point->toArray();
            }
        }

        return $this->points;
    }

    /**
     *
     * This function return current city delivery points
     *
     * @return array
     */
    public function getPoints() {
        return $this->loadPoints();
    }
}