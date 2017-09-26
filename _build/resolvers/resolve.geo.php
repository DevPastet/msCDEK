<?php

if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

            /** @var msCDEK $msCDEK */
            if (!$msCDEK = $modx->getService('mscdek', 'msCDEK', $modx->getOption('mscdek_core_path', null,
                    $modx->getOption('core_path') . 'components/mscdek/') . 'model/mscdek/', array())
            ) {
                return 'Could not load msCDEK class!';
            }

            if (!$modx->getCount('msCDEKCountry') or !empty($options['update_geo'])) {
                fillDBGeo();
            }

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

function fillDBGeo()
{
    global $modx;
    /** @var msCDEK $msCDEK */
    if (!$msCDEK = $modx->getService('mscdek', 'msCDEK', $modx->getOption('mscdek_core_path', null,
            $modx->getOption('core_path') . 'components/mscdek/') . 'model/mscdek/', array())
    ) {
        return 'Could not load msCDEK class!';
    }
    if ($msCDEK->initCountries() and $msCDEK->initCities()) {
        $modx->exec('TRUNCATE ' . $modx->getTableName('msCDEKCountry'));
        $modx->exec('TRUNCATE ' . $modx->getTableName('msCDEKCity'));
        $countries = array();
        foreach ($msCDEK->countryNamesLower as $key => $country) {
            if ($c = $modx->newObject('msCDEKCountry', array('name' => $country, 'alias' => $key)) and $c->save()) {
                $cName = strtoupper($c->get('alias'));
                $countries[$cName] = $c->get('id');
            }
        }

        foreach ($msCDEK->cityNames as $country => $cities) {
            $query = 'INSERT INTO ' . $modx->getTableName('msCDEKCity') . ' (city_id, name, country) VALUES ';
            foreach ($cities as $id => $name) {
                $query .= "('" . $id . "', '" . $name . "', '" . $countries[$country] . "'), ";
            }
            $query  = substr($query, 0, -2);
            $result = $modx->exec($query);
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Added cities for "' . $country . '": ' . $result);
            if (!$result) {
                $modx->log(xpdo::LOG_LEVEL_WARN, 'Reason: ' . print_r($modx->pdo->errorInfo(), true));
            }
        }
    }
    return true;
}

return true;