<?php

class Zend_EventManager_TestAsset_Functor
{
    public function __invoke($e)
    {
        return __METHOD__;
    }
}
