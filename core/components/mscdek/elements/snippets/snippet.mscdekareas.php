<?php
/** @var array $scriptProperties */
/** @var msCDEK $msCDEK */
if (!$msCDEK = $modx->getService('mscdek', 'msCDEK', $modx->getOption('mscdek_core_path', null, $modx->getOption('core_path') . 'components/mscdek/') . 'model/mscdek/', $scriptProperties)) {
    return 'Could not load msCDEK class!';
}

$onlyInit = $modx->getOption('onlyInit', $scriptProperties, false);
if ($onlyInit) {
    return;
}

if (!$ms2 = $modx->getService('minishop2')) {
    return 'Could not load miniShop2 class!';
}

// Do your snippet code here. This demo grabs 5 items from our custom table.
$tpl = $modx->getOption('tpl', $scriptProperties, 'option');
$type = $modx->getOption('type', $scriptProperties, 'city');
$query = $modx->getOption('query', $scriptProperties, 'city');
$format = $modx->getOption('format', $scriptProperties, 'html');
$sortby = $modx->getOption('sortby', $scriptProperties, 'name');
$sortdir = $modx->getOption('sortdir', $scriptProperties, 'ASC');
$limit = $modx->getOption('limit', $scriptProperties, 5);
$outputSeparator = $modx->getOption('outputSeparator', $scriptProperties, "\n");
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);

$outputAsHtml = $format == 'html';
$output = $outputAsHtml ? '' : array();
$areas = array();
$areasRaw = $msCDEK->getAreas($type, $query);
$order = $ms2->order->get();
$selectedArea = $order[$type];

//$modx->log(1, '>> areas: '. print_r($areasRaw,1));
$areas = array_values($areasRaw);
sort($areas);
foreach ($areas as $area) {
    $selected = $selectedArea == $area ? 'selected' : '';
    if ($outputAsHtml) {
        $areaOutput = $modx->getChunk($tpl, array('area' => $area, 'selected' => $selected));
    } else {
        $areaOutput = array('name' => $area, 'selected' => $selected);
    }
    if (mb_strlen($area) == mb_strlen($query)) {
        if ($outputAsHtml) {
            $output  = $areaOutput . $output;
        } else {
            $output = array_merge(array($areaOutput), $output);
        }
    } else {
        if ($outputAsHtml) {
            $output .= $areaOutput;
        } else {
            $output[] = $areaOutput;
        }
    }
}

if (!empty($toPlaceholder)) {
    // If using a placeholder, output nothing and set output to specified placeholder
    $modx->setPlaceholder($toPlaceholder, $output);

    return '';
}
// By default just return output
//$modx->log(1, $output);
return $outputAsHtml ? $output : $modx->toJSON($output);
