<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var msCDEK $msCDEK */
$msCDEK = $modx->getService('mscdek', 'msCDEK', $modx->getOption('mscdek_core_path', null, $modx->getOption('core_path') . 'components/mscdek/') . 'model/mscdek/');
$modx->lexicon->load('mscdek:default');

// handle request
$corePath = $modx->getOption('mscdek_core_path', null, $modx->getOption('core_path') . 'components/mscdek/');
$path = $modx->getOption('processorsPath', $msCDEK->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));