<?php

class Worldview_Io_Model_Curl extends Varien_Http_Adapter_Curl
{
    const ERROR_NO_IMAGE_DATA_WAS_RETURNED = 'No image data was returned from URL %s. Could not write to %s';
    const DEFAULT_DOWNLOADED_IMAGE_MODE = 0777;
    const DEFAULT_CURL_TIMEOUT = 5;

    public function downloadImage($url, $destination_path, $mode = null)
    {
        try
        {
            $curl_config = array(
                'timeout' => self::DEFAULT_CURL_TIMEOUT
            );
            $this->setConfig($curl_config);

            $curl_options = array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_BINARYTRANSFER => 1
            );
            $this->setOptions($curl_options);

            // The parent class doesn't do anything with the $url for this method
            // We just need to call this to set the curl options
            $this->connect($url);
            $binary_image_data = $this->read();
            $this->close();

            // Check to see if any data was pulled
            if (empty($binary_image_data))
            {
                $error_message = sprintf(self::ERROR_NO_IMAGE_DATA_WAS_RETURNED, $url, $destination_path);
                throw new Exception ($error_message);
            }

            $destination_file = Mage::getModel('worldview_io/io_file');
            if (is_null($mode))
            {
                $mode = self::DEFAULT_DOWNLOADED_IMAGE_MODE;
            }

            $destination_file->write($destination_path, $binary_image_data, $mode);
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            return $e;
        }

        return true;
    }
}
