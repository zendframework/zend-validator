<?php


namespace Zend\Validator;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class UndisclosedPassword extends AbstractValidator
{
    const HIBP_API_URI = 'https://api.pwnedpasswords.com';
    const HIBP_API_TIMEOUT = 300;
    const HIBP_CLIENT_UA = 'zend-validator';
    const HIBP_CLIENT_ACCEPT = 'application/vnd.haveibeenpwned.v2+json';
    const HIBP_RANGE_LENGTH = 5;
    const HIBP_RANGE_BASE = 0;
    const HIBP_COUNT_BASE = 0;
    const SHA1_LENGTH = 40;

    const PASSWORD_BREACHED = 'passwordBreached';
    const WRONG_INPUT = 'wrongInput';
    const CONNECTION_FAILURE = 'connFail';
    const UNKNOWN_ERROR = 'unknownError';

    protected $messageTemplates = [
        self::PASSWORD_BREACHED => 'The provided password was used by others',
        self::WRONG_INPUT => 'The provided password failed to be correctly hashed, please verify your input',
        self::CONNECTION_FAILURE => 'Unable to reach HIBP service, please try again later',
        self::UNKNOWN_ERROR => 'Something happened beyond our control, error reporting should give more details',
    ];

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
     * @var int
     */
    private $count = 0;

    /**
     * PasswordBreach constructor.
     *
     * @param ClientInterface $httpClient
     * @param RequestInterface $httpRequest
     * @param ResponseInterface $httpResponse
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestInterface $httpRequest,
        ResponseInterface $httpResponse
    ) {
        $this->httpClient = $httpClient;
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
    }

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        if (! is_string($value)) {
            $this->error(self::WRONG_INPUT);
            return false;
        }
        try {
            $isPwnd = $this->isPwnedPassword($value);
        } catch (Exception\InvalidArgumentException $invalidArgumentException) {
            $this->error(self::WRONG_INPUT);
            return false;
        } catch (Exception\RuntimeException $runtimeException) {
            $this->error(self::CONNECTION_FAILURE);
            return false;
        } catch (\Exception $exception) {
            $this->error(self::UNKNOWN_ERROR);
            return false;
        }
        if ($isPwnd) {
            $this->error(self::PASSWORD_BREACHED);
            return false;
        }
        return true;
    }

    private function isPwnedPassword($password)
    {
        $sha1Hash = $this->hashPassword($password);
        $rangeHash = $this->getRangeHash($sha1Hash);
        $hashList = $this->retrieveHashList($rangeHash);
        return $this->hashInResponse($sha1Hash, $hashList);
    }

    /**
     * We use a SHA1 hashed password for checking it against
     * the breached data set of HIBP.
     *
     * @param string $password
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    private function hashPassword($password)
    {
        $hashedPassword = \sha1($password);
        if (self::SHA1_LENGTH !== strlen($hashedPassword)) {
            throw new Exception\InvalidArgumentException($this->messageTemplates[self::WRONG_INPUT]);
        }
        return strtoupper($hashedPassword);
    }

    /**
     * Creates a hash range that will be send to HIBP API
     * applying K-Anonymity
     *
     * @param string $passwordHash
     * @return string
     * @see https://www.troyhunt.com/enhancing-pwned-passwords-privacy-by-exclusively-supporting-anonymity/
     */
    private function getRangeHash($passwordHash)
    {
        $range = substr($passwordHash, self::HIBP_RANGE_BASE, self::HIBP_RANGE_LENGTH);
        return $range;
    }

    /**
     * Making a connection to the HIBP API to retrieve a
     * list of hashes that all have the same range as we
     * provided.
     *
     * @param string $passwordRange
     * @return string
     * @throws Exception\RuntimeException
     */
    private function retrieveHashList($passwordRange)
    {
        $requestClass = get_class($this->httpRequest);
        $request = new $requestClass('GET', '/range/' . $passwordRange);

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $connectException) {
            throw new Exception\RuntimeException($this->messageTemplates[self::CONNECTION_FAILURE]);
        }
        return (string) $response->getBody();
    }

    /**
     * Checks if the password is in the response from HIBP
     *
     * @param string $sha1Hash
     * @param string $resultStream
     * @return bool
     */
    private function hashInResponse($sha1Hash, $resultStream)
    {
        $data = explode("\r\n", $resultStream);
        $totalCount = self::HIBP_COUNT_BASE;
        $hashes = array_filter($data, function ($value) use ($sha1Hash, &$totalCount) {
            list($hash, $count) = explode(':', $value);
            if (0 === strcmp($hash, substr($sha1Hash, self::HIBP_RANGE_LENGTH))) {
                $totalCount = (int) $count;
                return true;
            }
            return false;
        });
        if ([] === $hashes) {
            return false;
        }
        $this->count = $totalCount;
        return true;
    }
}
