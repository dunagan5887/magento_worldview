<?php

class Worldview_Cluster_Model_Mysql4_Cluster_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('worldview_cluster/cluster');
    }
}