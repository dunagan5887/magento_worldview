<?php

class Worldview_Source_Helper_Scrape_USA_Today extends Worldview_Source_Helper_Scrape
{
    public function getScrapedText($html)
    {
        $article_text = '';
        /* For CNN articles, the text is contained in a div with class cnn_storyarea. Paragraph tags within
         * this div contain the data we are looking for.
        */
        $story_area_divs = $html->find('article.story');
        // There should only be one cnn_storyarea div tag, but account for the possibility of multiple
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
