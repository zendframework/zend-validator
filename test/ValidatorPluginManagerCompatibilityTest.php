<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Zend\Validator\Exception\RuntimeException;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Test\CommonPluginManagerTrait;

class ValidatorPluginManagerCompatibilityTest extends TestCase
{
    use CommonPluginManagerTrait;

    protected function getPluginManager()
    {
        return new ValidatorPluginManager(new ServiceManager());
    }

    protected function getV2InvalidPluginException()
    {
        return RuntimeException::class;
    }

    protected function getInstanceOf()
    {
        return ValidatorInterface::class;
    }

    public function aliasProvider()
    {
        $pluginManager = $this->getPluginManager();
        $r = new ReflectionProperty($pluginManager, 'aliases');
        $r->setAccessible(true);
        $aliases = $r->getValue($pluginManager);

        foreach ($aliases as $alias => $target) {
            // Skipping due to required options
            if (strpos($target, '\\Barcode')) {
                continue;
            }

            // Skipping due to required options
            if (strpos($target, '\\Between')) {
                continue;
            }

            // Skipping due to required options
            if (strpos($target, '\\Db\\')) {
                continue;
            }

            // Skipping due to required options
            if (strpos($target, '\\File\\ExcludeExtension')) {
                continue;
            }

            // Skipping due to required options
            if (strpos($target, '\\File\\Extension')) {
                continue;
            }

            // Skipping due to required options
            if (strpos($target, '\\File\\FilesSize')) {
                continue;
            }

            // Skipping due to required options
            if (strpos($target, '\\Regex')) {
                continue;
            }

            yield $alias => [$alias, $target];
        }
    }
}
