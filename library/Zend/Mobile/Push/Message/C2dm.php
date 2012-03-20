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
 * @package    Zend_Mobile
 * @subpackage Zend_Mobile_Push
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Mobile_Push_Message_Abstract **/
require_once 'Zend/Mobile/Push/Message/Abstract.php';

/**
 * C2dm Message
 *
 * @category   Zend
 * @package    Zend_Mobile
 * @subpackage Zend_Mobile_Push
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Mobile_Push_Message_C2dm extends Zend_Mobile_Push_Message_Abstract
{
    /**
     * Data key value pairs
     * 
     * @var array
     */
    protected $_data = array();

    /**
     * Delay While Idle
     *
     * @var boolean
     */
    protected $_delay = false;

    /**
     * Add Data
     *
     * @param string $key
     * @param string $value
     * @return Zend_Mobile_Push_Message_C2dm
     * @throws Zend_Mobile_Push_Message_Exception
     */
    public function addData($key, $value)
    {
        if (!is_string($key)) {
            throw new Zend_Mobile_Push_Message_Exception('$key is not a string');
        }
        if (!is_scalar($value)) {
            throw new Zend_Mobile_Push_Message_Exception('$value is not a string');
        }
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * Set Data
     *
     * @param array $data
     * @return Zend_Mobile_Push_Message_C2dm
     * @throws Zend_Mobile_Push_Message_Exception
     */
    public function setData(array $data)
    {
        $this->clearData();
        foreach ($data as $k => $v) {
            $this->addData($k, $v);
        }
        return $this;
    }

    /**
     * Clear Data
     *
     * @return Zend_Mobile_Push_Message_C2dm
     */
    public function clearData()
    {
        $this->_data = array();
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Set Delay While Idle
     *
     * @param boolean $delay
     * @return Zend_Mobile_Push_Message_C2dm
     * @throws Zend_Mobile_Push_Message_Exception
     */
    public function setDelayWhileIdle($delay)
    {
        if (!is_bool($delay)) {
            throw new Zend_Mobile_Push_Message_Exception('$delay must be boolean');
        }
        $this->_delay = $delay;
        return $this;
    }

    /**
     * Get Delay While Idle
     *
     * @return boolean
     */
    public function getDelayWhileIdle()
    {
        return $this->_delay;
    }

    /**
     * Validate this is a proper C2dm message
     * Does not validate size.
     *
     * @return boolean
     */
    public function validate()
    {
        if (!is_string($this->_token) || strlen($this->_token) === 0) {
            return false;
        }
        if (!is_scalar($this->_id) || strlen($this->_id) === 0) {
            return false;
        }
        return true;
    }
}
