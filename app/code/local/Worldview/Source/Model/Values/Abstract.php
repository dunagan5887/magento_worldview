<?php

class Worldview_Source_Model_Values_Abstract extends Mage_Core_Model_Abstract
{
    protected $_values = array();

    public function getValues()
    {
        return $this->_values;
    }
}
