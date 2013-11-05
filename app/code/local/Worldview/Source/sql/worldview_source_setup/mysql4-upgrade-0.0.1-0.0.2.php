<?php

$rss_language = 'English';
$rss_category = 'World';

$sources = array(
    array('rss_country' => 'USA', 'rss_code' => 'cnn_world', 'rss_url' => 'http://rss.cnn.com/rss/cnn_world.rss', 'rss_name' => 'CNN World'),
    array('rss_country' => 'USA', 'rss_code' => 'fox_news_world', 'rss_url' => 'http://feeds.foxnews.com/foxnews/world', 'rss_name' => 'Fox News World'),
    array('rss_country' => 'USA', 'rss_code' => 'msnbc_world', 'rss_url' => 'http://feeds.nbcnews.com/feeds/worldnews', 'rss_name' => 'MSNBC World'),
    array('rss_country' => 'USA', 'rss_code' => 'huffington_post_world', 'rss_url' => 'http://www.huffingtonpost.com/feeds/verticals/world/index.xml', 'rss_name' => 'Huffington Post World'),
    array('rss_country' => 'USA', 'rss_code' => 'usa_today_world', 'rss_url' => 'http://rssfeeds.usatoday.com/UsatodaycomWorld-TopStories', 'rss_name' => 'USA Today World')
);

foreach ($sources as $source)
{
    $source_model = Mage::getModel('worldview_source/rss');
    $source_model->setData($source);
    $source_model->setRssLanguage($rss_language);
    $source_model->setRssCategory($rss_category);
    $source_model->save();
}

/*
 * The purpose of this upgrade script is to create the Source Category, Country and Language
 * object and save them to the database
 */

$installer = $this;

$category_values = Mage::getModel('worldview_source/values_category')->getValues();

foreach ($category_values as $category)
{
    $category_model = Mage::getModel('worldview_source/category');
    $category_model->setCategoryName($category);
    $category_model->save();
}


$country_values = Mage::getModel('worldview_source/values_country')->getValues();

foreach ($country_values as $country)
{
    $country_model = Mage::getModel('worldview_source/country');
    $country_model->setCountryName($country);
    $country_model->save();
}


// Currently English is the only language being scraped, due to Google Translate's policies
$language_values = Mage::getModel('worldview_source/values_language')->getValues();

foreach ($language_values as $language)
{
    $language_model = Mage::getModel('worldview_source/language');
    $language_model->setLanguageName($language);
    $language_model->save();
}

$installer->endSetup();
