<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Validator;

use PHPUnit\Framework\TestCase;
use Zend\Validator\IsCountable;
use Zend\Validator\Exception;

class IsCountableTest extends TestCase
{
    public function conflictingOptionsProvider()
    {
        return [
            'count-min' => [['count' => 10, 'min' => 1]],
            'count-max' => [['count' => 10, 'max' => 10]],
        ];
    }

    /**
     * @dataProvider conflictingOptionsProvider
     */
    public function testConstructorRaisesExceptionWhenProvidedConflictingOptions(array $options)
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('conflicts');
        new IsCountable($options);
    }

    public function conflictingSecondaryOptionsProvider()
    {
        return [
            'count-min' => [['count' => 10], ['min' => 1]],
            'count-max' => [['count' => 10], ['max' => 10]],
        ];
    }

    /**
     * @dataProvider conflictingSecondaryOptionsProvider
     */
    public function testSetOptionsRaisesExceptionWhenProvidedOptionConflictingWithCurrentSettings(
        array $originalOptions,
        array $secondaryOptions
    ) {
        $validator = new IsCountable($originalOptions);
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('conflicts');
        $validator->setOptions($secondaryOptions);
    }

    public function testArrayIsValid()
    {
        $sut = new IsCountable([
            'min' => 1,
            'max' => 10,
        ]);

        $this->assertTrue($sut->isValid(['Foo']), json_encode($sut->getMessages()));
        $this->assertCount(0, $sut->getMessages());
    }

    public function testIteratorIsValid()
    {
        $sut = new IsCountable();

        $this->assertTrue($sut->isValid(new \SplQueue()), json_encode($sut->getMessages()));
        $this->assertCount(0, $sut->getMessages());
    }

    public function testValidEquals()
    {
        $sut = new IsCountable([
            'count' => 1,
        ]);

        $this->assertTrue($sut->isValid(['Foo']));
        $this->assertCount(0, $sut->getMessages());
    }

    public function testValidMax()
    {
        $sut = new IsCountable([
            'max' => 1,
        ]);

        $this->assertTrue($sut->isValid(['Foo']));
        $this->assertCount(0, $sut->getMessages());
    }

    public function testValidMin()
    {
        $sut = new IsCountable([
            'min' => 1,
        ]);

        $this->assertTrue($sut->isValid(['Foo']));
        $this->assertCount(0, $sut->getMessages());
    }

    public function testInvalidNotEquals()
    {
        $sut = new IsCountable([
            'count' => 2,
        ]);

        $this->assertFalse($sut->isValid(['Foo']));
        $this->assertCount(1, $sut->getMessages());
    }

    public function testInvalidType()
    {
        $sut = new IsCountable();

        $this->assertFalse($sut->isValid(new \stdClass()));
        $this->assertCount(1, $sut->getMessages());
    }

    public function testInvalidExceedsMax()
    {
        $sut = new IsCountable([
            'max' => 1,
        ]);

        $this->assertFalse($sut->isValid(['Foo', 'Bar']));
        $this->assertCount(1, $sut->getMessages());
    }

    public function testInvalidExceedsMin()
    {
        $sut = new IsCountable([
            'min' => 2,
        ]);

        $this->assertFalse($sut->isValid(['Foo']));
        $this->assertCount(1, $sut->getMessages());
    }
}
