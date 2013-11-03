<?php

class Worldview_Io_Model_Import_Abstract extends Worldview_Io_Model_Transaction_Abstract
{
    const IMPORT_FILE_PERMISSIONS = 0777;
    const ERR_FILE_LOCK = 'Error obtaining lock for: %s';
    const ERROR_READING_ENTIRE_FILE = 'Error reading entire file for: "%s". Entire file was not read.';
    const FILE_ONLY_HAS_HEADER_ROW = 'File "%s" only has only one row including the header, this file will not be processed.';
    const ERR_INVALID_FILENAME = 'Found file with invalid filename: "%s". This file was not processed.';
    const ERR_MISSING_REQUIRED_COLUMN = 'File "%s" is missing a required header column %s.  This file cannot be processed.';
    const ERR_DATA_ROW_IS_INVALID = 'Row %s of file "%s" is invalid and will be skipped due to error: %s';
    const FILE_ROW_HAS_WRONG_NUMBER_OF_FIELDS = 'File %s contains row %s with an incorrect amount of fields: %s';

    protected $_file_delimiter = ',';
    protected $_file_enclosure = '"';
    protected $_error_log_file = 'io_import_error';

    protected $_files;
    protected $_valid_data;

    public function run()
    {
        $this->read();

        $this->_processFiles();

        $this->_importData();
    }

    public function read()
    {
        $ioAdapter = $this->_getIoAdapter();
        $entityImportDir = $this->_getImportDirectory();
        $ioAdapter->open(array('path' => $entityImportDir));
        $files = $ioAdapter->ls(Varien_Io_File::GREP_FILES);

        foreach($files as $file)
        {
            if(!$this->_validateFilename($file['text']))
            {
                $this->logError(sprintf(self::ERR_INVALID_FILENAME, $file['text']));
                unset($file);
                continue;
            }

            $filePath = $entityImportDir. DS . $file['text'];
            $ioAdapter->streamOpen($filePath, 'r', self::IMPORT_FILE_PERMISSIONS);
            if(!$ioAdapter->streamLock(true))
            {
                throw new Worldview_Io_Model_Exception(sprintf(self::ERR_FILE_LOCK, $filePath));
            }

            $this->_readFile($ioAdapter, $file, $filePath);

            $ioAdapter->streamUnlock();
            $ioAdapter->streamClose();
        }
    }

    protected function _readFile(&$ioAdapter, $file, $filePath)
    {
        // Build _files array, cells with header names.
        $headerRow = $ioAdapter->streamReadCsv(
            $this->_file_delimiter,
            $this->_file_enclosure
        );

        $row_num = 0;

        $this->_files[$file['text']][$row_num] = new Varien_Object(array_combine($headerRow, $headerRow));

        while(false !== ($row = $ioAdapter->streamReadCsv($this->_file_delimiter, $this->_file_enclosure)))
        {
            $row_num++;
            $header_columns = count($headerRow);
            $row_columns = count($row);

            if ($header_columns != $row_columns)
            {
                $data_row = serialize($row);
                $this->logError(sprintf(self::FILE_ROW_HAS_WRONG_NUMBER_OF_FIELDS, $file['text'], $row_num, $data_row));
                continue;
            }
            $this->_files[$file['text']][$row_num] = new Varien_Object(array_combine($headerRow, $row));
            unset($row);
        }

        // Does this file have any data?
        if(count($this->_files[$file['text']]) < 2)
        {
            $this->logError(
                sprintf(
                    self::FILE_ONLY_HAS_HEADER_ROW,
                    $file['text']
                )
            );

            unset($file);
        }

        // Check if we reached EOF.  If not, something went wrong.
        if(!feof($ioAdapter->getStream()))
        {
            throw new Worldview_Io_Model_Exception(sprintf(self::ERROR_READING_ENTIRE_FILE, $filePath));
        }
    }

