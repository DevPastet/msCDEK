<?php

$settings = array();

$tmp = array(
    'mscdek_from_cityid' => array(
		'xtype' => 'textfield',
		'value' => '137',
		'area' => 'mscdek_main',
	),
    'ms2_delivery_weight_in_kg' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'ms2_delivery',
        'namespace' => 'minishop2'
	),
//    'mscdek_cache_ttl' => array(
//		'xtype' => 'textfield',
//		'value' => 604800,
//		'area' => 'mscdek_main',
//	),
    'mscdek_countries_list' => array(
		'xtype' => 'textfield',
		'value' => 'libs/countries.ser',
		'area' => 'mscdek_main',
	),
    'mscdek_cities_list' => array(
		'xtype' => 'textfield',
		'value' => 'libs/cities_c.ser',
		'area' => 'mscdek_main',
	),
    'mscdek_default_size' => array(
		'xtype' => 'textfield',
		'value' => '1',
		'area' => 'mscdek_main',
	),
    'mscdek_default_weight' => array(
		'xtype' => 'textfield',
		'value' => '1',
		'area' => 'mscdek_main',
	),
    'mscdek_find_size' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'mscdek_main',
    ),
    'mscdek_field_width' => array(
        'xtype' => 'textfield',
        'value' => 'width',
        'area' => 'mscdek_main',
    ),
    'mscdek_field_height' => array(
        'xtype' => 'textfield',
        'value' => 'height',
        'area' => 'mscdek_main',
    ),
    'mscdek_field_depth' => array(
        'xtype' => 'textfield',
        'value' => 'depth',
        'area' => 'mscdek_main',
    ),
    'mscdek_login' => array(
		'xtype' => 'textfield',
		'value' => '',
		'area' => 'mscdek_main',
	),
    'mscdek_password' => array(
		'xtype' => 'textfield',
		'value' => '',
		'area' => 'mscdek_main',
	),
	'mscdek_frontend_js' => array(
		'value' => '[[+jsUrl]]web/default.js'
        ,'xtype' => 'textfield'
        ,'area' => 'mscdek_main'
	),
	'mscdek_frontend_css' => array(
		'value' => '[[+cssUrl]]web/default.css'
        ,'xtype' => 'textfield'
        ,'area' => 'mscdek_main'
	),
    'mscdek_return_time' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'mscdek_main',
    ),
    'mscdek_default_country' => array(
        'xtype' => 'textfield',
        'value' => 'RUS',
        'area' => 'mscdek_main',
    ),


);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
