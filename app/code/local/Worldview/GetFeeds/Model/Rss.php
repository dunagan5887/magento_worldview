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
                if (empty($item->link))
                {
                    continue;
                }

                // Scrape the text from the page
                $item_text = Mage::helper('worldview_source/scrape')->getScrapedPageText($item->link, $source->getRssCode());
            }
        }
    }

    public function getSourceModelClassname()
    {
        return $this->_sourceModelClassName;
    }
}
