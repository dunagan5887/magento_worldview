<?php

$rss_language = 'English';
$rss_category = 'World';

$sources = array(
    array('rss_country' => 'India', 'rss_code' => 'times_of_india_world', 'rss_url' => 'http://timesofindia.feedsportal.com/c/33039/f/533917/index.rss', 'rss_name' => 'Times of India World'),
    array('rss_country' => 'China', 'rss_code' => 'peoples_daily_world', 'rss_url' => 'http://english.peopledaily.com.cn/rss/World.xml', 'rss_name' => 'People\'s Daily World')
);

foreach ($sources as $source)
{
    $source_model = Mage::getModel('worldview_source/rss');
    $source_model->setData($source);
    $source_model->setRssLanguage($rss_language);
    $source_model->setRssCategory($rss_category);
    $source_model->save();
}

