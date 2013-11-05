<?php

class Worldview_Analyzer_Helper_Cluster_Articles extends Worldview_Analyzer_Helper_Cluster
{
    protected $_pos_tagger = 'stanford';
    protected $_cluster_method_classname = 'worldview_analyzer/cluster_method_matrix';

    /**
     * @param $article_pos_tags_array
     * @return Worldview_Analyzer_Model_Clustering
     */
    public function clusterArticlesByPosTags($article_pos_tags_array)
    {
        $clustering = $this->buildClustering($article_pos_tags_array);

        $clusteringMethod = Mage::getModel($this->_cluster_method_classname);
        $merged_clusters_array = $clusteringMethod->mergeClusters($clustering);

        $clustering->setClusters($merged_clusters_array);
        $clustering->sortAscendingByClusterSize();

        return $clustering;
    }

    public function buildClustering($article_pos_tags_array)
    {
        $pos_tag_helper = Mage::helper('worldview_tagger/pos_' . $this->getPosTagger());
        $principal_pos_tags = $pos_tag_helper->getPrincipalPosStrings();

        $clustering = Mage::getModel('worldview_analyzer/clustering');

        foreach ($article_pos_tags_array as $articled_id => $article_tags)
        {
            $articleCluster = Mage::getModel('worldview_analyzer/article_cluster');
            $articleCluster->addArticleId($articled_id);

            foreach ($principal_pos_tags as $tag)
            {
                $articleCluster->addTermCounts($article_tags[$tag]);
            }

            $articleCluster->computeFrequencies();

            $clustering->addCluster($articleCluster);
        }

        return $clustering;
    }

    public function getPosTagger()
    {
        return $this->_pos_tagger;
    }
}
