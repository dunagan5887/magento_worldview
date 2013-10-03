<?php

class Worldview_Source_Helper_Scrape extends Mage_Core_Helper_Data
{
    const SCRAPER_HELPERS_BY_CODE_CONFIG_PATH = 'scrape/source_helpers/';
    const SCRAPER_HELPER_CLASS_NAME_BASE = 'worldview_source/scrape';

    private $_scraper_library_filepath = 'Worldview/simple_html_dom.php';
    private $_scraper_library_has_been_included = false;

    public function getScrapedPageText($site_url, $source_code)
    {
        $source_scraper_helper = $this->getSourceScraperByCode($source_code);

        if (!$source_scraper_helper)
        {
            return '';
        }

        return $source_scraper_helper->scrapePageText($site_url);
    }

    public function scrapePageText($site_url)
    {
        $library_is_included = $this->includeScraperLibrary();
        if (!$library_is_included)
        {
            return '';
        }

        $html = file_get_html($site_url);

        return $this->getScrapedText($html);
    }

    public function getScrapedText($html)
    {
        // A subclass of this helper class should be responsible for scraping the text
        return '';
    }

    /**
     * To scrape the web pages, I have employed the HTML DOM PHP library, available on SourceForge.
     * We want to make sure that the library is included only once, and we want to require its inclusion.
     *
     * @return bool
     */
    private function includeScraperLibrary()
    {
        if (!$this->_scraper_library_has_been_included)
        {
            try
            {
                require_once $this->_scraper_library_filepath;
                $this->_scraper_library_has_been_included = true;
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                $this->_scraper_library_has_been_included = false;
            }
        }

        return $this->_scraper_library_has_been_included;
    }

    public function getSourceScraperByCode($source_code)
    {
        $source_scraper_config_path = self::SCRAPER_HELPERS_BY_CODE_CONFIG_PATH . $source_code;
        $source_scraper_code = Mage::getStoreConfig($source_scraper_config_path);
        $source_scraper_helper_class_name = self::SCRAPER_HELPER_CLASS_NAME_BASE . '_' . $source_scraper_code;
        $source_scraper_helper = Mage::helper($source_scraper_helper_class_name);

        return is_object($source_scraper_helper) ? $source_scraper_helper : false;
    }
}
