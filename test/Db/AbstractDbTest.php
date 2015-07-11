<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator\Db;

use ZendTest\Validator\Db\TestAsset\ConcreteDbValidator;
use Zend\Db\Sql\Select;

/**
 * @group      Zend_Validator
 */
class AbstractDbTest extends \PHPUnit_Framework_TestCase
{

    protected $validator;

    public function setUp()
    {
        $this->validator = new ConcreteDbValidator([
            'table' => 'table',
            'field' => 'field',
		    'schema' => 'schema',
        ]);
    }

    public function testConstructorWithNoTableAndSchemaKey()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException',
        'Table or Schema option missing!');
        $this->validator = new ConcreteDbValidator([
            'field' => 'field',
        ]);
    }

    public function testConstructorWithNoFieldKey()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException',
        'Field option missing!');
        $validator = new ConcreteDbValidator([
            'schema' => 'schema',
			'table' => 'table',
        ]);
    }

    public function testSetSelect()
    {
        $select = new Select();
        $this->validator->setSelect($select);

        $this->assertSame($select, $this->validator->getSelect());
    }

    public function testGetSchema()
    {
        $schema = 'test_db';
        $this->validator->setSchema($schema);

        $this->assertEquals($schema, $this->validator->getSchema());
    }

    public function testGetTable()
    {
        $table = 'test_table';
        $this->validator->setTable($table);

        $this->assertEquals($table, $this->validator->getTable());
    }

    public function testGetField()
    {
        $field = 'test_field';
        $this->validator->setField($field);

        $this->assertEquals($field, $this->validator->getField());
    }

    public function testGetExclude()
    {
        $field = 'test_field';
        $this->validator->setField($field);

        $this->assertEquals($field, $this->validator->getField());
    }
}
