<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\GPSPoint;


/**
 * @group      Zend_Validator
 */
class GPSPointTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GPSPoint
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new GPSPoint();
    }


    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic($gpsPoint)
    {
        $this->assertTrue($this->validator->isValid($gpsPoint));
    }

    public function testBoundariesAreRespected()
    {
        $this->assertFalse($this->validator->isValid('181.8897,-77.0089'));
        $this->assertFalse($this->validator->isValid('38.8897,-181.0089'));
        $this->assertFalse($this->validator->isValid('-181.8897,-77.0089'));
        $this->assertFalse($this->validator->isValid('38.8897,181.0089'));
    }

    public function basicDataProvider()
    {
        return [
            ['38° 53\' 23" N, 77° 00\' 32" W'],
            ['38.8897°, -77.0089°'],
            ['38.8897,-77.0089']
        ];
    }
} 