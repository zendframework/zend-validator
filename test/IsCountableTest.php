<?php

namespace ZendTest\Validator;

use PHPUnit\Framework\TestCase;
use Zend\Validator\IsCountable;

class IsCountableTest extends TestCase
{
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

    public function testExactCountOverridesMinAndMax()
    {
        $sut = new IsCountable([
            'count' => 1,
            'min' => 2,
            'max' => 3,
        ]);

        $this->assertSame(1, $sut->getCount());
        $this->assertNull($sut->getMin());
        $this->assertNull($sut->getMax());

        $sut->setOptions([
            'count' => 4,
            'min' => 5,
            'max' => 6,
        ]);

        $this->assertSame(4, $sut->getCount());
        $this->assertNull($sut->getMin());
        $this->assertNull($sut->getMax());
    }
}
