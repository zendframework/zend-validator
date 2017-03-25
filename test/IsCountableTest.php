<?php

namespace ZendTest\Validator;

use PHPUnit\Framework\TestCase;
use Zend\Validator\IsCountable;

class IsCountableTest extends TestCase
{
    /** @var IsCountable */
    private $sut;

    protected function setUp()
    {
        $this->sut = new IsCountable();
    }

    public function testArrayIsValid()
    {
        $this->sut->setMin(1);
        $this->sut->setMax(10);

        self::assertTrue($this->sut->isValid(['Foo']), json_encode($this->sut->getMessages()));
        self::assertCount(0, $this->sut->getMessages());
    }

    public function testIteratorIsValid()
    {
        self::assertTrue($this->sut->isValid(new \SplQueue()), json_encode($this->sut->getMessages()));
        self::assertCount(0, $this->sut->getMessages());
    }

    public function testValidEquals()
    {
        $this->sut->setCount(1);

        self::assertTrue($this->sut->isValid(['Foo']));
        self::assertCount(0, $this->sut->getMessages());
    }

    public function testValidMax()
    {
        $this->sut->setMax(1);

        self::assertTrue($this->sut->isValid(['Foo']));
        self::assertCount(0, $this->sut->getMessages());
    }

    public function testValidMin()
    {
        $this->sut->setMin(1);

        self::assertTrue($this->sut->isValid(['Foo']));
        self::assertCount(0, $this->sut->getMessages());
    }

    public function testInvalidNotEquals()
    {
        $this->sut->setCount(2);

        self::assertFalse($this->sut->isValid(['Foo']));
        self::assertCount(1, $this->sut->getMessages());
    }

    /**
     * @expectedException \Zend\Validator\Exception\RuntimeException
     */
    public function testInvalidType()
    {
        $this->sut->isValid(new \stdClass());
    }

    public function testInvalidExceedsMax()
    {
        $this->sut->setMax(1);

        self::assertFalse($this->sut->isValid(['Foo', 'Bar']));
        self::assertCount(1, $this->sut->getMessages());
    }

    public function testInvalidExceedsMin()
    {
        $this->sut->setMin(2);

        self::assertFalse($this->sut->isValid(['Foo']));
        self::assertCount(1, $this->sut->getMessages());
    }
}
