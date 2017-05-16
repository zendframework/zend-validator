<?php
/**
 * @link      http://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Zend\Validator\Digits;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;
use Zend\Validator\ValidatorPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class ValidatorPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new ValidatorPluginManagerFactory();

        $validators = $factory($container, ValidatorPluginManagerFactory::class);
        $this->assertInstanceOf(ValidatorPluginManager::class, $validators);

        if (method_exists($validators, 'configure')) {
            // zend-servicemanager v3
            $this->assertAttributeSame($container, 'creationContext', $validators);
        } else {
            // zend-servicemanager v2
            $this->assertSame($container, $validators->getServiceLocator());
        }
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $validator = $this->prophesize(ValidatorInterface::class)->reveal();

        $factory = new ValidatorPluginManagerFactory();
        $validators = $factory($container, ValidatorPluginManagerFactory::class, [
            'services' => [
                'test' => $validator,
            ],
        ]);
        $this->assertSame($validator, $validators->get('test'));
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderServiceManagerV2()
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $validator = $this->prophesize(ValidatorInterface::class)->reveal();

        $factory = new ValidatorPluginManagerFactory();
        $factory->setCreationOptions([
            'services' => [
                'test' => $validator,
            ],
        ]);

        $validators = $factory->createService($container->reveal());
        $this->assertSame($validator, $validators->get('test'));
    }

    public function testConfiguresValidatorServicesWhenFound()
    {
        $validator = $this->prophesize(ValidatorInterface::class)->reveal();
        $config = [
            'validators' => [
                'aliases' => [
                    'test' => Digits::class,
                ],
                'factories' => [
                    'test-too' => function ($container) use ($validator) {
                        return $validator;
                    },
                ],
            ],
        ];

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->has('MvcTranslator')->willReturn(false); // necessary due to default initializers

        $factory = new ValidatorPluginManagerFactory();
        $validators = $factory($container->reveal(), 'ValidatorManager');

        $this->assertInstanceOf(ValidatorPluginManager::class, $validators);
        $this->assertTrue($validators->has('test'));
        $this->assertInstanceOf(Digits::class, $validators->get('test'));
        $this->assertTrue($validators->has('test-too'));
        $this->assertSame($validator, $validators->get('test-too'));
    }

    public function testDoesNotConfigureValidatorServicesWhenServiceListenerPresent()
    {
        $validator = $this->prophesize(ValidatorInterface::class)->reveal();
        $config = [
            'validators' => [
                'aliases' => [
                    'test' => Digits::class,
                ],
                'factories' => [
                    'test-too' => function ($container) use ($validator) {
                        return $validator;
                    },
                ],
            ],
        ];

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(true);
        $container->has('config')->shouldNotBeCalled();
        $container->get('config')->shouldNotBeCalled();
        $container->has('MvcTranslator')->willReturn(false); // necessary due to default initializers

        $factory = new ValidatorPluginManagerFactory();
        $validators = $factory($container->reveal(), 'ValidatorManager');

        $this->assertInstanceOf(ValidatorPluginManager::class, $validators);
        $this->assertFalse($validators->has('test'));
        $this->assertFalse($validators->has('test-too'));
    }

    public function testDoesNotConfigureValidatorServicesWhenConfigServiceNotPresent()
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(false);
        $container->get('config')->shouldNotBeCalled();
        $container->has('MvcTranslator')->willReturn(false); // necessary due to default initializers

        $factory = new ValidatorPluginManagerFactory();
        $validators = $factory($container->reveal(), 'ValidatorManager');

        $this->assertInstanceOf(ValidatorPluginManager::class, $validators);
    }

    public function testDoesNotConfigureValidatorServicesWhenConfigServiceDoesNotContainValidatorsConfig()
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn(['foo' => 'bar']);
        $container->has('MvcTranslator')->willReturn(false); // necessary due to default initializers

        $factory = new ValidatorPluginManagerFactory();
        $validators = $factory($container->reveal(), 'ValidatorManager');

        $this->assertInstanceOf(ValidatorPluginManager::class, $validators);
        $this->assertFalse($validators->has('foo'));
    }
}