    protected function _processFiles()
    {
        $this->_initValidDataArray();

        // Run through file(s).
        foreach($this->_getFiles() as $fileName => $fileRows)
        {
            // Validate and pop the header row off the stack.
            if(!$this->_validateHeader(array_shift($fileRows), $fileName))
            {
                $this->_actOnInvalidHeaderFile($fileName, $fileRows);
                continue;
            }

            $file = $this->_createFileObject($fileName, $fileRows);

            $this->_processFile($file);
        }
    }

    protected function _processFile(Varien_Object $file)
    {
        $fileName = $file->getFileName();
        $this->_initValidDataArray($fileName);

        $this->_processRows($file);
    }

    protected function _processRows(Varien_Object $file)
    {
        $fileName = $file->getFileName();
        $fileRows = $file->getFileRows();

        // Scan entire shipment to guarantee that we find any orphaned Order Items.
        foreach($fileRows as $rowNum => $rowData)
        {
            $this->_processRow($rowNum, $rowData, $fileName);
        }
    }

    public function _processRow($rowNum, $rowData, $fileName)
    {
        try
        {
            if ($this->_isDataValid($rowData))
            {
                $this->_markDataRowAsValid($rowNum, $rowData, $fileName);
            }
            else
            {
                $this->logError(
                    sprintf(self::ERR_DATA_ROW_IS_INVALID, $rowNum, $fileName, "Row is not valid")
                );
            }
        }
        catch(Exception $e)
        {
            $this->logError(
                sprintf(self::ERR_DATA_ROW_IS_INVALID, $rowNum, $fileName, $e->getMessage())
            );
        }
    }

    protected function _importData()
    {
        // Run through file(s).
        foreach($this->_getFiles() as $fileName => $fileRows)
        {
            foreach ($this->getValidDataRows($fileName) as $rowNum => $rowData)
            {
                $this->_prepareRowForImport($rowNum, $rowData, $fileName);
            }

            $this->_importDataRows($fileName);
        }
    }

    protected function _importDataRows($fileName)
    {
        // Iterate over all of the rows which were deemed valid
        foreach ($this->getValidDataRows($fileName) as $rowNum => $rowData)
        {
            $this->_importDataRow($rowNum, $rowData);
        }
    }

    protected function _getImportDirectory()
    {
        return parent::_getTransactionDirectory() . DS . "import";
    }

    protected function _getFiles()
    {
        return $this->_files;
    }

    protected function _actOnInvalidHeaderFile($fileName, $fileRows)
    {
        if (isset($this->_files[$fileName]))
        {
            unset($this->_files[$fileName]);
        }

        return true;
    }

    protected function _createFileObject($fileName, $fileRows)
    {
        return new Varien_Object(array('file_name' => $fileName, 'file_rows' => $fileRows));
    }

    protected function _validateHeader(Varien_Object $headerRow, $fileName)
    {
        $isHeaderValid = true;
        foreach($this->_getRequiredHeaders() as $requiredHeader)
        {
            if(!$headerRow->hasData($requiredHeader))
            {
                $this->logError(
                    sprintf(self::ERR_MISSING_REQUIRED_COLUMN, $fileName, $requiredHeader)
                );
                $isHeaderValid = false;
            }
        }

        return $isHeaderValid;
    }

    public function getValidDataRows($filename)
    {
        return $this->_valid_data[$filename];
    }

    protected function _markDataRowAsValid($rowNum, $rowData, $fileName)
    {
        if (!isset($this->_valid_data[$fileName]))
        {
            $this->_initValidDataArray($fileName);
        }

        $this->_valid_data[$fileName][$rowNum] = $rowData;
    }

    protected function _initValidDataArray($fileName = null)
    {
        if (is_null($fileName))
        {
            $this->_valid_data = array();
        }
        else
        {
            $this->_valid_data[$fileName] = array();
        }
    }

    /*
     * The following methods are expected to be defined by subclasses. They are not made abstract
     * in order to make a developer's job easier if they don't need to implement any of the following.
     */

    protected function _validateFilename($filename)
    {
        return true;
    }

    protected function _getRequiredHeaders()
    {
        return array();
    }

    protected function _isDataValid($rowData)
    {
        return true;
    }

    protected function _prepareRowForImport($rowNum, $rowData, $fileName)
    {
        return true;
    }
}