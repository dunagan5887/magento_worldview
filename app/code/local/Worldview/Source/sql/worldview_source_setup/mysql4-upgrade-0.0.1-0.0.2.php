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

