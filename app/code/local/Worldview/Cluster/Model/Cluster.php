<?php

abstract class Worldview_Cluster_Model_Cluster
    extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('worldview_cluster/cluster');
    }
}