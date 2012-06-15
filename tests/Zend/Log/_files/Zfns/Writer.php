<?php

namespace Zfns;

class Writer extends \Zend_Log_Writer_Abstract
{
    /**
     * Construct a Zend_Log driver
     *
     * @param  array|\Zend_Config $config
     * @return \Zend_Log_FactoryInterface
     */
    static public function factory($config)
    {
        return new self();
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  log data event
     * @return void
     */
    protected function _write($event)
    {
        // Nothing here
    }
}
