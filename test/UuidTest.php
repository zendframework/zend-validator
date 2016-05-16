<?php

/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\Uuid;

/**
 * Class UuidTest.
 *
 * Uuid test cases based on https://github.com/beberlei/assert/blob/master/tests/Assert/Tests/AssertTest.php
 */
final class UuidTest extends \PHPUnit_Framework_TestCase
{
    /** @var Uuid */
    protected $validator;

    /**
     * setUp.
     */
    public function setUp()
    {
        $this->validator = new Uuid();
    }

    /**
     * testValidUuids.
     *
     * @param $uuid
     * @dataProvider validUuidProvider
     */
    public function testValidUuid($uuid)
    {
        $this->assertTrue($this->validator->isValid($uuid));
        $messages = $this->validator->getMessages();
        $this->assertCount(0, $messages);
    }

    /**
     * testValidUuids.
     *
     * @param $uuid
     * @dataProvider invalidUuidProvider
     */
    public function testInvalidUuid($uuid, $expectedMessageKey)
    {
        $this->assertFalse($this->validator->isValid($uuid));
        $messages = $this->validator->getMessages();
        $this->assertCount(1, $messages);
        $this->assertArrayHasKey($expectedMessageKey, $messages);
        $this->assertNotEmpty($messages[$expectedMessageKey]);
    }

    /**
     * providesValidUuids.
     *
     * @return array
     */
    public function validUuidProvider()
    {
        return [
            ['00000000-0000-0000-0000-000000000000'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-21e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-31e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-41e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-51e1-9b21-0800200c9a66'],
            ['FF6F8CB0-C57D-11E1-9B21-0800200C9A66'],
        ];
    }

    /**
     * invalidUuidProvider.
     *
     * @return array
     */
    public function invalidUuidProvider()
    {
        return [
            ['zf6f8cb0-c57d-11e1-9b21-0800200c9a66', Uuid::INVALID],
            ['af6f8cb0c57d11e19b210800200c9a66', Uuid::INVALID],
            ['ff6f8cb0-c57da-51e1-9b21-0800200c9a66', Uuid::INVALID],
            ['af6f8cb-c57d-11e1-9b21-0800200c9a66', Uuid::INVALID],
            ['3f6f8cb0-c57d-11e1-9b21-0800200c9a6', Uuid::INVALID],
            ['3f6f8cb0', Uuid::INVALID],
            ['', Uuid::INVALID],
            [123, Uuid::NOT_STRING],
            [new \stdClass(), Uuid::NOT_STRING],
        ];
    }
}
