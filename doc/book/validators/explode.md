# Explode Validator

`Zend\Validator\Explode` executes a validator for each item exploded.

## Supported options

The following options are supported for `Zend\Validator\Explode`:

- `valueDelimiter`: Defines the delimiter used to explode the value to an array. It defaults to `,`. If working with an array, this option isn't used.
- `validator`: Sets the validator that will be executed on each exploded item.

## Basic usage

To validate if every item in an array is into a certain haystack:

```php
$inArrayValidator = new Zend\Validator\InArray([
    'haystack' => [1, 2, 3, 4, 5, 6]
]);

$explodeValidator = new Zend\Validator\Explode([
    'validator' => $inArrayValidator
]);

$value  = [1, 4, 6, 8];
$return = $valid->isValid($value);
// returns false
```

The above example returns `true` if all $value items are between 1 and 6.
