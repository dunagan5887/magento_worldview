<?php

class Worldview_Source_Model_Mysql4_Language extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('worldview_source/source_language', 'language_id');
    }
}
