<?php

class Worldview_Source_Model_Mysql4_Rss extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('worldview_source/rss_source', 'rss_source_id');
    }
}
