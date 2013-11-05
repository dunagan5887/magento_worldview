<?php

class Worldview_Analyzer_Model_Article_Cluster extends Worldview_Analyzer_Model_Cluster_Terms
{
    protected $_article_ids = null;

    public function __construct()
    {
        $this->_article_ids = array();
    }

    public function mergeInOtherCluster(Worldview_Analyzer_Model_Cluster $otherCluster)
    {
        if ($otherCluster instanceof Worldview_Analyzer_Model_Article_Cluster)
        {
            $other_cluster_counts = $otherCluster->getArticleIds();
            $this->addArticleIds($other_cluster_counts);
        }

        return parent::mergeInOtherCluster($otherCluster);
    }

    public function getClusterSize()
    {
        return count($this->_article_ids);
    }

    public function getArticleIds()
    {
        return $this->_article_ids;
    }

    public function addArticleIds($article_ids)
    {
        foreach ($article_ids as $article_id)
        {
            $this->addArticleId($article_id);
        }
    }

    public function addArticleId($article_id)
    {
        $this->_article_ids[$article_id] = $article_id;
    }
}
