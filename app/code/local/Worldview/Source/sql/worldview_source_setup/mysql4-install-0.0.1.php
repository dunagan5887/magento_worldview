<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

// Clean installation.
$installer->getConnection()->dropTable($installer->getTable('worldview_source/rss_source'));

// Define Batch Table.
$rss_source_table =
    $installer->getConnection()
    ->newTable($installer->getTable('worldview_source/rss_source'))
    ->addColumn(
        'rss_source_id',
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
        'rss_url',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array('nullable'  => false),
        'The URL representing the rss feed'
    )->addColumn(
        'rss_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        100,
        array('nullable'  => false),
        'The Name of the RSS Feed'
    )->addColumn(
        'rss_language',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array('nullable'  => true),
        'The language of the RSS content'
    )->addColumn(
        'rss_country',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array('nullable'  => true),
        'Country of origin for the RSS feed'
    )->addColumn(
        'rss_category',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array('nullable'  => true),
        'Category of the RSS feed'
    )->setComment('Table Abstracting RSS News Sources')
     ->addIndex(
        $installer->getIdxName('worldview_source/rss_source', array('rss_category')),
        array('rss_category'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    );

// Create Batch, Batch Type, and Batch Item Tables.
$installer->getConnection()->createTable($rss_source_table);

$installer->endSetup();
