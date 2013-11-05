<?php

$rss_language = 'English';
$rss_category = 'World';

$rss_language = Mage::getModel('worldview_source/language')
                    ->getCollection()
                    ->addFieldToFilter('language_name', 'English')
                    ->getFirstItem()
                    ->getId();

$rss_category = Mage::getModel('worldview_source/category')
                    ->getCollection()
                    ->addFieldToFilter('category_name', 'World')
                    ->getFirstItem()
                    ->getId();

$country_china = Mage::getModel('worldview_source/country')
                    ->getCollection()
                    ->addFieldToFilter('country_name', 'China')
                    ->getFirstItem()
                    ->getId();

$country_india = Mage::getModel('worldview_source/country')
                    ->getCollection()
                    ->addFieldToFilter('country_name', 'India')
                    ->getFirstItem()
                    ->getId();

$sources = array(
    array('rss_country' => $country_india, 'rss_code' => 'times_of_india_world', 'rss_url' => 'http://timesofindia.feedsportal.com/c/33039/f/533917/index.rss', 'rss_name' => 'Times of India World'),
    array('rss_country' => $country_china, 'rss_code' => 'peoples_daily_world', 'rss_url' => 'http://english.peopledaily.com.cn/rss/World.xml', 'rss_name' => 'People\'s Daily World')
);

foreach ($sources as $source)
{
    $source_model = Mage::getModel('worldview_source/rss');
    $source_model->setData($source);
    $source_model->setRssLanguage($rss_language);
    $source_model->setRssCategory($rss_category);
    $source_model->save();
}

