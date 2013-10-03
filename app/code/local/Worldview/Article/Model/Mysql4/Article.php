<?php

class Worldview_Article_Model_Mysql4_Article extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('worldview_article/article', 'article_id');
    }
}
