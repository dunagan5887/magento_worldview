<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

// Clean installation.
$installer->getConnection()->dropTable($installer->getTable('worldview_article/article'));

// Define Batch Table.
$article_table =
    $installer->getConnection()
    ->newTable($installer->getTable('worldview_article/article'))
    ->addColumn(
        'article_id',
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
        'article_url',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array('nullable'  => false),
        'The URL representing the article'
    )->addColumn(
        'headline',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        150,
        array(
            'nullable' => false
        ),
        'Headline for the Article'
    )->addColumn(
        'article_language',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array('nullable'  => true),
        'The language of the article'
    )->addColumn(
        'article_country',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array('nullable'  => true),
        'Country of origin for the article'
    )->addColumn(
        'article_category',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array('nullable'  => true),
        'Category of the category'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Creation Time'
    )->addColumn(
        'article_text',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('nullable'  => true),
        'Text of the article'
    )->setComment('Table Abstracting News Articles');

// Create Batch, Batch Type, and Batch Item Tables.
$installer->getConnection()->createTable($article_table);

$installer->endSetup();
