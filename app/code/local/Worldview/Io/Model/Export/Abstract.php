<?php

class Worldview_Io_Model_Export_Abstract extends Worldview_Io_Model_Transaction_Abstract
{
    const FILE_PERMISSIONS      = 0777;

    const ERR_FILE_LOCK         = 'Error obtaining lock for: %s';
    const ERR_FILE_WRITE        = 'Error writing to: %s';

    const WARN_HEADER_ROW_ALREADY_SET   = 'Warning: header row already set for interface name: "%s"';

    protected $_file_delimiter = ',';
    protected $_file_enclosure = '"';

    protected $_files;

    protected $_direction = 'export';
    protected $_transaction_file_extension = '.csv';

    private $_isHeaderRowSet;

    public function run()
    {
        $this->_buildDataFiles();

        $this->_write();
    }

    protected function _write()
    {
        if($this->_files && is_array($this->_files))
        {
            $ioAdapter = $this->_getIoAdapter();
            foreach($this->_files as $filePrefix => $data)
            {
                $ioAdapter->setAllowCreateFolders(true);
                $dirPath = $this->getExportPath();

                $ioAdapter->open(array('path' => $dirPath));
                $filename = $this->getTransactionFileName($filePrefix);
                $filePath = $dirPath . DS . $filename;

                $ioAdapter->streamOpen($filePath, 'w+', self::FILE_PERMISSIONS);
                if(!$ioAdapter->streamLock(true))
                {
                    throw new Worldview_Io_Model_Exception(sprintf(self::ERR_FILE_LOCK, $filePath));
                }

                $contents = $this->_generateCsvContents($data, $this->_file_delimiter, $this->_file_enclosure);

                // Use an "atomic" write to the stream for the whole file.  Do not use streamWriteCsv(), fputcsv, etc.
                if(!$ioAdapter->streamWrite($contents))
                {
                    throw new Worldview_Io_Model_Exception(sprintf(self::ERR_FILE_WRITE, $filePath));
                }

                $this->_archive($filename);

                $ioAdapter->streamUnlock();
                $ioAdapter->streamClose();
            }
        }
    }

    protected function _addNewRow($interfaceName, array $row)
    {
        $this->_files[$interfaceName][] = $row;
        return $this;
    }

    protected function _countRows($interfaceName)
    {
        if(!isset($this->_files[$interfaceName]))
        {
            return 0;
        }else{
            return count($this->_files[$interfaceName]);
        }
    }

    protected function _getColumns($interfaceName)
    {
        return $this->_columns[$interfaceName];
    }

    protected function _setHeaderRow($interfaceName)
    {
        if(!isset($this->_isHeaderRowSet[$interfaceName]))
        {
            $columnNames = $this->_getColumnNames($interfaceName);
            $header_row = $this->_buildHeaderRow($columnNames);

            if($this->_countRows($interfaceName) == 0)
            {
                $this->_addNewRow($interfaceName, $header_row);
            }else{
                array_unshift($this->_files[$interfaceName], $header_row);
            }
            $this->_isHeaderRowSet[$interfaceName] = true;
        }else{
            Mage::log(sprintf(self::WARN_HEADER_ROW_ALREADY_SET, $interfaceName), Zend_Log::WARN);
        }

        return $this;
    }

    protected function _buildHeaderRow($columnNames)
    {
        return array_combine($columnNames, $columnNames);
    }

    protected function _getColumnNames($interfaceName)
    {
        return array_keys($this->_getColumns($interfaceName));
    }

    protected function _generateCsvContents($data = array(), $delimiter = ',', $enclosure = '"', $escape_char = '\\')
    {
        $str = '';
        foreach($data as $fields)
        {
            if(!empty($fields) && is_array($fields))
            {
                $str .= $this->convertArrayToCsvContent($fields, $delimiter, $enclosure, $escape_char);
            }
            $str .= "\n";
        }

        return $str;
    }

    public function convertArrayToCsvContent($data_array, $delimiter = ',', $enclosure = '"', $escape_char = '\\')
    {
        $str = '';
        foreach ($data_array as $value) {
            if (strpos($value, $delimiter) !== false ||
                strpos($value, $enclosure) !== false ||
                strpos($value, "\n") !== false ||
                strpos($value, "\r") !== false ||
                strpos($value, "\t") !== false ||
                strpos($value, ' ') !== false) {
                $str2 = $enclosure;
                $escaped = 0;
                $len = strlen($value);
                for ($i=0;$i<$len;$i++) {
                    if ($value[$i] == $escape_char) {
                        $escaped = 1;
                    } else if (!$escaped && $value[$i] == $enclosure) {
                        $str2 .= $enclosure;
                    } else {
                        $escaped = 0;
                    }
                    $str2 .= $value[$i];
                }
                $str2 .= $enclosure;
                $str .= $str2.$delimiter;
            } else {
                $str .= $enclosure.$value.$enclosure.$delimiter;
            }
        }

        $str = substr($str,0,-1);
        return $str;
    }

    public function getExportPath()
    {
        $transaction_directory = $this->_getTransactionDirectory();
        return $transaction_directory . DS . 'export';
    }
}
