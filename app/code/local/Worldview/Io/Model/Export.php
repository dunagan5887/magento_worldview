<?php

class Worldview_Io_Model_Export extends Worldview_Io_Model_Export_Abstract
{
    protected $_export_file = 'export_file';
    protected $_columns = null;

    protected function setColumnNames($columns_array)
    {
        $this->_columns = array();
        $this->_columns[$this->_export_file] = array();

        foreach ($columns_array as $col_index => $col_value)
        {
            $this->_columns[$this->_export_file][$col_value] = '';
        }

        return $this;
    }

    public function addRowToFile($row)
    {
        $row_as_array = (is_object($row)) ? $row->getData() : $row;
        return parent::_addNewRow($this->_export_file, $row_as_array);
    }

    public function setHeaderRow()
    {
        return parent::_setHeaderRow($this->_export_file);
    }

    public function write()
    {
        return parent::_write();
    }

    protected function _substituteHeaderFieldNames(array $columnNames)
    {
        $substitutions = $this->_getColumnNameSubstitutions();

        if (empty($substitutions))
        {
            return $columnNames;
        }

        foreach ($substitutions as $name_to_remove => $name_to_insert)
        {
            $name_to_remove_key = array_search(($name_to_remove), $columnNames);

            if ($name_to_remove_key !== FALSE)
            {
                $columnNames[] = strtolower($name_to_insert);
                unset($columnNames[$name_to_remove_key]);
            }
        }

        return $columnNames;
    }

    protected function _substituteColumnNames(array $columnNames)
    {
        $substitutions = $this->_getColumnNameSubstitutions();
        if (empty($substitutions))
        {
            return $columnNames;
        }

        foreach ($substitutions as $name_to_remove => $name_to_insert)
        {
            $name_to_remove = strtolower($name_to_remove);
            if (in_array($name_to_remove, array_keys($columnNames)))
            {
                $new_field_value = $columnNames[$name_to_remove];
                $columnNames[$name_to_insert] = $new_field_value;
                unset($columnNames[$name_to_remove]);
            }
        }

        return $columnNames;
    }

    protected function _getColumnNameSubstitutions()
    {
        return array();
    }
}
