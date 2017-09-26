<?php
$xpdo_meta_map['msCDEKDeliveryPoint']= array (
    'package' => 'mscdek',
    'version' => '1.1',
    'table' => 'mscdek_delivery_points',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        array (
            'city' => NULL,
            'code' => '',
            'address' => NULL,
            'phone' => NULL,
            'email' => NULL,
            'time' => NULL,
            'properties' => NULL,
        ),
    'fieldMeta' =>
        array (
            'city' =>
                array (
                    'dbtype' => 'int',
                    'precision' => '11',
                    'phptype' => 'integer',
                    'null' => false,
                    'attributes' => 'unsigned',
                    'index' => 'index',
                ),
            'code' =>
                array (
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ),
            'address' =>
                array (
                    'dbtype' => 'text',
                    'null' => true,
                    'phptype' => 'string',
                ),
            'phone' =>
                array (
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'null' => true,
                    'phptype' => 'string',
                ),
            'email' =>
                array (
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'null' => true,
                    'phptype' => 'string',
                ),
            'time' =>
                array (
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'null' => true,
                    'phptype' => 'string',
                ),
            'properties' =>
                array (
                    'dbtype' => 'text',
                    'phptype' => 'json',
                    'null' => true,
                ),
        ),
    'indexes' =>
        array (
            'city' =>
                array (
                    'alias' => 'city',
                    'primary' => false,
                    'unique' => false,
                    'type' => 'BTREE',
                    'columns' =>
                        array (
                            'city' =>
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
            'City' =>
                array (
                    'class' => 'msCDEKCity',
                    'local' => 'city',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ),
        ),
);
