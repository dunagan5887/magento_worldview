<?php

class Worldview_Source_Model_Mysql4_Language_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('worldview_source/language');
    }
}
