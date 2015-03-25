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
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Abstract master class for extension.
 */
require_once 'Zend/View/Abstract.php';


/**
 * Concrete class for handling view scripts.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @method Zend_View_Helper_BaseUrl baseUrl()
 * @method Zend_View_Helper_Currency currency()
 * @method Zend_View_Helper_Cycle cycle()
 * @method Zend_View_Helper_Doctype doctype()
 * @method Zend_View_Helper_Fieldset fieldset()
 * @method Zend_View_Helper_Form form()
 * @method Zend_View_Helper_FormButton formButton()
 * @method Zend_View_Helper_FormCheckbox formCheckbox()
 * @method Zend_View_Helper_FormElement formElement()
 * @method Zend_View_Helper_FormErrors formErrors()
 * @method Zend_View_Helper_FormFile formFile()
 * @method Zend_View_Helper_FormHidden formHidden()
 * @method Zend_View_Helper_FormImage formImage()
 * @method Zend_View_Helper_FormLabel formLabel()
 * @method Zend_View_Helper_FormMultiCheckbox formMultiCheckbox()
 * @method Zend_View_Helper_FormNote formNote()
 * @method Zend_View_Helper_FormPassword formPassword()
 * @method Zend_View_Helper_FormRadio formRadio()
 * @method Zend_View_Helper_FormReset formReset()
 * @method Zend_View_Helper_FormSelect formSelect()
 * @method Zend_View_Helper_FormSubmit formSubmit()
 * @method Zend_View_Helper_FormText formText()
 * @method Zend_View_Helper_FormTextarea formTextarea()
 * @method Zend_View_Helper_Gravatar gravatar()
 * @method Zend_View_Helper_HeadLink headLink()
 * @method Zend_View_Helper_HeadMeta headMeta()
 * @method Zend_View_Helper_HeadScript headScript()
 * @method Zend_View_Helper_HeadStyle headStyle()
 * @method Zend_View_Helper_HeadTitle headTitle()
 * @method Zend_View_Helper_HtmlElement htmlElement()
 * @method Zend_View_Helper_HtmlFlash htmlFlash()
 * @method Zend_View_Helper_HtmlList htmlList()
 * @method Zend_View_Helper_HtmlObject htmlObject()
 * @method Zend_View_Helper_HtmlPage htmlPage()
 * @method Zend_View_Helper_HtmlQuicktime htmlQuicktime()
 * @method Zend_View_Helper_InlineScript inlineScript()
 * @method Zend_View_Helper_Interface interface()
 * @method Zend_View_Helper_Json json($data, $keepLayouts = false, $encodeData = true)
 * @method Zend_View_Helper_Layout layout()
 * @method Zend_View_Helper_Navigation navigation()
 * @method Zend_View_Helper_PaginationControl paginationControl()
 * @method Zend_View_Helper_Partial partial()
 * @method Zend_View_Helper_PartialLoop partialLoop()
 * @method Zend_View_Helper_Placeholder placeholder()
 * @method Zend_View_Helper_RenderToPlaceholder renderToPlaceholder()
 * @method Zend_View_Helper_ServerUrl serverUrl()
 * @method Zend_View_Helper_Translate translate()
 * @method Zend_View_Helper_Url url()
 * @method Zend_View_Helper_UserAgent userAgent()
 */
class Zend_View extends Zend_View_Abstract
{
    /**
     * Whether or not to use streams to mimic short tags
     * @var bool
     */
    private $_useViewStream = false;

    /**
     * Whether or not to use stream wrapper if short_open_tag is false
     * @var bool
     */
    private $_useStreamWrapper = false;

    /**
     * Constructor
     *
     * Register Zend_View_Stream stream wrapper if short tags are disabled.
     *
     * @param  array $config
     * @return void
     */
    public function __construct($config = array())
    {
        $this->_useViewStream = (bool) ini_get('short_open_tag') ? false : true;
        if ($this->_useViewStream) {
            if (!in_array('zend.view', stream_get_wrappers())) {
                require_once 'Zend/View/Stream.php';
                stream_wrapper_register('zend.view', 'Zend_View_Stream');
            }
        }

        if (array_key_exists('useStreamWrapper', $config)) {
            $this->setUseStreamWrapper($config['useStreamWrapper']);
        }

        parent::__construct($config);
    }

    /**
     * Set flag indicating if stream wrapper should be used if short_open_tag is off
     *
     * @param  bool $flag
     * @return Zend_View
     */
    public function setUseStreamWrapper($flag)
    {
        $this->_useStreamWrapper = (bool) $flag;
        return $this;
    }

    /**
     * Should the stream wrapper be used if short_open_tag is off?
     *
     * @return bool
     */
    public function useStreamWrapper()
    {
        return $this->_useStreamWrapper;
    }

    /**
     * Includes the view script in a scope with only public $this variables.
     *
     * @param string The view script to execute.
     */
    protected function _run()
    {
        if ($this->_useViewStream && $this->useStreamWrapper()) {
            include 'zend.view://' . func_get_arg(0);
        } else {
            include func_get_arg(0);
        }
    }
}
