<?php
/**
 * @see Zend_Service_Ebay_Abstract
 */
require_once 'Zend/Service/Ebay/Abstract.php';

class Zend_Service_Ebay_AbstractConcrete extends Zend_Service_Ebay_Abstract
{
    /**
     * Does nothing
     * Required by the Zend_Service_Ebay_Abstract
     *
     * @param mixed
     */
    public function setClient($client)
    {
    }

    /**
     * Does nothing
     * Required by the Zend_Service_Ebay_Abstract
     *
     * @return void
     */
    public function getClient()
    {
    }

    /**
     * Proxy to Zend_Service_Ebay_Abstract::_optionsToNameValueSyntax
     *
     * @param array|Zend_Config $options
     * @return array
     */
    public function optionsToNameValueSyntax($options)
    {
        return $this->_optionsToNameValueSyntax($options);
    }
}
