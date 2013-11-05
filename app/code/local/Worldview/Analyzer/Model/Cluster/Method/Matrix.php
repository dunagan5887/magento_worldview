<?php

class Worldview_Analyzer_Model_Cluster_Method_Matrix
    extends Worldview_Analyzer_Model_Cluster_Method
{
    protected $_clusters = array();
    protected $_distance_matrix = array();
    protected $_distanceMethod = null;
    protected $_distance_method_model = 'worldview_analyzer/cluster_method_distance_termfreqa';

    public function mergeClusters(Worldview_Analyzer_Model_Clustering $clustering)
    {
        $this->_clusters = $clustering->getClusters();
        // Compute the distance between clusters with these new clusters
        $this->_createDistanceMatrix();

        // TODO!!! Everything below

        // Find the clusters which are similar enough to be merged together
        $min_distances = $this->getClosestClusters();
        // While there are clusters which are similar enough to be merged together
        while (count($min_distances) > 0)
        {
            // Merge the pairs of clusters which are the most similar
            $this->mergeClosestClusters($min_distances);
            // Recompute the distance between clusters with these new clusters
            $this->_createDistanceMatrix();
            // Find the clusters which are similar enough to be merged together
            $min_distances = $this->getClosestClusters();
        }

        return $this->_clusters;
    }

    protected function getClosestClusters()
    {
        $min_distances = array();
        $threshold = $this->_distanceMethod->getThreshold();

        foreach($this->_distance_matrix as $index_1 => $cluster_1)
        {
            foreach($this->_distance_matrix as $index_2 => $cluster_2)
            {
                if ($index_1 != $index_2)
                {
                    $distance = $this->_distance_matrix[$index_1][$index_2];
                    if ($distance < $threshold)
                    {
                        // If distance < threshold, then this pair of clusters is similar enough
                        // to be merged into 1 cluster
                        $min_distances[] = array("distance" => $distance, "index_1" => $index_1, "index_2" => $index_2);
                    }
                }
            }
        }
        // Sort distance by descending similarity
        usort($min_distances, "self::sort_min_distances_array");

        return $min_distances;
    }

    /*
     * TODO!! Resume with this, fix it to be better
     */
    protected function mergeClosestClusters($min_distances)
    {
        // TODO# Define $min_distances

        $merged_clusters = array();
        // For each pair of clusters which are similar enough to be merged together
        foreach($min_distances as $distance_data)
        {
            $id_a = $distance_data["index_1"];
            $id_b = $distance_data["index_2"];
            // If neither of these arrays has been merged yet
            if (!in_array($id_a, $merged_clusters) && !in_array($id_b, $merged_clusters))
            {
                $size_a = $this->_clusters[$id_a]->getClusterSize();
                $size_b = $this->_clusters[$id_b]->getClusterSize();

                if ($size_a < $size_b)
                {
                    $smaller_index = $id_a;
                    $larger_index = $id_b;
                }
                else
                {
                    $smaller_index = $id_b;
                    $larger_index = $id_a;
                }

                $smaller_cluster = $this->_clusters[$smaller_index];
                // Merge the smaller cluster into the larger cluster
                $this->_clusters[$larger_index]->mergeInOtherCluster($smaller_cluster);
                unset($this->_clusters[$smaller_index]);
                $merged_clusters[$id_a] = $id_a;
                $merged_clusters[$id_b] = $id_b;
            }
        }
    }

    protected function sort_min_distances_array($a, $b)
    {
        if ($a['distance'] === $b['distance'])
        {
            return 0;
        }

        return ($a['distance'] < $b['distance']) ? -1 : 1;
    }

    public function _createDistanceMatrix()
    {
        $this->_clearDistanceMatrix();

        foreach($this->_clusters as $index_1 => $cluster_1)
        {
            foreach($this->_clusters as $index_2 => $cluster_2)
            {
                if ($index_1 != $index_2)
                {
                    // Compute the distance between the two clusters using the chosen distance_method metric
                    $distance = $this->_distanceMethod->distance($cluster_1, $cluster_2);
                    $this->_distance_matrix[$index_1][$index_2] = $this->_distance_matrix[$index_2][$index_1] = $distance;
                }
                else
                {
                    $this->_distance_matrix[$index_1][$index_2] = $this->_distance_matrix[$index_2][$index_1] = 1;
                }
            }
        }
    }

    protected function _clearDistanceMatrix()
    {
        unset($this->_distance_matrix);
        $this->_distance_matrix = array();
        foreach($this->_clusters as $index_1 => $cluster_1)
        {
            $this->_distance_matrix[$index_1] = array();
        }
    }

    public function __construct()
    {
        $this->_distanceMethod = Mage::getModel($this->_distance_method_model);
    }
}
