<?php

class Worldview_Source_Helper_Scrape_Economist extends Worldview_Source_Helper_Scrape
{
    public function getScrapedText($html)
    {
        $article_text = '';

        if (!is_object($html))
        {
            return false;
        }

        $story_area_divs = $html->find('article div.main-content');

        foreach ($story_area_divs as $story_area_div)
        {
            // Iterate over every paragraph block in the article-text div
            foreach($story_area_div->find('p') as $paragraph)
            {
                $article_text .= $paragraph->plaintext . "\n";
            }
        }

        return $article_text;
    }
}
