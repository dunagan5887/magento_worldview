<?php

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
