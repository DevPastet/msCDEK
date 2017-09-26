<?php
$xpdo_meta_map['msCDEKCity']= array (
    'package' => 'mscdek',
    'version' => '1.1',
    'table' => 'mscdek_cities',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        array (
            'city_id' => NULL,
            'name' => NULL,
            'country' => NULL,
        ),
    'fieldMeta' =>
        array (
            'city_id' =>
                array (
                    'dbtype' => 'int',
                    'precision' => '11',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'index' => 'index',
                ),
            'name' =>
                array (
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'country' =>
                array (
                    'dbtype' => 'int',
                    'precision' => '11',
                    'phptype' => 'integer',
                    'null' => false,
                    'attributes' => 'unsigned',
                ),
        ),
    'indexes' =>
        array (
            'city_id' =>
                array (
                    'alias' => 'city_id',
                    'primary' => false,
                    'unique' => true,
                    'type' => 'BTREE',
                    'columns' =>
                        array (
                            'city_id' =>
                                array (
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                        ),
                ),
            'country' =>
                array (
                    'alias' => 'country',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        array (
                            'country' =>
                                array (
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                        ),
                ),
        ),
    'composites' =>
        array (
            'Points' =>
                array (
                    'class' => 'msCDEKDeliveryPoint',
                    'local' => 'id',
                    'foreign' => 'city',
                    'owner' => 'local',
                    'cardinality' => 'many',
                ),
            'Deliveries' =>
                array (
                    'class' => 'msCDEKDeliveryCity',
                    'local' => 'id',
                    'foreign' => 'city',
                    'cardinality' => 'many',
                    'owner' => 'local',
                ),
        ),
    'aggregates' =>
        array (
            'Country' =>
                array (
                    'class' => 'msCDEKCountry',
                    'local' => 'country',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ),
        ),
);
