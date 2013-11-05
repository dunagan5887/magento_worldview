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

$country_uk = Mage::getModel('worldview_source/country')
                    ->getCollection()
                    ->addFieldToFilter('country_name', 'UK')
                    ->getFirstItem()
                    ->getId();

$sources = array(
    array('rss_country' => $country_uk, 'rss_code' => 'economist_europe', 'rss_url' => 'http://www.economist.com/feeds/print-sections/75/europe.xml', 'rss_name' => 'Economist Europe'),
    array('rss_country' => $country_uk, 'rss_code' => 'economist_middle_east_africa', 'rss_url' => 'http://www.economist.com/feeds/print-sections/99/middle-east-africa.xml', 'rss_name' => 'Economist Middle East and Africa'),
    array('rss_country' => $country_uk, 'rss_code' => 'economist_asia', 'rss_url' => 'http://www.economist.com/feeds/print-sections/73/asia.xml', 'rss_name' => 'Economist Asia'),
    array('rss_country' => $country_uk, 'rss_code' => 'economist_americas', 'rss_url' => 'http://www.economist.com/feeds/print-sections/72/the-americas.xml', 'rss_name' => 'Economist Americas')
);

foreach ($sources as $source)
{
    $source_model = Mage::getModel('worldview_source/rss');
    $source_model->setData($source);
    $source_model->setRssLanguage($rss_language);
    $source_model->setRssCategory($rss_category);
    $source_model->save();
}

