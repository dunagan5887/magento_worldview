<?php

class Worldview_Io_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_DECLARATION_REGEX = '/<\?xml.*\?>/';

    protected $_existing_data_arrays = array();

    public function dataValueAlreadyExists($value_to_test, $model, $field, $add_field_to_select = false)
    {
        if (!isset($this->_existing_data_arrays[$model][$field]))
        {
            $this->buildExistingDataArray($model, $field, $add_field_to_select);
        }
        return in_array($value_to_test, $this->_existing_data_arrays[$model][$field]);
    }

    public function buildExistingDataArray($model, $field, $add_field_to_select = false)
    {
        if (!isset($this->_existing_data_arrays[$model]))
        {
            $this->_existing_data_arrays[$model] = array();
        }
        if (!isset($this->_existing_data_arrays[$model][$field]))
        {
            $this->_existing_data_arrays[$model][$field] = array();
        }

        $object_collection = Mage::getModel($model)
                                ->getCollection();

        if ($add_field_to_select)
        {
            $object_collection = $object_collection->addFieldToSelect($field);
        }
        else
        {
            $object_collection = $object_collection->addAttributeToSelect($field);
        }

        foreach($object_collection->getItems() as $item)
        {
            $this->_existing_data_arrays[$model][$field][] = $item->getData($field);
        }

        return $this->_existing_data_arrays[$model][$field];
    }

    public function testValueExistence($value_to_test, $model, $field, $add_field_to_select = false)
    {
        $object_collection = Mage::getModel($model)
                                ->getCollection();

        if ($add_field_to_select)
        {
            $object_collection = $object_collection->addFieldToFilter($field, $value_to_test);
        }
        else
        {
            $object_collection = $object_collection->addAttributeToFilter($field, $value_to_test);
        }

        $entity = $object_collection->getFirstItem();
        return $entity->getId();
    }

    public function getUrlSpecialCharacters()
    {
        return array('.', ' ', '$', '&', '`', ':', '<', '>', '[', ']', '{', '}', '"', '+', '#', '%', '@', '/', ';', '=', '?', '\\', '^', '|', '~', '\'', ',');
    }

    public function buildCsvFileLine($fields, $delimiter = ',', $enclosure = '"')
    {
        $str = '';
        $escape_char = '\\';

        foreach ($fields as $value) {
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
                        $str2 .= $escape_char;
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

    public function getUnescapedSimpleXmlOutput(SimpleXMLElement $simple_xml_element)
    {
        $xml = $simple_xml_element->asXML();
        // Remove the xml declaration line
        $unescaped_xml = preg_replace(self::XML_DECLARATION_REGEX, '', $xml);
        // Unescape the contents
        $unescaped_xml = html_entity_decode($unescaped_xml);
        $unescaped_xml = trim($unescaped_xml);

        return $unescaped_xml;
    }

    public function getSimpleXmlElementChild(SimpleXMLElement $simple_xml_element, $child_to_return)
    {
        $children = $simple_xml_element->children();

        foreach ($children as $name => $child)
        {
            if ($child_to_return == $name)
            {
                return $child;
            }
        }

        return false;
    }

    public function getSimpleXmlElementChildValue(SimpleXMLElement $simple_xml_element, $child_value_to_return)
    {
        $child = $this->getSimpleXmlElementChild($simple_xml_element, $child_value_to_return);

        if ($child == false)
        {
            return false;
        }

        $as_array = $child->asArray();
        if (is_array($as_array))
        {
            return reset($as_array);
        }

        return $as_array;
    }

    public function getSimpleXmlElementAttribute(SimpleXMLElement $simple_xml_element, $attribute_to_return)
    {
        $element_attributes = $simple_xml_element->attributes();

        foreach ($element_attributes as $attribute_name => $attribute)
        {
            if ($attribute_name == $attribute_to_return)
            {
                return $attribute->__toString();
            }
        }

        return '';
    }

    public function getSimpleXmlElementChildMultiples(SimpleXMLElement $simple_xml_element, $child_node_name)
    {
        $children_to_return = array();
        $index_counter = 0;
        foreach ($simple_xml_element->$child_node_name as $index => $row_node)
        {
            // $index is always $this->_xml_row_node_name, so we need to define our own indexes
            $children_to_return[$index_counter++] = $row_node;
        }
        reset($children_to_return);

        return $children_to_return;
    }

    public function convertRowToString($row)
    {
        if (is_array($row))
        {
            return serialize($row);
        }

        if ($row instanceof SimpleXMLElement)
        {
            return $row->asXML();
        }

        return $row->__toString();
    }
}
