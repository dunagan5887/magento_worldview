<?php

class Worldview_Source_Helper_Scrape_Huffington extends Worldview_Source_Helper_Scrape
{
    public function getScrapedText($html)
    {
        $article_text = '';
        /* For Foxnews articles, the text is contained in a div with class article-text. Paragraph tags within
         * this div contain the data we are looking for.
        */
        $article_text_divs = $html->find('div.entry_body_text');
        // There should only be one article-text div tag, but account for the possibility of multiple
        foreach ($article_text_divs as $article_text_div)
        {
            // Iterate over every paragraph block in the article-text div
            foreach($article_text_div->find('p') as $paragraph)
            {
                $article_text .= $paragraph->plaintext . "\n";
            }
        }

        return $article_text;
    }
}
