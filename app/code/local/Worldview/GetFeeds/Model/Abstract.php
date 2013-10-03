<?php

abstract class Worldview_GetFeeds_Model_Abstract extends Mage_Core_Model_Abstract
{
    const ARTICLE_CLASS_NAME = 'worldview_article/article';

    abstract public function getFeedData($sources);

    abstract public function getSourceModelClassname();

    public function processFeeds()
    {
        $sources = $this->getSourceCollection();

        if (is_object($sources))
        {
            $this->getFeedData($sources);
        }
    }

    public function getSourceCollection()
    {
        $source_model_class_name = $this->getSourceModelClassname();
        $source_model = Mage::getModel($source_model_class_name);

        if (is_object($source_model))
        {
            return $source_model->getCollection();
        }

        return false;
    }
}

