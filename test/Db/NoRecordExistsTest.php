<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator\Db;

use PHPUnit\Framework\TestCase;
use Zend\Validator\Db\NoRecordExists;
use Zend\Db\Adapter\ParameterContainer;
use ArrayObject;
use Zend\Validator\Exception\RuntimeException;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Adapter;

/**
 * @group      Zend_Validator
 */
class NoRecordExistsTest extends TestCase
{
    /**
     * Return a Mock object for a Db result with rows
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    protected function getMockHasResult()
    {
        // mock the adapter, driver, and parts
        $mockConnection = $this->createMock(ConnectionInterface::class);

        // Mock has result
        $mockHasResultRow      = new ArrayObject();
        $mockHasResultRow->one = 'one';

        $mockHasResult = $this->createMock(ResultInterface::class);
        $mockHasResult->expects($this->any())
            ->method('current')
            ->will($this->returnValue($mockHasResultRow));

        $mockHasResultStatement = $this->createMock(StatementInterface::class);
        $mockHasResultStatement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($mockHasResult));

        $mockHasResultStatement->expects($this->any())
            ->method('getParameterContainer')
            ->will($this->returnValue(new ParameterContainer()));

        $mockHasResultDriver = $this->createMock(DriverInterface::class);
        $mockHasResultDriver->expects($this->any())
            ->method('createStatement')
            ->will($this->returnValue($mockHasResultStatement));
        $mockHasResultDriver->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        return $this->getMockBuilder(Adapter::class)
            ->setMethods(null)
            ->setConstructorArgs([$mockHasResultDriver])
            ->getMock();
    }

    /**
     * Return a Mock object for a Db result without rows
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    protected function getMockNoResult()
    {
        // mock the adapter, driver, and parts
        $mockConnection = $this->createMock(ConnectionInterface::class);

        $mockNoResult = $this->createMock(ResultInterface::class);
        $mockNoResult->expects($this->any())
            ->method('current')
            ->will($this->returnValue(null));

        $mockNoResultStatement = $this->createMock(StatementInterface::class);
        $mockNoResultStatement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($mockNoResult));

        $mockNoResultStatement->expects($this->any())
            ->method('getParameterContainer')
            ->will($this->returnValue(new ParameterContainer()));

        $mockNoResultDriver = $this->createMock(DriverInterface::class);
        $mockNoResultDriver->expects($this->any())
            ->method('createStatement')
            ->will($this->returnValue($mockNoResultStatement));
        $mockNoResultDriver->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        return $this->getMockBuilder(Adapter::class)
            ->setMethods(null)
            ->setConstructorArgs([$mockNoResultDriver])
            ->getMock();
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsRecord()
    {
        $validator = new NoRecordExists('users', 'field1', null, $this->getMockHasResult());
        $this->assertFalse($validator->isValid('value1'));
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsNoRecord()
    {
        $validator = new NoRecordExists('users', 'field1', null, $this->getMockNoResult());
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     *
     * @return void
     */
    public function testExcludeWithArray()
    {
        $validator = new NoRecordExists(
            'users',
            'field1',
            ['field' => 'id', 'value' => 1],
            $this->getMockHasResult()
        );
        $this->assertFalse($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with an array
     *
     * @return void
     */
    public function testExcludeWithArrayNoRecord()
    {
        $validator = new NoRecordExists(
            'users',
            'field1',
            ['field' => 'id', 'value' => 1],
            $this->getMockNoResult()
        );
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithString()
    {
        $validator = new NoRecordExists('users', 'field1', 'id != 1', $this->getMockHasResult());
        $this->assertFalse($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithStringNoRecord()
    {
        $validator = new NoRecordExists('users', 'field1', 'id != 1', $this->getMockNoResult());
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test that the class throws an exception if no adapter is provided
     * and no default is set.
     *
     * @return void
     */
    public function testThrowsExceptionWithNoAdapter()
    {
        $validator = new NoRecordExists('users', 'field1', 'id != 1');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No database adapter present');
        $validator->isValid('nosuchvalue');
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchema()
    {
        $validator = new NoRecordExists([
            'table' => 'users',
            'schema' => 'my'
        ], 'field1', null, $this->getMockHasResult());
        $this->assertFalse($validator->isValid('value1'));
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchemaNoResult()
    {
        $validator = new NoRecordExists([
            'table' => 'users',
            'schema' => 'my'
        ], 'field1', null, $this->getMockNoResult());
        $this->assertTrue($validator->isValid('value1'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator  = new NoRecordExists('users', 'field1');
        $this->assertAttributeEquals(
            $validator->getOption('messageTemplates'),
            'messageTemplates',
            $validator
        );
    }
}
