<?php

class Worldview_Io_Model_File_Utility extends Mage_Core_Model_Abstract
{
    const OPEN_FILE_PERMISSIONS = 0777;
    const LOCK_FILE_PERMISSIONS = 0644;

    protected $_ioAdapter = null;
    protected $_established_stream = null;

    protected $_csv_delimiter = ',';
    protected $_csv_enclosure = '"';

    public function openFile($filepath, $mode = 'w+', $permissions = self::OPEN_FILE_PERMISSIONS)
    {
        $ioAdapter = $this->_getIoAdapter();

        if ($this->_openStream($filepath, $mode, $permissions))
        {
            return true;
        }

        return false;
    }

    public function closeFile()
    {
        return $this->_getIoAdapter()->streamClose();
    }

    public function lockFile($filepath, $mode = 'w+')
    {
        $ioAdapter = $this->_getIoAdapter();
        if ($this->openFile($filepath, $mode, self::LOCK_FILE_PERMISSIONS))
        {
            return $ioAdapter->streamLock(true);
        }

        return false;
    }

    public function copyFile($source, $destination)
    {
        $ioAdapter = $this->_getIoAdapter();
        $result = $ioAdapter->cp($source, $destination);
        return $result;
    }

    public function moveFile($source, $destination)
    {
        $ioAdapter = $this->_getIoAdapter();
        $result = $ioAdapter->mv($source, $destination);
        return $result;
    }

    public function unlockFile($filepath, $force_unlock = false)
    {
        if (!$this->_streamIsOpen())
        {
            throw new Worldview_Io_Model_Exception("Attempting to unlock file " . $filepath . " without having established a stream.");
        }
        if (!$force_unlock && strcmp($filepath, $this->_established_stream))
        {
            throw new Worldview_Io_Model_Exception("Attempting to unlock file " . $filepath . " but the established stream is " . $this->_established_stream);
        }

        $ioAdapter = $this->_getIoAdapter();
        $ioAdapter->streamUnlock();
        return true;
    }

    public function deleteFile($filepath)
    {
        $ioAdapter = $this->_getIoAdapter();
        $ioAdapter->rm($filepath);
        return true;
    }

    public function getFilesFromDirectory($dir_path)
    {
        $this->openDirectory($dir_path);
        $ioAdapter = $this->_getIoAdapter();
        $files = $ioAdapter->ls(Varien_Io_File::GREP_FILES);
        return $files;
    }

    public function openDirectory($dir_path)
    {
        $ioAdapter = $this->_getIoAdapter();
        return $ioAdapter->open(array('path' => $dir_path));
    }

    public function readCsvRow()
    {
        return $this->_getIoAdapter()->streamReadCsv($this->_csv_delimiter, $this->_csv_enclosure);
    }

    public function writeToFile($data)
    {
        return $this->_getIoAdapter()->streamWrite($data);
    }

    public function writeCSVToFile($data)
    {
        return $this->_getIoAdapter()->streamWriteCsv($data);
    }

    public function getFileMd5HashValue($absolute_filepath)
    {
        return md5_file($absolute_filepath);
    }

    protected function _openStream($filepath, $mode = 'w+', $permissions = self::OPEN_FILE_PERMISSIONS)
    {
        if ($this->_streamIsOpen())
        {
            $this->_closeStream();
        }
        $ioAdapter = $this->_getIoAdapter();

        // Open the directory the file resides in
        $filepath_exploded = explode("/", $filepath);
        array_pop($filepath_exploded);
        $file_directory = implode("/", $filepath_exploded);
        $this->openDirectory($file_directory);

        $ioAdapter->streamOpen($filepath, $mode, $permissions);

        $this->_established_stream = $filepath;
        return true;
    }

    protected function _closeStream()
    {
        $ioAdapter = $this->_getIoAdapter();
        $ioAdapter->streamClose();
        $this->_established_stream = null;
        return true;
    }

    protected function _streamIsOpen()
    {
        return (!is_null($this->_established_stream));
    }

    protected function _getIoAdapter()
    {
        if(is_null($this->_ioAdapter))
        {
            $this->_ioAdapter = Mage::getModel('worldview_io/io_file')->setAllowCreateFolders(true);
        }

        return $this->_ioAdapter;
    }

    public function setCsvDelimiter($delimiter)
    {
        $this->_csv_delimiter = $delimiter;
    }

    public function setCsvEnclosure($enclosure)
    {
        $this->_csv_enclosure = $enclosure;
    }

    public function getEstablishedStream()
    {
        return $this->_established_stream;
    }
}
