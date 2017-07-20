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
            'inclusive-int-valid-floor' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => true,
                'value' => 1,
            ],
            'inclusive-int-valid-between' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => true,
                'value' => 10,
            ],
            'inclusive-int-valid-ceiling' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => true,
                'value' => 100,
            ],
            'inclusive-int-invaild-below' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => false,
                'value' => 0,
            ],
            'inclusive-int-invalid-below-fractional' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => false,
                'value' => 0.99,
            ],
            'inclusive-int-invalid-above-fractional' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => false,
                'value' => 100.01,
            ],
            'inclusive-int-invalid-above' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => true,
                'expected' => false,
                'value' => 101,
            ],
            'exclusive-int-invalid-below' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => false,
                'expected' => false,
                'value' => 0,
            ],
            'exclusive-int-invalid-floor' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => false,
                'expected' => false,
                'value' => 1,
            ],
            'exclusive-int-invalid-ceiling' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => false,
                'expected' => false,
                'value' => 100,
            ],
            'exclusive-int-invalid-above' => [
                'min' => 1,
                'max' => 100,
                'inclusive' => false,
                'expected' => false,
                'value' => 101,
            ],
            'inclusive-string-valid-floor' => [
                'min' => 'a',
                'max' => 'z',
                'inclusive' => true,
                'expected' => true,
                'value' => 'a',
            ],
            'inclusive-string-valid-between' => [
                'min' => 'a',
                'max' => 'z',
                'inclusive' => true,
                'expected' => true,
                'value' => 'm',
            ],
            'inclusive-string-valid-ceiling' => [
                'min' => 'a',
                'max' => 'z',
                'inclusive' => true,
                'expected' => true,
                'value' => 'z',
            ],
            'exclusive-string-invalid-out-of-range' => [
                'min' => 'a',
                'max' => 'z',
                'inclusive' => false,
                'expected' => false,
                'value' => '!',
            ],
            'exclusive-string-invalid-floor' => [
                'min' => 'a',
                'max' => 'z',
                'inclusive' => false,
                'expected' => false,
                'value' => 'a',
            ],
            'exclusive-string-invalid-ceiling' => [
                'min' => 'a',
                'max' => 'z',
                'inclusive' => false,
                'expected' => false,
                'value' => 'z',
            ],
            'inclusive-int-invalid-string' => [
                'min' => 0,
                'max' => 99999999,
                'inclusive' => true,
                'expected' => false,
                'value' => 'asdasd',
            ],
            'inclusive-int-invalid-char' => [
                'min' => 0,
                'max' => 99999999,
                'inclusive' => true,
                'expected' => false,
                'value' => 'q',
            ],
            'inclusive-string-invalid-zero' => [
                'min' => 'a',
                'max' => 'zzzzz',
                'inclusive' => true,
                'expected' => false,
                'value' => 0,
            ],
            'inclusive-string-invalid-non-zero' => [
                'min' => 'a',
                'max' => 'zzzzz',
                'inclusive' => true,
                'expected' => false,
                'value' => 10,
            ],
        ];
    }
    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider providerBasic
     * @param int|float|string $min
     * @param int|float|string $max
     * @param bool $inclusive
     * @param bool $expected
     * @param mixed $value
     * @return void
     */
    public function testBasic($min, $max, $inclusive, $expected, $value)
    {
        $validator = new Between(['min' => $min, 'max' => $max, 'inclusive' => $inclusive]);

        $this->assertSame(
            $expected,
            $validator->isValid($value),
            'Failed value: ' . $value . ':' . implode("\n", $validator->getMessages())
        );
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
