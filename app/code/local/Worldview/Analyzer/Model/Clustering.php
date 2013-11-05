<?php

class Worldview_Analyzer_Model_Clustering
{
    /**
     * @var array $_cluster_array - array of Worldview_Analyzer_Model_Cluster objects which comprise this clustering
     */
    protected $_cluster_array;

    public function sortAscendingByClusterSize()
    {
        // Sort clusters by size
        $cluster_array = $this->_cluster_array;
        $first_cluster = reset($cluster_array);
        $cluster_class_name = get_class($first_cluster);
        $sort_result = usort($cluster_array, array($cluster_class_name, "sortDescendingByClusterSizeMetric"));

        $this->_cluster_array = $cluster_array;


        return $this;
    }

    static function sortDescendingByClusterSizeMetric($a, $b)
    {
        $a_size = $a->getClusterSize();
        $b_size = $b->getClusterSize();

        if ($a_size == $b_size)
        {
            return 0;
        }

        return ($a_size > $b_size) ? -1 : 1;
    }
    
    public function getClusters()
    {
        return $this->_cluster_array;
    }

    /**
     * @param array $clusters_array - Assumed to be an array of Worldview_Analyzer_Model_Cluster
     */
    public function setClusters(array $cluster_array)
    {
        unset($this->_cluster_array);
        foreach ($cluster_array as $cluster)
        {
            $this->addCluster($cluster);
        }
    }

    public function addCluster(Worldview_Analyzer_Model_Cluster $cluster)
    {
        $this->_cluster_array[] = $cluster;
    }

    public function __construct()
    {
        $this->_cluster_array = array();
    }
}
