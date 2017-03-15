<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Zend\Validator\Exception\RuntimeException;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\ServiceManager;

/**
 * @group      Zend_Validator
 */
class ValidatorPluginManagerTest extends TestCase
{
    public function setUp()
    {
        $this->validators = new ValidatorPluginManager(new ServiceManager);
    }

    public function testAllowsInjectingTranslator()
    {
        $translator = $this->prophesize(TestAsset\Translator::class)->reveal();

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('MvcTranslator')->willReturn(true);
        $container->get('MvcTranslator')->willReturn($translator);

        $validators = new ValidatorPluginManager($container->reveal());

        $validator = $validators->get('notempty');
        $this->assertEquals($translator, $validator->getTranslator());
    }

    public function testNoTranslatorInjectedWhenTranslatorIsNotPresent()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('MvcTranslator')->willReturn(false);

        $validators = new ValidatorPluginManager($container->reveal());

        $validator = $validators->get('notempty');
        $this->assertNull($validator->getTranslator());
    }

    public function testRegisteringInvalidValidatorRaisesException()
    {
        try {
            $this->validators->setService('test', $this);
        } catch (InvalidServiceException $e) {
            $this->assertContains(ValidatorInterface::class, $e->getMessage());
        } catch (RuntimeException $e) {
            $this->assertContains(ValidatorInterface::class, $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(sprintf(
                'Unexpected exception of type "%s" when testing for invalid validator types',
                get_class($e)
            ));
        }
    }

    public function testLoadingInvalidValidatorRaisesException()
    {
        $this->validators->setInvokableClass('test', get_class($this));
        try {
            $this->validators->get('test');
        } catch (InvalidServiceException $e) {
            $this->assertContains(ValidatorInterface::class, $e->getMessage());
        } catch (RuntimeException $e) {
            $this->assertContains(ValidatorInterface::class, $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(sprintf(
                'Unexpected exception of type "%s" when testing for invalid validator types',
                get_class($e)
            ));
        }
    }

    public function testInjectedValidatorPluginManager()
    {
        $validator = $this->validators->get('explode');
        $this->assertSame($this->validators, $validator->getValidatorPluginManager());
    }
}
