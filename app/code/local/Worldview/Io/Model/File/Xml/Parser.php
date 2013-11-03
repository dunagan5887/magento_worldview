<?php

class Worldview_Io_Model_File_Xml_Parser extends Mage_Core_Model_Abstract
{
    public function getXmlStructureByFilepath($absolute_xml_filepath)
    {
        try
        {
            $simple_xml_config = new Varien_Simplexml_Config($absolute_xml_filepath);
            $root_xml_object = $simple_xml_config->getNode();

            return $root_xml_object;
        }
        catch(Exception $e)
        {
            // TODO Put something here
        }
        return false;
    }
}
