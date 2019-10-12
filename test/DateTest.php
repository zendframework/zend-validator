<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\Validator;

/**
 * @group      Zend_Validator
 */
class DateTest extends TestCase
{
    /**
     * @var Validator\Date
     */
    protected $validator;

    /**
     * Creates a new Zend\Validator\Date object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Validator\Date();
    }

    public function testSetFormatIgnoresNull()
    {
        $this->validator->setFormat(null);
        $this->assertEquals(Validator\Date::FORMAT_DEFAULT, $this->validator->getFormat());
    }

    public function datesDataProvider()
    {
        return [
            // date                     format             isValid   isValid Strict
            ['2007-01-01',              null,              true,     true],
            ['2007-02-28',              null,              true,     true],
            ['2007-02-29',              null,              false,    false],
            ['2008-02-29',              null,              true,     true],
            ['2007-02-30',              null,              false,    false],
            ['2007-02-99',              null,              false,    false],
            ['2007-02-99',              'Y-m-d',           false,    false],
            ['9999-99-99',              null,              false,    false],
            ['9999-99-99',              'Y-m-d',           false,    false],
            ['Jan 1 2007',              null,              false,    false],
            ['Jan 1 2007',              'M j Y',           true,     true],
            ['asdasda',                 null,              false,    false],
            ['sdgsdg',                  null,              false,    false],
            ['2007-01-01something',     null,              false,    false],
            ['something2007-01-01',     null,              false,    false],
            ['10.01.2008',              'd.m.Y',           true,     true],
            ['01 2010',                 'm Y',             true,     true],
            ['2008/10/22',              'd/m/Y',           false,    false],
            ['22/10/08',                'd/m/y',           true,     true],
            ['22/10',                   'd/m/Y',           false,    false],
            // time
            ['2007-01-01T12:02:55Z',    DateTime::ISO8601, true,     false],
            ['2007-01-01T12:02:55+0000', DateTime::ISO8601, true,    true],
            ['12:02:55',                'H:i:s',           true,     true],
            ['25:02:55',                'H:i:s',           false,    false],
            // int
            [0,                         null,              true,     false],
            [6,                         'd',               true,     false],
            ['6',                       'd',               true,     false],
            ['06',                      'd',               true,     true],
            [123,                       null,              true,     false],
            [1340677235,                null,              true,     false],
            // 32bit version of php will convert this to double
            [999999999999,              null,              true,     false],
            // double
            [12.12,                     null,              false,    false],
            // array
            [['2012', '06', '25'],      null,              true,     false],
            // 0012-06-25 is a valid date, if you want 2012, use 'y' instead of 'Y'
            [['12', '06', '25'],        null,              true,     false],
            [['2012', '06', '33'],      null,              false,    false],
            [[1 => 1],                  null,              false,    false],
            // DateTime
            [new DateTime(),            null,              true,     false],
            // invalid obj
            [new stdClass(),            null,              false,    false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider datesDataProvider
     */
    public function testBasic($input, $format, $result)
    {
        $this->validator->setFormat($format);
        $this->assertEquals($result, $this->validator->isValid($input));
    }

    /**
     * @dataProvider datesDataProvider
     *
     * @param mixed $input
     * @param string|null $format
     * @param bool $result
     * @param bool $resultStrict
     */
    public function testBasicStrictMode($input, $format, $result, $resultStrict)
    {
        $this->validator->setStrict(true);
        $this->validator->setFormat($format);
        $this->assertSame($resultStrict, $this->validator->isValid($input));
    }

    public function testDateTimeImmutable()
    {
        $this->assertTrue($this->validator->isValid(new DateTimeImmutable()));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensures that the validator can handle different manual dateformats
     *
     * @group  ZF-2003
     * @return void
     */
    public function testUseManualFormat()
    {
        $this->assertTrue(
            $this->validator->setFormat('d.m.Y')->isValid('10.01.2008'),
            var_export(date_get_last_errors(), 1)
        );
        $this->assertEquals('d.m.Y', $this->validator->getFormat());

        $this->assertTrue($this->validator->setFormat('m Y')->isValid('01 2010'));
        $this->assertFalse($this->validator->setFormat('d/m/Y')->isValid('2008/10/22'));
        $this->assertTrue($this->validator->setFormat('d/m/Y')->isValid('22/10/08'));
        $this->assertFalse($this->validator->setFormat('d/m/Y')->isValid('22/10'));
        // Omitting the following assertion, as it varies from 5.3.3 to 5.3.11,
        // and there is no indication in the PHP changelog as to when or why it
        // may have changed. Leaving for posterity, to indicate original expectation.
        // $this->assertFalse($this->validator->setFormat('s')->isValid(0));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals(
            $validator->getOption('messageTemplates'),
            'messageTemplates',
            $validator
        );
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals(
            $validator->getOption('messageVariables'),
            'messageVariables',
            $validator
        );
    }

    public function testConstructorWithFormatParameter()
    {
        $format = 'd/m/Y';
        $validator = new Validator\Date($format);

        $this->assertEquals($format, $validator->getFormat());
    }
}
