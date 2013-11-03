<?php

class Worldview_Cluster_Model_Mysql4_Cluster extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('worldview_cluster/cluster', 'cluster_id');
    }
}
