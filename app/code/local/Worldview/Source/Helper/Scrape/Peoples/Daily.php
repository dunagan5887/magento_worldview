<?php

class Worldview_Source_Helper_Scrape_Peoples_Daily extends Worldview_Source_Helper_Scrape
{
    public function getScrapedText($html)
    {
        $article_text = '';
        /* For CNN articles, the text is contained in a div with class cnn_storyarea. Paragraph tags within
         * this div contain the data we are looking for.
        */

        if (!is_object($html))
        {
            return false;
        }

        $story_area_divs = $html->find('span#p_content');
        // There should only be one cnn_storyarea div tag, but account for the possibility of multiple
        foreach ($story_area_divs as $story_area_div)
        {
            $article_text .= $story_area_div->plaintext . "\n";
            /*
            // Iterate over every paragraph block in the article-text div
            foreach($story_area_div->find('p') as $paragraph)
            {
                $article_text .= $paragraph->plaintext . "\n";
            }
            */
        }

        $story_area_divs = $html->find('div.wb_content');
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
