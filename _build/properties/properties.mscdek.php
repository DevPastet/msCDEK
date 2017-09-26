<?php

$properties = array();

$tmp = array(
	'tpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.msCDEK.delivery',
	),
	'tariffId' => array(
		'type' => 'textfield',
		'value' => '1',
	),
	'weight' => array(
		'type' => 'textfield',
		'value' => '1'
	),
//	'cost' => array(
//		'type' => 'textfield',
//		'value' => '0'
//	),
	'to' => array(
		'type' => 'textfield',
		'value' => '',
	),
//    'weightInKg' => array(
//        'type' => 'combo-boolean',
//        'value' => true,
//    ),
	'toPlaceholder' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => PKG_NAME_LOWER . '_prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;