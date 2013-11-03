<?php

class Worldview_Tagger_Model_Articles_Pos extends Worldview_Tagger_Model_Articles_Abstract
{
    protected $tag_algorithm = 'stanford';

    public function getTaggedArticlesByDate($date, $category = null)
    {
        $articles = $this->getArticlesByDate($date, $category);

        $articles_text_blob = '';
        $tagging_algorithm = $this->getTagAlgorithm();
        $tagging_helper = Mage::helper('worldview_tagger/pos_' . $tagging_algorithm);
        $text_blob_separator = $tagging_helper->getBlobSeparatorToken();
        $article_id_order = array();

        foreach ($articles as $article)
        {
            $articles_text_blob .= $article->getArticleText() . "\n" . $text_blob_separator . "\n\n";
            $article_id_order[] = $article->getId();
        }

        $tagged_text_array = $tagging_helper->parseTextBlob($articles_text_blob, $article_id_order);

        return $tagged_text_array;
    }

    public function getTagAlgorithm()
    {
        return $this->tag_algorithm;
    }

    public function setTagAlgorithm($algorithm)
    {
        $this->tag_algorithm = $algorithm;
        return $this;
    }
}
