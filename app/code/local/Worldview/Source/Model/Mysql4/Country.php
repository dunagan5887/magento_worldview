<?php

class Worldview_Source_Model_Mysql4_Country extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('worldview_source/source_country', 'country_id');
    }
}
