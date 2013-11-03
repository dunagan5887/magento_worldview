<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

// Clean installation.
$installer->getConnection()->dropTable($installer->getTable('worldview_cluster/cluster'));
$installer->getConnection()->dropTable($installer->getTable('worldview_cluster/cluster_xref'));

$cluster_table =
    $installer->getConnection()
    ->newTable($installer->getTable('worldview_cluster/cluster'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_BIGINT,
        11,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Row ID for the table'
    )->addColumn(
        'cluster_type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        5,
        array(  'nullable'  => false,
                'unsigned'  => true),
        'The type of cluster (e.g. article)'
    )->addColumn(
        'cluster_category',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array('nullable'  => true),
        'Category of the cluster'
    )->addColumn(
        'cluster_date',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        10,
        array('nullable'  => true),
        'Publication Date'
    )->addColumn(
        'cluster_data',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('nullable'  => true),
        'Any data which may need to be stored regarding the cluster'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Creation Time'
    )->addIndex(
        $installer->getIdxName('worldview_cluster/cluster', array('cluster_type', 'cluster_category', 'cluster_date')),
        array('cluster_type', 'cluster_category', 'cluster_date'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )->addIndex(
        $installer->getIdxName('worldview_cluster/cluster', array('cluster_type', 'cluster_date')),
        array('cluster_type', 'cluster_date'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )->setComment('Table Abstracting Clusters');

$installer->getConnection()->createTable($cluster_table);

$cluster_xref_table =
    $installer->getConnection()
        ->newTable($installer->getTable('worldview_cluster/cluster_xref'))
        ->addColumn(
        'cluster_xref_id',
        Varien_Db_Ddl_Table::TYPE_BIGINT,
        11,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Row ID for the table'
    )->addColumn(
        'cluster_id',
        Varien_Db_Ddl_Table::TYPE_BIGINT,
        11,
        array('nullable'  => false, 'unsigned'  => true),
        'Id of the cluster'
    )
    ->addColumn(
        'xref_id',
        Varien_Db_Ddl_Table::TYPE_BIGINT,
        11,
        array('nullable'  => false, 'unsigned'  => true),
        'Entity id of the entity in the cluster'
    )->addIndex(
        $installer->getIdxName('worldview_cluster/cluster_xref', array('cluster_id')),
        array('cluster_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )->addForeignKey(
        $installer->getFkName('worldview_cluster/cluster_xref', 'cluster_id', 'worldview_cluster/cluster', 'cluster_id'),
        'cluster_id',
        $installer->getTable('worldview_cluster/cluster'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->setComment('Table Abstracting Clusters Xrefs');

$installer->getConnection()->createTable($cluster_xref_table);

$installer->endSetup();
