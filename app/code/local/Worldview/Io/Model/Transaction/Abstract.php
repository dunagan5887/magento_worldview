<?php

abstract class Worldview_Io_Model_Transaction_Abstract extends Mage_Core_Model_Abstract
{
    protected $_error_log_file = 'io_transaction_error';
    protected $_timestamp_php_date_format = 'Ymd_His';

    abstract public function run();

    protected function _getIoAdapter()
    {
        if(!$this->hasData('io_adapter'))
        {
            $this->setData('io_adapter', Mage::getModel('worldview_io/io_file')->setAllowCreateFolders(true));
        }

        return $this->getData('io_adapter');
    }

    protected function _getTransactionDirectory()
    {
        return Mage::getBaseDir('var');
    }

    public function logError($message)
    {
        Mage::log($message, Zend_Log::ERR, $this->_error_log_file);

        return $this;
    }

    public function getTransactionFileName($file_prefix)
    {
       return $file_prefix . '_' . $this->getFileNameTimestamp() . $this->_transaction_file_extension;
    }

    public function getFileNameTimestamp()
    {
        return date($this->_timestamp_php_date_format);
    }
}
