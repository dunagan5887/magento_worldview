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

                // If an article with the same link already exists in the database, don't record it again
                $article_link = $item->link;
                $article_resource = Mage::getModel(self::ARTICLE_CLASS_NAME)->getResource();
                $read = $article_resource->getReadConnection();
                $select = $read->select()->from($article_resource->getMainTable(), array('article_url'))
                                ->where('article_url = ?', $article_link)
                                ->limit(1);

                $article_link_found = (bool) $read->fetchOne($select);

                if ($article_link_found)
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

                if (!$item_text)
                {
                    continue;
                }

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
        $article->setSourceId($source->getId());
        $article->setArticleUrl($item->link);
        $article->setHeadline($item->title);
        $article->setArticleText($item_text);
        $article->setArticleLanguage($source->getRssLanguage());
        $article->setArticleCountry($source->getRssCountry());
        $article->setArticleCategory($source->getRssCategory());

        $pub_date = $item->pubDate;
        $pub_timestamp = strtotime($pub_date);
        $date_string = date('Y-m-d', $pub_timestamp);
        $article->setPublishedDate($date_string);

        // Get current timestamp adjusted for the timezone of the instance
        $article->setCreatedAt(Mage::getModel('core/date')->timestamp());

        return $article;
    }

    public function getSourceModelClassname()
    {
        return $this->_sourceModelClassName;
    }
}
