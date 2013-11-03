<?php

class Worldview_Io_Model_File_Xml_Validator extends Mage_Core_Model_Abstract
{
    const ERROR_XML_XSD_VALIDATION_FAILED = 'Failed to validate xml file %s against xsd template %s: %s';

    public function validateFile($absolute_filepath_to_validate, $xsdToValidateAgainst)
    {
        $file_string = file_get_contents($absolute_filepath_to_validate);
        $utf8_string = utf8_encode($file_string);
        $document = new DOMDocument();
        $document->loadXML($utf8_string);

        if ($document->schemaValidate($xsdToValidateAgainst))
        {
            return true;
        }

        // Validation failed, fetch the errors which were thrown
        $error_string = $this->_getXmlValidationErrorsString();
        $exception_message = sprintf(self::ERROR_XML_XSD_VALIDATION_FAILED, $absolute_filepath_to_validate, $xsdToValidateAgainst, $error_string);
        Mage::log($exception_message, null, 'xsd');
        throw new Worldview_Io_Model_Exception($exception_message);
        return false;
    }

    protected function _getXmlValidationErrorsString() {
        $errors_array = array();

        $thrown_xml_errors = libxml_get_errors();
        foreach ($thrown_xml_errors as $error) {
            $errors_array[] = $this->_getErrorMessage($error);
        }
        libxml_clear_errors();

        return implode("\n", $errors_array);
    }

    protected function _getErrorMessage($error)
    {
        $error_message = 'Error of code ' . $error->code . ': ' . trim($error->message);
        if ($error->file) {
            $error_message .= ' on line ' . $error->line . ' of file ' . $error->file;
        }

        return $error_message;
    }
}
