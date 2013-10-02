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

            foreach($rss_xml_object->channel->item as $item)
            {
                $page_data = file_get_contents($item->link);
                echo $page_data;
            }
        }
    }

    public function getSourceModelClassname()
    {
        return $this->_sourceModelClassName;
    }
}
