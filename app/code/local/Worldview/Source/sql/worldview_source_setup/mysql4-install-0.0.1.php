<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

// Clean installation.
$installer->getConnection()->dropTable($installer->getTable('worldview_source/rss_source'));
$installer->getConnection()->dropTable($installer->getTable('worldview_source/source_country'));
$installer->getConnection()->dropTable($installer->getTable('worldview_source/source_language'));
$installer->getConnection()->dropTable($installer->getTable('worldview_source/source_category'));

/*
 * The country, language, and category tables are created to provide normalization for the database.
 * Both the Source and Article tables will use the ids referring to these tables in their data rows.
 */

$source_category_table =
    $installer->getConnection()
    ->newTable($installer->getTable('worldview_source/source_category'))
    ->addColumn(
        'category_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Row ID for the table'
    )->addColumn(
        'category_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array('nullable' => false),
        'An English name for the category'
    )->addIndex(
        $installer->getIdxName('worldview_source/source_category', array('category_name')),
        array('category_name'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->setComment('Table Abstracting Source Categories');

$installer->getConnection()->createTable($source_category_table);

$source_language_table =
    $installer->getConnection()
    ->newTable($installer->getTable('worldview_source/source_language'))
    ->addColumn(
        'language_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Row ID for the table'
    )->addColumn(
        'language_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        40,
        array('nullable' => false),
        'An English name for the language'
    )->addIndex(
        $installer->getIdxName('worldview_source/source_language', array('language_name')),
        array('language_name'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->setComment('Table Abstracting Source Languages');

$installer->getConnection()->createTable($source_language_table);

$source_country_table =
    $installer->getConnection()
    ->newTable($installer->getTable('worldview_source/source_country'))
    ->addColumn(
        'country_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Row ID for the table'
    )->addColumn(
        'country_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        40,
        array('nullable' => false),
        'An English name for the country'
    )->addIndex(
        $installer->getIdxName('worldview_source/source_country', array('country_name')),
        array('country_name'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->setComment('Table Abstracting Source Countries');

$installer->getConnection()->createTable($source_country_table);

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
        'rss_code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        30,
        array(
            'nullable' => false
        ),
        'A string representing a unique identifier for the source'
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
        'rss_category',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array('nullable'  => false,
              'unsigned'  => true),
        'Category of the RSS feed'
    )->addColumn(
        'rss_language',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array('nullable'  => false,
              'unsigned'  => true),
        'The language of the RSS content'
    )->addColumn(
        'rss_country',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array('nullable'  => false,
              'unsigned'  => true),
        'Country of origin for the RSS feed'
    )->addIndex(
        $installer->getIdxName('worldview_source/rss_source', array('rss_code')),
        array('rss_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->addIndex(
        $installer->getIdxName('worldview_source/rss_source', array('rss_category')),
        array('rss_category'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )->addIndex(
        $installer->getIdxName('worldview_source/rss_source', array('rss_language')),
        array('rss_language'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )->addIndex(
        $installer->getIdxName('worldview_source/rss_source', array('rss_country')),
        array('rss_country'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )->addForeignKey(
        $installer->getFkName('worldview_source/rss_source', 'rss_category', 'worldview_source/source_category', 'category_id'),
        'rss_category',
        $installer->getTable('worldview_source/source_category'),
        'category_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName('worldview_source/rss_source', 'rss_language', 'worldview_source/source_language', 'language_id'),
        'rss_language',
        $installer->getTable('worldview_source/source_language'),
        'language_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName('worldview_source/rss_source', 'rss_country', 'worldview_source/source_country', 'country_id'),
        'rss_country',
        $installer->getTable('worldview_source/source_country'),
        'country_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->setComment('Table Abstracting RSS News Sources');

$installer->getConnection()->createTable($rss_source_table);

$installer->endSetup();
