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
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */

require_once 'Zend/XmlRpc/Request.php';
require_once 'Zend/XmlRpc/Value/Nil.php';
require_once 'Zend/XmlRpc/Value/String.php';


/**
 * Test case for Zend_XmlRpc_Request
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class Zend_XmlRpc_RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_XmlRpc_Request object
     * @var Zend_XmlRpc_Request
     */
    protected $_request;

    /**
     * Setup environment
     */
    public function setUp()
    {
        $this->_request = new Zend_XmlRpc_Request();
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        unset($this->_request);
    }

    /**
     * get/setMethod() test
     */
    public function testMethod()
    {
        $this->assertTrue($this->_request->setMethod('testMethod'));
        $this->assertTrue($this->_request->setMethod('testMethod9'));
        $this->assertTrue($this->_request->setMethod('test.Method'));
        $this->assertTrue($this->_request->setMethod('test_method'));
        $this->assertTrue($this->_request->setMethod('test:method'));
        $this->assertTrue($this->_request->setMethod('test/method'));
        $this->assertFalse($this->_request->setMethod('testMethod-bogus'));

        $this->assertEquals('test/method', $this->_request->getMethod());
    }


    /**
     * __construct() test
     */
    public function testConstructorOptionallySetsMethodAndParams()
    {
        $r = new Zend_XmlRpc_Request();
        $this->assertEquals('', $r->getMethod());
        $this->assertEquals(array(), $r->getParams());

        $method = 'foo.bar';
        $params = array('baz', 1, array('foo' => 'bar'));
        $r = new Zend_XmlRpc_Request($method, $params);
        $this->assertEquals($method, $r->getMethod());
        $this->assertEquals($params, $r->getParams());
    }


    /**
     * addParam()/getParams() test
     */
    public function testAddParam()
    {
        $this->_request->addParam('string1');
        $params = $this->_request->getParams();
        $this->assertEquals(1, count($params));
        $this->assertEquals('string1', $params[0]);

        $this->_request->addParam('string2');
        $params = $this->_request->getParams();
        $this->assertSame(2, count($params));
        $this->assertSame('string1', $params[0]);
        $this->assertSame('string2', $params[1]);

        $this->_request->addParam(new Zend_XmlRpc_Value_String('foo'));
        $params = $this->_request->getParams();
        $this->assertSame(3, count($params));
        $this->assertSame('string1', $params[0]);
        $this->assertSame('string2', $params[1]);
        $this->assertSame('foo', $params[2]->getValue());
    }

    public function testAddDateParamGeneratesCorrectXml()
    {
        $time = time();
        $this->_request->addParam($time, Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);
        $this->_request->setMethod('foo.bar');
        $xml = $this->_request->saveXml();
        $sxl = new SimpleXMLElement($xml);
        $param = $sxl->params->param->value;
        $type  = 'dateTime.iso8601';
        $this->assertTrue(isset($param->{$type}), var_export($param, 1));
        $this->assertEquals($time, strtotime((string) $param->{$type}));
    }

    /**
     * setParams()/getParams() test
     */
    public function testSetParams()
    {
        $params = array(
            'string1',
            true,
            array('one', 'two')
        );
        $this->_request->setParams($params);
        $returned = $this->_request->getParams();
        $this->assertSame($params, $returned);

        $params = array(
            'string2',
            array('two', 'one')
        );
        $this->_request->setParams($params);
        $returned = $this->_request->getParams();
        $this->assertSame($params, $returned);

        $params = array(array('value' => 'foobar'));
        $this->_request->setParams($params);
        $this->assertSame(array('foobar'), $this->_request->getParams());
        $this->assertSame(array('string'), $this->_request->getTypes());

        $null = new Zend_XmlRpc_Value_Nil();
        $this->_request->setParams('foo', 1, $null);
        $this->assertSame(array('foo', 1, $null), $this->_request->getParams());
        $this->assertSame(array('string', 'int', 'nil'), $this->_request->getTypes());

        $this->assertNull($this->_request->setParams(), 'Call without argument returns null');
    }

    /**
     * loadXml() test
     */
    public function testLoadXml()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $mCall = $dom->appendChild($dom->createElement('methodCall'));
        $mName = $mCall->appendChild($dom->createElement('methodName', 'do.Something'));
        $params = $mCall->appendChild($dom->createElement('params'));
        $param1 = $params->appendChild($dom->createElement('param'));
            $value1 = $param1->appendChild($dom->createElement('value'));
            $value1->appendChild($dom->createElement('string', 'string1'));

        $param2 = $params->appendChild($dom->createElement('param'));
            $value2 = $param2->appendChild($dom->createElement('value'));
            $value2->appendChild($dom->createElement('boolean', 1));


        $xml = $dom->saveXml();

        try {
            $parsed = $this->_request->loadXml($xml);
        } catch (Exception $e) {
            $this->fail('Failed to parse XML: ' . $e->getMessage());
        }
        $this->assertTrue($parsed, $xml);

        $this->assertEquals('do.Something', $this->_request->getMethod());
        $test = array('string1', true);
        $params = $this->_request->getParams();
        $this->assertSame($test, $params);

        try {
            $parsed = $this->_request->loadXml('foo');
        } catch (Exception $e) {
            $this->fail('Failed to parse XML: ' . $e->getMessage());
        }
        $this->assertFalse($parsed, 'Parsed non-XML string?');
    }

    public function testPassingInvalidTypeToLoadXml()
    {
        $this->assertFalse($this->_request->loadXml(new stdClass()));
        $this->assertTrue($this->_request->isFault());
        $this->assertSame(635, $this->_request->getFault()->getCode());
        $this->assertSame('Invalid XML provided to request', $this->_request->getFault()->getMessage());
    }

    public function testLoadingXmlWithoutMethodNameElement()
    {
        $this->assertFalse($this->_request->loadXml('<empty/>'));
        $this->assertTrue($this->_request->isFault());
        $this->assertSame(632, $this->_request->getFault()->getCode());
        $this->assertSame("Invalid request, no method passed; request must contain a 'methodName' tag",
            $this->_request->getFault()->getMessage());
    }

    public function testLoadingXmlWithInvalidParams()
    {
        $this->assertFalse($this->_request->loadXml(
            '<methodCall>'
          . '<methodName>foo</methodName>'
          . '<params><param/><param/><param><foo/></param></params>'
          . '</methodCall>'));
        $this->assertTrue($this->_request->isFault());
        $this->assertSame(633, $this->_request->getFault()->getCode());
        $this->assertSame(
            'Param must contain a value',
            $this->_request->getFault()->getMessage());
    }

    public function testExceptionWhileLoadingXmlParamValueIsHandled()
    {
        $this->assertFalse($this->_request->loadXml(
            '<methodCall>'
          . '<methodName>foo</methodName>'
          . '<params><param><value><foo/></value></param></params>'
          . '</methodCall>'));
        $this->assertTrue($this->_request->isFault());
        $this->assertSame(636, $this->_request->getFault()->getCode());
        $this->assertSame(
            'Error creating xmlrpc value',
            $this->_request->getFault()->getMessage());
    }

    /**
     * isFault() test
     */
    public function testIsFault()
    {
        $this->assertFalse($this->_request->isFault());
        $this->_request->loadXml('foo');
        $this->assertTrue($this->_request->isFault());
    }

    /**
     * getFault() test
     */
    public function testGetFault()
    {
        $fault = $this->_request->getFault();
        $this->assertTrue(null === $fault);
        $this->_request->loadXml('foo');
        $fault = $this->_request->getFault();
        $this->assertTrue($fault instanceof Zend_XmlRpc_Fault);
    }

    /**
     * helper for saveXml() and __toString() tests
     *
     * @param string $xml
     * @return void
     */
    protected function _testXmlRequest($xml, $argv)
    {
        try {
            $sx = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $this->fail('Invalid XML returned');
        }

        $result = $sx->xpath('//methodName');
        $count = 0;
        while (list( , $node) = each($result)) {
            ++$count;
        }
        $this->assertEquals(1, $count, $xml);

        $result = $sx->xpath('//params');
        $count = 0;
        while (list( , $node) = each($result)) {
            ++$count;
        }
        $this->assertEquals(1, $count, $xml);

        try {
            $methodName = (string) $sx->methodName;
            $params = array(
                (string) $sx->params->param[0]->value->string,
                (bool) $sx->params->param[1]->value->boolean
            );
        } catch (Exception $e) {
            $this->fail('One or more inconsistencies parsing generated XML: ' . $e->getMessage());
        }

        $this->assertEquals('do.Something', $methodName);
        $this->assertSame($argv, $params, $xml);
    }

    /**
     * testSaveXML() test
     */
    public function testSaveXML()
    {
        $argv = array('string', true);
        $this->_request->setMethod('do.Something');
        $this->_request->setParams($argv);
        $xml = $this->_request->saveXml();
        $this->_testXmlRequest($xml, $argv);
    }

    /**
     * __toString() test
     */
    public function test__toString()
    {
        $argv = array('string', true);
        $this->_request->setMethod('do.Something');
        $this->_request->setParams($argv);
        $xml = $this->_request->__toString();
        $this->_testXmlRequest($xml, $argv);
    }

    /**
     * Test encoding settings
     */
    public function testSetGetEncoding()
    {
        $this->assertEquals('UTF-8', $this->_request->getEncoding());
        $this->assertEquals('UTF-8', Zend_XmlRpc_Value::getGenerator()->getEncoding());
        $this->assertSame($this->_request, $this->_request->setEncoding('ISO-8859-1'));
        $this->assertEquals('ISO-8859-1', $this->_request->getEncoding());
        $this->assertEquals('ISO-8859-1', Zend_XmlRpc_Value::getGenerator()->getEncoding());
    }

    /**
     * @group ZF-12293
+     *
+     * Test should remain, but is defunct since DOCTYPE presence should return FALSE
+     * from loadXml()
     */
    public function testDoesNotAllowExternalEntities()
    {
        $payload = file_get_contents(dirname(__FILE__) . '/_files/ZF12293-request.xml');
        $payload = sprintf($payload, 'file://' . realpath(dirname(__FILE__) . '/_files/ZF12293-payload.txt'));
        $this->_request->loadXml($payload);
        $method = $this->_request->getMethod();
        $this->assertTrue(empty($method));
        if (is_string($method)) {
            $this->assertNotContains('Local file inclusion', $method);
        }
    }

     public function testShouldDisallowsDoctypeInRequestXmlAndReturnFalseOnLoading()
     {
         $payload = file_get_contents(dirname(__FILE__) . '/_files/ZF12293-request.xml');
         $payload = sprintf($payload, 'file://' . realpath(dirname(__FILE__) . '/_files/ZF12293-payload.txt'));
         $this->assertFalse($this->_request->loadXml($payload));
     }
}
