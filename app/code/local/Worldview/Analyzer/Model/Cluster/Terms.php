<?php

class Worldview_Analyzer_Model_Cluster_Terms extends Worldview_Analyzer_Model_Cluster
{
    protected $_term_counts = null;
    protected $_term_freqs = null;

    public function mergeInOtherCluster(Worldview_Analyzer_Model_Cluster $otherCluster)
    {
        $other_cluster_counts = $otherCluster->getTermCounts();
        $this->addTermCounts($other_cluster_counts);
        $this->computeFrequencies();

        return $this;
    }
    
    public function computeFrequencies()
    {
        unset($this->_term_freqs);

        $term_count_sum = array_sum($this->_term_counts);

        foreach ($this->_term_counts as $term => $count)
        {
            $this->_term_freqs[$term] = floatval($count) / $term_count_sum;
        }

        return $this->_term_freqs;
    }

    public function getTermFrequencies()
    {
        return $this->_term_freqs;
    }

    public function addTermCounts($term_counts_array)
    {
        foreach ($term_counts_array as $term => $count)
        {
            if (isset($this->_term_counts[$term]))
            {
                $this->_term_counts[$term] = $this->_term_counts[$term] + $count;
            }
            else
            {
                $this->_term_counts[$term] = $count;
            }
        }

        return $this;
    }

    public function getTermCounts()
    {
        return $this->_term_counts;
    }

    public function setTermCounts($term_counts_array)
    {
        $this->_term_counts = $term_counts_array;
    }

    public function __construct()
    {
        $this->_term_freqs = array();
    }
}
