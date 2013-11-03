<?php

abstract class Worldview_Tagger_Model_Articles_Abstract
    extends Mage_Core_Model_Abstract
{
    abstract public function getTaggedArticlesByDate($date, $category = null);

    public function getArticlesByDate($date, $category = null)
    {
        $articles = Mage::getModel('worldview_article/article')
            ->getCollection()
            ->addFieldToFilter('published_date', $date);

        if (!is_null($category))
        {
            $articles->addFieldToFilter('article_category', $category);
        }

        return $articles->getItems();
    }
}