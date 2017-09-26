<?php
/**
 * Resolves setup-options settings
 *
 * @var xPDOObject $object
 * @var array $options
 */

if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $object->xpdo;

    $success = false;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

            $ms2 = $modx->getService('minishop2');
            $deliveries = array(
                array('name' => 'СДЕК ЭЛ Склад-Склад', 'properties' => array('tariffId' => 10)),
                array('name' => 'СДЕК ЭЛ Склад-Дверь', 'properties' => array('tariffId' => 11)),
                array('name' => 'СДЕК ЭЛ Дверь-Склад', 'properties' => array('tariffId' => 12)),
                array('name' => 'СДЕК ЭЛ Дверь-Дверь', 'properties' => array('tariffId' => 1)),
                array('name' => 'СДЕК Посылка Склад-Склад', 'properties' => array('tariffId' => 136)),
                array('name' => 'СДЕК Посылка Склад-Дверь', 'properties' => array('tariffId' => 137)),
                array('name' => 'СДЕК Посылка Дверь-Склад', 'properties' => array('tariffId' => 138)),
                array('name' => 'СДЕК Посылка Дверь-Дверь', 'properties' => array('tariffId' => 139)),
            );
            $deliveriesSaved = array();

            foreach ($deliveries as $dlvr) {

                if (!$delivery = $modx->getObject('msDelivery', array('name' => $dlvr['name']))) {
                    $delivery = $modx->newObject('msDelivery');
                    $delivery->fromArray(array(
                        'name' => $dlvr['name'],
                        'class' => 'msCDEKHandler',
                        'requires' => 'receiver,phone,city',
                        'active' => 0
                    ));
                }

                $delivery->set('properties', array_merge($dlvr['properties'], array('name' => $dlvr['name'])));
                $delivery->save();

                $deliveriesSaved[$delivery->get('id')] = $dlvr['name'];

            }

            file_put_contents(MODX_CORE_PATH . 'components/mscdek/deliveries.json', json_encode($deliveriesSaved));

            if (!copy(MODX_CORE_PATH . 'components/mscdek/libs/mscdekhandler.class.php',
                $ms2->config['customPath'] . 'delivery/mscdekhandler.class.php')
            ) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'msCDEK Handler class not copied to minishop2 folder');
            }

            if ($menu = $modx->getObject('modMenu', array('text' => 'msCDEK'))) {
                $menu->remove();
            }

            // Checking and installing required packages
//			$packages = array(
//				'pdoTools' => array(
//					'version_major' => 1,
//					'version_minor:>=' => 9,
//				)
//			);
//			foreach ($packages as $package => $options) {
//				$query = array('package_name' => $package);
//				if (!empty($options)) {
//					$query = array_merge($query, $options);
//				}
//				if (!$modx->getObject('transport.modTransportPackage', $query)) {
//					$modx->log(modX::LOG_LEVEL_INFO, 'Trying to install <b>' . $package . '</b>. Please wait...');
//
//					$response = installPackage($package);
//					if ($response['success']) {
//						$level = modX::LOG_LEVEL_INFO;
//					}
//					else {
//						$level = modX::LOG_LEVEL_ERROR;
//					}
//
//					$modx->log($level, $response['message']);
//				}
//			}
            $success = true;
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;
            break;
    }

    return $success;
}

/**
 * @param $packageName
 *
 * @return array|bool
 */
function installPackage($packageName)
{
    global $modx;

    /** @var modTransportProvider $provider */
    if (!$provider = $modx->getObject('transport.modTransportProvider', array('service_url:LIKE' => '%simpledream%'))) {
        $provider = $modx->getObject('transport.modTransportProvider', 1);
    }

//	$provider->getClient();
    $version = $modx->getVersionData();
    $productVersion = $version['code_name'] . '-' . $version['full_version'];

    $response = $provider->request('package', 'GET', array(
        'supports' => $productVersion,
        'query' => $packageName
    ));

    if (!empty($response)) {
        $foundPackages = simplexml_load_string($response->response);
        foreach ($foundPackages as $foundPackage) {
            /** @var modTransportPackage $foundPackage */
            if ($foundPackage->name == $packageName) {
                $sig = explode('-', $foundPackage->signature);
                $versionSignature = explode('.', $sig[1]);
                $url = $foundPackage->location;

                if (!downloadPackage($url,
                    MODX_CORE_PATH . 'packages/' . $foundPackage->signature . '.transport.zip')
                ) {
                    return array(
                        'success' => 0,
                        'message' => 'Could not download package <b>' . $packageName . '</b>.',
                    );
                }

                // Add in the package as an object so it can be upgraded
                /** @var modTransportPackage $package */
                $package = $modx->newObject('transport.modTransportPackage');
                $package->set('signature', $foundPackage->signature);
                $package->fromArray(array(
                    'created' => date('Y-m-d h:i:s'),
                    'updated' => null,
                    'state' => 1,
                    'workspace' => 1,
                    'provider' => $provider->id,
                    'source' => $foundPackage->signature . '.transport.zip',
                    'package_name' => $foundPackage->name,
                    'version_major' => $versionSignature[0],
                    'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
                    'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
                ));

                if (!empty($sig[2])) {
                    $r = preg_split('/([0-9]+)/', $sig[2], -1, PREG_SPLIT_DELIM_CAPTURE);
                    if (is_array($r) && !empty($r)) {
                        $package->set('release', $r[0]);
                        $package->set('release_index', (isset($r[1]) ? $r[1] : '0'));
                    } else {
                        $package->set('release', $sig[2]);
                    }
                }

                if ($package->save() && $package->install()) {
                    return array(
                        'success' => 1,
                        'message' => '<b>' . $packageName . '</b> was successfully installed',
                    );
                } else {
                    return array(
                        'success' => 0,
                        'message' => 'Could not save package <b>' . $packageName . '</b>',
                    );
                }
                break;
            }
        }
    } else {
        return array(
            'success' => 0,
            'message' => 'Could not find <b>' . $packageName . '</b> in MODX repository',
        );
    }

    return true;
}


/**
 * @param $src
 * @param $dst
 *
 * @return bool
 */
function downloadPackage($src, $dst)
{
    if (ini_get('allow_url_fopen')) {
        $file = @file_get_contents($src);
    } else {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $src);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 180);
            $safeMode = @ini_get('safe_mode');
            $openBasedir = @ini_get('open_basedir');
            if (empty($safeMode) && empty($openBasedir)) {
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            }

            $file = curl_exec($ch);
            curl_close($ch);
        } else {
            return false;
        }
    }
    file_put_contents($dst, $file);

    return file_exists($dst);
}
