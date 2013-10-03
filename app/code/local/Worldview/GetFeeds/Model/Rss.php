<?php

class Worldview_GetFeeds_Model_Rss extends Worldview_GetFeeds_Model_Abstract
{
    protected $_sourceModelClassName = 'worldview_source/rss';

    public function getFeedData($sources)
    {
        foreach ($sources->getItems() as $source)
        {
            $rss_url = $source->getRssUrl();
            $rss_xml_object = simplexml_load_file($rss_url);

            if (!is_object($rss_xml_object) || !is_object($rss_xml_object->channel))
            {
                return false;
            }

            foreach($rss_xml_object->channel->item as $item)
            {
                // Ensure that the $item object contains a link
                if (!isset($item->link) || empty($item->link))
                {
                    continue;
                }
                // Ensure that the $item object contains a title
                if (!isset($item->title) || empty($item->title))
                {
                    continue;
                }

                // Scrape the text from the page
                $item_text = Mage::helper('worldview_source/scrape')->getScrapedPageText($item->link, $source->getRssCode());
                $article = $this->convertItemToArticle($item, $item_text, $source);
                if (!is_object($article))
                {
                    continue;
                }

                try
                {
                    $article->save();
                }
                catch(Exception $e)
                {
                    Mage::logException($e);
                }
            }
        }
    }

    public function convertItemToArticle($item, $item_text, $source)
    {
        $article = Mage::getModel(self::ARTICLE_CLASS_NAME);
        $article->setArticleUrl($item->link);
        $article->setHeadline($item->title);
        $article->setArticleText($item_text);
        $article->setArticleLanguage($source->getRssLanguage());
        $article->setArticleCountry($source->getRssCountry());
        $article->setArticleCategory($source->getRssCategory());
        // Get current timestamp adjusted for the timezone of the instance
        $article->setCreatedAt(Mage::getModel('core/date')->timestamp());

        return $article;
    }

    public function getSourceModelClassname()
    {
        return $this->_sourceModelClassName;
    }
}
