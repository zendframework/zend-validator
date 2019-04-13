<?php


namespace ZendTest\Validator;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Validator\UndisclosedPassword;
use ZendTest\Validator\TestAsset\HttpClientException;

class UndisclosedPasswordTest extends TestCase
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestInterface
     */
    private $httpRequest;

    /**
     * @var ResponseInterface
     */
    private $httpResponse;

    /**
     * @var UndisclosedPassword
     */
    private $validator;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->httpClient = $this->getMockBuilder(ClientInterface::class)
            ->getMockForAbstractClass();
        $this->httpRequest = $this->getMockBuilder(RequestFactoryInterface::class)
            ->getMockForAbstractClass();
        $this->httpResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMockForAbstractClass();
        $responseFactoryInterface = $this->getMockBuilder(ResponseFactoryInterface::class)
            ->getMockForAbstractClass();

        $this->validator = new UndisclosedPassword(
            $this->httpClient,
            $this->httpRequest,
            $responseFactoryInterface
        );
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        $this->httpClient = null;
    }

    /**
     * Data provider returning good, strong and unseen
     * passwords to be used in the validator.
     *
     * @return array
     */
    public function goodPasswordProvider()
    {
        return [
            ['ABi$B47es.Pfg3n9PjPi'],
            ['potence tipple would frisk shoofly'],
        ];
    }

    /**
     * Data provider for most common used passwords
     *
     * @return array
     * @see https://en.wikipedia.org/wiki/List_of_the_most_common_passwords
     */
    public function seenPasswordProvider()
    {
        return [
            ['123456'],
            ['password'],
            ['123456789'],
            ['12345678'],
            ['12345'],
        ];
    }

    /**
     * Testing that we reject invalid password types
     *
     * @covers \Zend\Validator\UndisclosedPassword
     * @covers \Zend\Validator\AbstractValidator
     * @todo Can be replaced by a \TypeError being thrown in PHP 7.0 or up
     */
    public function testValidationFailsForInvalidInput()
    {
        $this->assertFalse($this->validator->isValid(true));
        $this->assertFalse($this->validator->isValid((new \stdClass())));
        $this->assertFalse($this->validator->isValid(['foo']));
    }

    /**
     * Test that a given password was not found in the HIBP
     * API service.
     *
     * @param string $password
     *
     * @covers \Zend\Validator\UndisclosedPassword
     * @dataProvider goodPasswordProvider
     */
    public function testStrongUnseenPasswordsPassValidation($password)
    {
        $this->httpResponse->method('getBody')
            ->will($this->returnCallback(function () use ($password) {
                $hash = \sha1('zend-validator');
                return sprintf(
                    '%s:%d',
                    strtoupper(substr($hash, UndisclosedPassword::HIBP_K_ANONYMITY_HASH_RANGE_LENGTH)),
                    rand(0, 100000)
                );
            }));
        $this->httpClient->method('sendRequest')
            ->will($this->returnValue($this->httpResponse));

        $this->assertTrue($this->validator->isValid($password));
    }

    /**
     * Test that a given password was already seen in the HIBP
     * AP service.
     *
     * @param string $password
     * @dataProvider seenPasswordProvider
     * @covers \Zend\Validator\UndisclosedPassword
     * @covers \Zend\Validator\AbstractValidator
     */
    public function testBreachedPasswordsDoNotPassValidation($password)
    {
        $this->httpResponse->method('getBody')
            ->will($this->returnCallback(function () use ($password) {
                $hash = \sha1($password);
                return sprintf(
                    '%s:%d',
                    strtoupper(substr($hash, UndisclosedPassword::HIBP_K_ANONYMITY_HASH_RANGE_LENGTH)),
                    rand(0, 100000)
                );
            }));
        $this->httpClient->method('sendRequest')
            ->will($this->returnValue($this->httpResponse));

        $this->assertFalse($this->validator->isValid($password));
    }

    /**
     * Testing we are setting error messages when a password was found
     * in the breach database.
     *
     * @param string $password
     * @depends testBreachedPasswordsDoNotPassValidation
     * @dataProvider seenPasswordProvider
     * @covers \Zend\Validator\UndisclosedPassword
     */
    public function testBreachedPasswordReturnErrorMessages($password)
    {
        $this->httpClient->method('sendRequest')
            ->will($this->throwException(new \Exception('foo')));

        $this->expectException(\Exception::class);
        $this->validator->isValid($password);
        $this->fail('Excpected exception was not thrown');
    }

    /**
     * Testing that we capture any failures when trying to connect with
     * the HIBP web service.
     *
     * @param string $password
     * @depends testBreachedPasswordsDoNotPassValidation
     * @dataProvider seenPasswordProvider
     * @covers \Zend\Validator\UndisclosedPassword
     */
    public function testValidationDegradesGracefullyWhenNoConnectionCanBeMade($password)
    {
        $clientException = $this->getMockBuilder(HttpClientException::class)
            ->getMock();
        $this->httpClient->method('sendRequest')
            ->will($this->throwException($clientException));

        $this->expectException(ClientExceptionInterface::class);

        $this->validator->isValid($password);
        $this->fail('Expected ClientException was not thrown');
    }
}
