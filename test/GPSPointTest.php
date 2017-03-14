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
use Zend\Validator\GpsPoint;

/**
 * @group      Zend_Validator
 */
class GPSPointTest extends TestCase
{

    /**
     * @var GpsPoint
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new GpsPoint();
    }


    /**
     * @dataProvider basicDataProvider
     * @covers \Zend\Validator\GPSPoint::isValid
     */
    public function testBasic($gpsPoint)
    {
        $this->assertTrue($this->validator->isValid($gpsPoint));
    }

    /**
     * @covers \Zend\Validator\GPSPoint::isValid
     */
    public function testBoundariesAreRespected()
    {
        $this->assertFalse($this->validator->isValid('181.8897,-77.0089'));
        $this->assertFalse($this->validator->isValid('38.8897,-181.0089'));
        $this->assertFalse($this->validator->isValid('-181.8897,-77.0089'));
        $this->assertFalse($this->validator->isValid('38.8897,181.0089'));
    }

    /**
     * @covers \Zend\Validator\GPSPoint::isValid
     * @dataProvider ErrorMessageTestValues
     */
    public function testErrorsSetOnOccur($value, $messageKey, $messageValue)
    {
        $this->assertFalse($this->validator->isValid($value));
        $messages = $this->validator->getMessages();
        $this->assertArrayHasKey($messageKey, $messages);
        $this->assertContains($messageValue, $messages[$messageKey]);
    }

    public function basicDataProvider()
    {
        return [
            ['38° 53\' 23" N, 77° 00\' 32" W'],
            ['15° 22\' 20.137" S, 35° 35\' 14.686" E'],
            ['65° 4\' 36.434" N,-22.728867530822754'],
            ['38.8897°, -77.0089°'],
            ['38.8897,-77.0089']
        ];
    }
    // @codingStandardsIgnoreStart
    public function ErrorMessageTestValues()
    {
        // @codingStandardsIgnoreEnd
        return [
            ['63 47 24.691 N, 18 2 54.363 W', GpsPoint::OUT_OF_BOUNDS, '63 47 24.691 N'],
            ['° \' " N,° \' " E', GpsPoint::CONVERT_ERROR, '° \' " N'],
            ['° \' " N', GpsPoint::INCOMPLETE_COORDINATE, '° \' " N'],
        ];
    }
}
