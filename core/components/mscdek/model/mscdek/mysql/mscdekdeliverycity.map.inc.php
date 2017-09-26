<?php
$xpdo_meta_map['msCDEKDeliveryCity']= array (
    'package' => 'mscdek',
    'version' => '1.1',
    'table' => 'mscdek_delivery_availiblities',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        array (
            'city_from' => NULL,
            'city_to' => NULL,
            'tariff' => NULL,
            'active' => 1,
            'updatedon' => NULL,
        ),
    'fieldMeta' =>
        array (
            'city_from' =>
                array (
                    'dbtype' => 'int',
                    'precision' => '11',
                    'phptype' => 'integer',
                    'null' => false,
                    'index' => 'index',
                ),
            'city_to' =>
                array (
                    'dbtype' => 'int',
                    'precision' => '11',
                    'phptype' => 'integer',
                    'null' => false,
                    'index' => 'index',
                ),
            'tariff' =>
                array (
                    'dbtype' => 'int',
                    'precision' => '11',
                    'phptype' => 'integer',
                    'null' => false,
                    'index' => 'index',
                ),
            'active' =>
                array (
                    'dbtype' => 'tinyint',
                    'precision' => '1',
                    'phptype' => 'boolean',
                    'null' => false,
                    'default' => 1,
                ),
            'updatedon' =>
                array (
                    'dbtype' => 'timestamp',
                    'phptype' => 'timestamp',
                    'null' => true,
                    'default' => NULL,
                    'attributes' => 'ON UPDATE CURRENT_TIMESTAMP',
                ),
        ),
    'indexes' =>
        array (
            'delivery_city' =>
                array (
                    'alias' => 'delivery_city',
                    'primary' => false,
                    'unique' => true,
                    'type' => 'BTREE',
                    'columns' =>
                        array (
                            'city_from' =>
                                array (
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                            'city_to' =>
                                array (
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                            'tariff' =>
                                array (
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                        ),
                ),
        ),
    'aggregates' =>
        array (
            'CityFrom' =>
                array (
                    'class' => 'msCDEKCity',
                    'local' => 'city_from',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ),
            'CityTo' =>
                array (
                    'class' => 'msCDEKCity',
                    'local' => 'city_to',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ),
        ),
);
