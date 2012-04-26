<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage AutoDiscover
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

class Zend_Soap_Server_Proxy
{
    /**
     * @var object
     */
    protected $_service;
    /**
     * Constructor
     * 
     * @param object $service 
     */
    public function  __construct($service)
    {
        $this->_service = $service;
    }
    /**
     * Proxy for the WS-I compliant call
     * 
     * @param  string $name
     * @param  string $arguments
     * @return array 
     */
    public function __call($name, $arguments)
    {
        $params = array();
        if(count($arguments) > 0){
            foreach($arguments[0] as $property => $value){
                $params[$property] = $value;
            }
        }
        $result = call_user_func_array(array($this->_service, $name), $params);
        return array("{$name}Result"=>$result);
    }
}