<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\Bitwise;

class BitwiseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Validator\Bitwise
     */
    public $validator;

    public function setUp()
    {
        $this->validator = new Bitwise();
    }

    /**
     * @covers \Zend\Validator\Bitwise::__construct()
     * @dataProvider constructDataProvider
     *
     * @param array $args
     * @param array $options
     */
    public function testConstruct(array $args, array $options)
    {
        $validator = new Bitwise($args);

        $this->assertSame($options['control'], $validator->getControl());
        $this->assertSame($options['operator'], $validator->getOperator());
        $this->assertSame($options['strict'], $validator->getStrict());
    }

    public function constructDataProvider()
    {
        return [
            [
                [],
                ['control' => null, 'operator' => null, 'strict' => false],
            ],
            [
                ['control' => 0x1],
                ['control' => 0x1, 'operator' => null, 'strict' => false],
            ],
            [
                ['control' => 0x1, 'operator' => Bitwise::OP_AND],
                ['control' => 0x1, 'operator' => Bitwise::OP_AND, 'strict' => false],
            ],
            [
                ['control' => 0x1, 'operator' => Bitwise::OP_AND, 'strict' => true],
                ['control' => 0x1, 'operator' => Bitwise::OP_AND, 'strict' => true],
            ],
        ];
    }

    /**
     * @covers \Zend\Validator\Bitwise::isvalid()
     */
    public function testBitwiseAndNotStrict()
    {
        $controlSum = 0x7; // (0x1 | 0x2 | 0x4) === 0x7

        $validator = new Bitwise();
        $validator->setControl($controlSum);
        $validator->setOperator(Bitwise::OP_AND);

        $this->assertTrue($validator->isValid(0x1));
        $this->assertTrue($validator->isValid(0x2));
        $this->assertTrue($validator->isValid(0x4));
        $this->assertFalse($validator->isValid(0x8));

        $validator->isValid(0x8);
        $messages = $validator->getMessages();
        $this->assertArrayHasKey($validator::NOT_AND, $messages);
        $this->assertSame("The input has no common bit set with '$controlSum'", $messages[$validator::NOT_AND]);

        $this->assertTrue($validator->isValid(0x1 | 0x2));
        $this->assertTrue($validator->isValid(0x1 | 0x2 | 0x4));
        $this->assertTrue($validator->isValid(0x1 | 0x8));
    }

    /**
     * @covers \Zend\Validator\Bitwise::isvalid()
     */
    public function testBitwiseAndStrict()
    {
        $controlSum = 0x7; // (0x1 | 0x2 | 0x4) === 0x7

        $validator = new Bitwise();
        $validator->setControl($controlSum);
        $validator->setOperator(Bitwise::OP_AND);
        $validator->setStrict(true);

        $this->assertTrue($validator->isValid(0x1));
        $this->assertTrue($validator->isValid(0x2));
        $this->assertTrue($validator->isValid(0x4));
        $this->assertFalse($validator->isValid(0x8));

        $validator->isValid(0x8);
        $messages = $validator->getMessages();
        $this->assertArrayHasKey($validator::NOT_AND_STRICT, $messages);
        $this->assertSame("The input doesn't have the same bits set as '$controlSum'", $messages[$validator::NOT_AND_STRICT]);

        $this->assertTrue($validator->isValid(0x1 | 0x2));
        $this->assertTrue($validator->isValid(0x1 | 0x2 | 0x4));
        $this->assertFalse($validator->isValid(0x1 | 0x8));
    }

    /**
     * @covers \Zend\Validator\Bitwise::isvalid()
     */
    public function testBitwiseXor()
    {
        $controlSum = 0x5; // (0x1 | 0x4) === 0x5

        $validator = new Bitwise();
        $validator->setControl($controlSum);
        $validator->setOperator(Bitwise::OP_XOR);

        $this->assertTrue($validator->isValid(0x2));
        $this->assertTrue($validator->isValid(0x8));
        $this->assertTrue($validator->isValid(0x10));
        $this->assertFalse($validator->isValid(0x1));
        $this->assertFalse($validator->isValid(0x4));

        $validator->isValid(0x4);
        $messages = $validator->getMessages();
        $this->assertArrayHasKey($validator::NOT_XOR, $messages);
        $this->assertSame("The input has common bit set with '$controlSum'", $messages[$validator::NOT_XOR]);

        $this->assertTrue($validator->isValid(0x8 | 0x10));
        $this->assertFalse($validator->isValid(0x1 | 0x4));
        $this->assertFalse($validator->isValid(0x1 | 0x8));
        $this->assertFalse($validator->isValid(0x4 | 0x8));
    }

    /**
     * @covers \Zend\Validator\Bitwise::setOperator()
     */
    public function testSetOperator()
    {
        $validator = new Bitwise();

        $validator->setOperator(Bitwise::OP_AND);
        $this->assertSame(Bitwise::OP_AND, $validator->getOperator());

        $validator->setOperator(Bitwise::OP_XOR);
        $this->assertSame(Bitwise::OP_XOR, $validator->getOperator());
    }

    /**
     * @covers \Zend\Validator\Bitwise::setStrict()
     */
    public function testSetStrict()
    {
        $validator = new Bitwise();

        $this->assertFalse($validator->getStrict(), 'Strict false by default');

        $validator->setStrict(false);
        $this->assertFalse($validator->getStrict());

        $validator->setStrict(true);
        $this->assertTrue($validator->getStrict());

        $validator = new Bitwise(0x1, Bitwise::OP_AND, false);
        $this->assertFalse($validator->getStrict());

        $validator = new Bitwise(0x1, Bitwise::OP_AND, true);
        $this->assertTrue($validator->getStrict());
    }
    
    
    public function testConstructWithArguments()
    {
    
    	$control = 0x1;
    	$operator = Bitwise::OP_AND;
    	$strict = true;
    
    	$validator = new Bitwise($control, $operator, $strict);
    	
    	$this->assertEquals($control, $validator->getControl());
    	$this->assertEquals($operator, $validator->getOperator());
    	$this->assertEquals($strict, $validator->getStrict());
    }
    
    public function testGetControl()
    {
    	$control = 0x1;
    	$validator = new Bitwise($control, Bitwise::OP_AND, false);
    	
    	$this->assertEquals($control, $validator->getControl());
    }
    
    public function testGetOperator()
    {
    	$operator = Bitwise::OP_AND;
    	
    	$validator = new Bitwise(0x1, $operator, false);
    	
    	$this->assertEquals($operator, $validator->getOperator());
    }
    
    public function testGetStrict()
    {
    	$strict = true;
    	
    	$validator = new Bitwise(0x1, Bitwise::OP_AND, $strict);
    	
    	$this->assertEquals($strict, $validator->getStrict());
    }
    
    public function testIsValidWithInvalidOperator()
    {
    	$validator = new Bitwise(0x1, 'or', false);
    	
    	$expectedResult = false;
    	
    	$this->assertEquals($expectedResult, $validator->isValid(0x2));
    }
    
    public function testSetControl()
    {
    	$validator = new Bitwise();
    	
    	$control = 0x2;
    	
    	$validator->setControl($control);
    	
    	$this->assertEquals($control, $validator->getControl());
    }
    
}
