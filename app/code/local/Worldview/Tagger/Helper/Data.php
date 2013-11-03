<?php

abstract class Worldview_Tagger_Helper_Data
{
    protected $_ioFileUtility = null;

    protected $_text_blob_separator = '.@#$%@#$%.';
    protected $_text_blob_file = 'text.txt';

    abstract public function parseFile($absolute_file_path, $blob_order_ids = null);

    public function parseTextBlob($text_blob, $blob_order_ids = null)
    {
        $absolute_filepath = $this->_writeTextBlobToFile($text_blob);
        $parsed_text = $this->parseFile($absolute_filepath, $blob_order_ids);

        return $parsed_text;
    }

    protected function _writeTextBlobToFile($text_blob)
    {
        $absolute_filepath = $this->_getTextBlobFilepath();

        $fileUtility = $this->_getIoFileUtility();
        $file_open_success = $fileUtility->openFile($absolute_filepath);
        $file_lock_success = $fileUtility->lockFile($absolute_filepath);

        $fileUtility->writeToFile($text_blob);

        $file_unlock_success = $fileUtility->unlockFile($absolute_filepath);

        return $absolute_filepath;
    }

    protected function _getTextBlobFilepath()
    {
        $filepath = Mage::getBaseDir('var') . '/tagger/blob/' . $this->_text_blob_file;
        return $filepath;
    }

    protected function _getIoFileUtility()
    {
        if (is_null($this->_ioFileUtility))
        {
            $this->_ioFileUtility = Mage::getModel('worldview_io/file_utility');
        }

        return $this->_ioFileUtility;
    }

    public function getBlobSeparatorToken()
    {
        return $this->_text_blob_separator;
    }
}
