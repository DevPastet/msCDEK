<?php
/** @var array $scriptProperties */

// Do your snippet code here. This demo grabs 5 items from our custom table.
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.msCDEK.delivery');
$tariffId = $modx->getOption('tariffId', $scriptProperties, '1');
$weight = $modx->getOption('weight', $scriptProperties, '1');
$to = $modx->getOption('to', $scriptProperties, 'Москва');
//$cost = $modx->getOption('cost', $scriptProperties, '0');
//$weight_in_kg = $modx->getOption('weightInKg', $scriptProperties, $modx->getOption('ms2_delivery_weight_in_kg', null, true));
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);

/** @var msCDEK $msCDEK */
if (!$msCDEK = $modx->getService('mscdek', 'msCDEK', $modx->getOption('mscdek_core_path', null, $modx->getOption('core_path') . 'components/mscdek/') . 'model/mscdek/', $scriptProperties)) {
	return 'Could not load msCDEK class!';
}

$sendingData = $msCDEK->getSendingData($tariffId, $weight, $to);
$output = $modx->getChunk($tpl, $sendingData);

// Output
if (!empty($toPlaceholder)) {
	// If using a placeholder, output nothing and set output to specified placeholder
	$modx->setPlaceholder($toPlaceholder, $output);

	return '';
}
// By default just return output
return $output;
