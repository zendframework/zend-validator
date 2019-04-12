<?php


namespace ZendTest\Validator;


use PHPUnit\Framework\MockObject\Generator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Validator\UndisclosedPassword;

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
     * @var ClientExceptionInterface
     */
    private $httpClientException;

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
        $this->httpRequest = $this->getMockBuilder(RequestInterface::class)
            ->getMockForAbstractClass();
        $this->httpResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMockForAbstractClass();

        $this->validator = new UndisclosedPassword(
            $this->httpClient,
            $this->httpRequest,
            $this->httpResponse
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
     * Test that a given password was not found in the HIBP
     * API service.
     *
     * @param string $password
     *
     * @covers \Zend\Validator\UndisclosedPassword::isValid
     * @dataProvider goodPasswordProvider
     */
    public function testStrongUnseenPasswordsPassValidation($password)
    {
        $this->httpResponse->method('getBody')
            ->will($this->returnCallback(function () use ($password) {
                $hash = \sha1('zend-validator');
                return sprintf(
                    '%s:%d',
                    strtoupper(substr($hash, UndisclosedPassword::HIBP_RANGE_LENGTH)),
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
     * @covers \Zend\Validator\UndisclosedPassword::isValid
     * @dataProvider seenPasswordProvider
     */
    public function testBreachedPasswordsDoNotPassValidation($password)
    {
        $this->httpResponse->method('getBody')
            ->will($this->returnCallback(function () use ($password) {
                $hash = \sha1($password);
                return sprintf(
                    '%s:%d',
                    strtoupper(substr($hash, UndisclosedPassword::HIBP_RANGE_LENGTH)),
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
     */
    public function testBreachedPasswordReturnErrorMessages($password)
    {
        $this->httpClient->method('sendRequest')
            ->will($this->throwException(new \Exception()));

        $this->validator->isValid($password);
        $this->assertCount(1, $this->validator->getMessages());
    }
}
