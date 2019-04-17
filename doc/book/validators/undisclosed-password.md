# Undisclosed Password Validator

`Zend\Validator\UndisclosedPassword` allows you to validate if a given password was found in data breaches using the service [Have I Been Pwned?](https://www.haveibeenpwned.com), in a secure, anonymous way using [K-Anonymity](https://www.troyhunt.com/ive-just-launched-pwned-passwords-version-2) to ensure passwords are not send in full over the wire.

> ### Installation requirements
> 
> This validator needs to make a request over HTTP, therefor it requires an HTTP client of your choice that implements [PSR-18](https://www.php-fig.org/psr/psr-18/) and [PSR-17](https://www.php-fig.org/psr/psr-17/) request and response factories.
>
> Make sure you have it installed before using this validator:
>
> ```bash
> $ composer require psr/http-client
> $ composer require psr/http-factory
> ```

## Basic usage

To validate if a password was disclosed in a known data breach, you need to provide a HTTP Client that implements `Psr\Http\Client\ClientInterface`, a `Psr\Http\Message\RequestFactoryInterface` and a `Psr\Http\Message\ResponseFactoryInterface` to the constructor and validate the password you want to check.

If the password was found via the service, `isValid` will return `false`. If the password was not found, `isValid` will return `true`.

```php
$validator = new Zend\Validator\UndisclosedPassword(
    $httpClient, // a PSR-18 HttpClientInterface
    $requestFactory, // a PSR-17 RequestFactoryInterface
    $responseFactory // a PSR-17 ResponseFactoryInterface
);
```
```php
$result = $validator->isValid('password'); 
// $result is FALSE because "password" was found in a data breach
```

```php
$result = $validator->isValid('8aDk=XiW2E.77tLfuAcB'); 
// $result is TRUE because "8aDk=XiW2E.77tLfuAcB" was not found in a data breach
```

## A simple command line example

In this example I'm using `zendframework/zend-diactoros` for HTTP messaging and `php-http/curl-client` as the HTTP client. Let's begin with installation of all required packages:

```bash
$ composer require \
    php-http/message \
    php-http/message-factory \
    php-http/discovery \
    php-http/curl-client \
    zendframework/zend-diactoros \
    zendframework/zend-validator 
```

Next thing is I create a file `undisclosed.php` where I will put in my code.

```php
<?php

namespace Undisclosed;

use Http\Client\Curl\Client;
use Zend\Diactoros\RequestFactory;
use Zend\Diactoros\ResponseFactory;
use Zend\Validator\UndisclosedPassword;

require_once __DIR__ . '/vendor/autoload.php';


$requestFactory = new RequestFactory();
$responseFactory = new ResponseFactory();
$client = new Client($responseFactory, null);

$undisclosedPassword = new UndisclosedPassword($client, $requestFactory, $responseFactory);
echo 'Password "password" is ' . ($undisclosedPassword->isValid('password') ? 'not disclosed' : 'disclosed') . PHP_EOL;
echo 'Password "NVt3MpvQ" is ' . ($undisclosedPassword->isValid('NVt3MpvQ') ? 'not disclosed' : 'disclosed') . PHP_EOL;
```

To run it, I use PHP on the command line:

```bash
$ php undisclosed.php
```

And it will give me the following output:

```bash
Password "password" is disclosed
Password "NVt3MpvQ" is not disclosed
```
