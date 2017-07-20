<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use PHPUnit\Framework\TestCase;
use Zend\Validator\Between;
use Zend\Validator\Exception\InvalidArgumentException;

/**
 * @group      Zend_Validator
 */
class BetweenTest extends TestCase
{
    public function providerBasic()
    {
        return [
            [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => true,
                'values' => [1, 10, 100],
            ],
            [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => false,
                'values' => [0, 0.99, 100.01, 101],
            ],
            [
                'min' => 1,
                'max' => 100,
                'inclusive' => false,
                'expected' => false,
                'values' => [0, 1, 100, 101],
            ],
            [
                'min' => 'a',
                'max' => 'z',
                'inclusive' => true,
                'expected' => true,
                'values' => ['a', 'b', 'y', 'z'],
            ],
            [
                'min' => 'a',
                'max' => 'z',
                'inclusive' => false,
                'expected' => false,
                'values' => ['!', 'a', 'z'],
            ],
            [
                'min' => 0,
                'max' => 99999999,
                'inclusive' => true,
                'expected' => false,
                'values' => ['asdasd', 'q'],
            ],
            [
                'min' => 'a',
                'max' => 'zzzzz',
                'inclusive' => true,
                'expected' => false,
                'values' => [0, 10, 548],
            ],
        ];
    }
    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider providerBasic
     * @return void
     */
    public function testBasic($min, $max, $inclusive, $expected, $values)
    {
        $validator = new Between(['min' => $min, 'max' => $max, 'inclusive' => $inclusive]);

        foreach ($values as $input) {
            $this->assertEquals(
                $expected,
                $validator->isValid($input),
                'Failed values: ' . $input . ":" . implode("\n", $validator->getMessages())
            );
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new Between(['min' => 1, 'max' => 10]);
        $this->assertEquals([], $validator->getMessages());
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin()
    {
        $validator = new Between(['min' => 1, 'max' => 10]);
        $this->assertEquals(1, $validator->getMin());
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax()
    {
        $validator = new Between(['min' => 1, 'max' => 10]);
        $this->assertEquals(10, $validator->getMax());
    }

    /**
     * Ensures that getInclusive() returns expected default value
     *
     * @return void
     */
    public function testGetInclusive()
    {
        $validator = new Between(['min' => 1, 'max' => 10]);
        $this->assertEquals(true, $validator->getInclusive());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Between(['min' => 1, 'max' => 10]);
        $this->assertAttributeEquals($validator->getOption('messageTemplates'), 'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = new Between(['min' => 1, 'max' => 10]);
        $this->assertAttributeEquals($validator->getOption('messageVariables'), 'messageVariables', $validator);
    }

    /**
     * @covers Zend\Validator\Between::__construct()
     * @dataProvider constructBetweenValidatorInvalidDataProvider
     *
     * @param array $args
     */
    public function testMissingMinOrMax(array $args)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing option : 'min' and 'max' have to be given");

        new Between($args);
    }

    public function constructBetweenValidatorInvalidDataProvider()
    {
        return [
            [
                ['min' => 1],
            ],
            [
                ['max' => 5],
            ],
        ];
    }

    public function testConstructorCanAcceptInclusiveParameter()
    {
        $validator = new Between(1, 10, false);
        $this->assertFalse($validator->getInclusive());
    }

    public function testConstructWithTravesableOptions()
    {
        $options = new \ArrayObject(['min' => 1, 'max' => 10, 'inclusive' => false]);
        $validator = new Between($options);

        $this->assertTrue($validator->isValid(5));
        $this->assertFalse($validator->isValid(10));
    }
}
