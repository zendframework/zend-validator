<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\ValidatorPluginManager;
use Zend\ServiceManager\ServiceManager;

/**
 * @group      Zend_Validator
 */
class ValidatorPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->validators = new ValidatorPluginManager(new ServiceManager);
    }

    public function testAllowsInjectingTranslator()
    {
        $translator = $this->getMock('ZendTest\Validator\TestAsset\Translator');

        $slContents = [['MvcTranslator', $translator]];
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
            ->method('get')
            ->will($this->returnValueMap($slContents));
        $serviceLocator->expects($this->once())
            ->method('has')
            ->with($this->equalTo('MvcTranslator'))
            ->will($this->returnValue(true));

        $validators = new ValidatorPluginManager($serviceLocator);

        $validator = $validators->get('notempty');
        $this->assertEquals($translator, $validator->getTranslator());
    }

    public function testNoTranslatorInjectedWhenTranslatorIsNotPresent()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
            ->method('has')
            ->with($this->equalTo('MvcTranslator'))
            ->will($this->returnValue(false));

        $validators = new ValidatorPluginManager($serviceLocator);

        $validator = $validators->get('notempty');
        $this->assertNull($validator->getTranslator());
    }

    public function testRegisteringInvalidValidatorRaisesException()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidServiceException');
        $this->validators->setService('test', $this);
        $this->validators->get('test');
    }

    public function testLoadingInvalidValidatorRaisesException()
    {
        $this->validators->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidServiceException');
        $this->validators->get('test');
    }

    public function testInjectedValidatorPluginManager()
    {
        $validator = $this->validators->get('explode');
        $this->assertSame($this->validators, $validator->getValidatorPluginManager());
    }
}
