<?php

class Worldview_Analyzer_Model_Cluster_Method_Distance_Termfreqa
    extends Worldview_Analyzer_Model_Cluster_Method_Distance
{
    public function __construct()
    {
        $this->_threshold = .72;
    }

    public function getThreshold()
    {
        return $this->_threshold;
    }

    /**
     * This is the method which provides the distance metric between clusters which is used
     * to cluster the articles
     */

    public function distance(Worldview_Analyzer_Model_Cluster_Terms $a, Worldview_Analyzer_Model_Cluster_Terms $b)
    {
        $a_term_freqs = $a->getTermFrequencies();
        $b_term_freqs = $b->getTermFrequencies();

        $similarity = 0.0;
        // Based off of the sum of the frequencies of the proper nouns
        foreach($a_term_freqs as $term => $a_freq)
        {
            if (isset($b_term_freqs[$term]))
            {
                $b_freq = $b_term_freqs[$term];
                $similarity += ($a_freq < $b_freq) ? $a_freq : $b_freq;
            }
        }

        return (1-$similarity);
    }
}
