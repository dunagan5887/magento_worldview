<?php

class Worldview_Io_Model_Io_File extends Varien_Io_File
{
    public function getStream()
    {
        return $this->_streamHandler;
    }

    public function streamReadCsv($delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        if (!$this->_streamHandler) {
            return false;
        }

        $csv_data = @fgetcsv($this->_streamHandler, 0, $delimiter, $enclosure, $escape);

        if (!is_array($csv_data))
        {
            return false;
        }

        $escape_and_enclosure = $escape.$enclosure;

        // Unescape the enclosures
        foreach ($csv_data as $index => $value)
        {
            $unescaped_value = str_replace($escape_and_enclosure, $enclosure, $value);
            $csv_data[$index] = $unescaped_value;
        }

        return $csv_data;
    }
}
