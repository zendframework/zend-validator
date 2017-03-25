<?php

namespace Zend\Validator;

use Zend\Validator\Exception\RuntimeException;

class IsCountable extends AbstractValidator
{
    const NOT_COUNTABLE = 'notCountable';
    const NOT_EQUALS    = 'notEquals';
    const GREATER_THAN  = 'greaterThan';
    const LESS_THAN     = 'lessThan';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_COUNTABLE => "The input must be an array or an instance of \\Countable",
        self::NOT_EQUALS    => "The input count must equal '%count%'",
        self::GREATER_THAN  => "The input count must be less than '%max%', inclusively",
        self::LESS_THAN     => "The input count must be greater than '%min%', inclusively",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'count' => ['options' => 'count'],
        'min'   => ['options' => 'min'],
        'max'   => ['options' => 'max'],
    ];

    /**
     * Options for the between validator
     *
     * @var array
     */
    protected $options = [
        'count' => null,
        'min'   => null,
        'max'   => null,
    ];

    /**
     * Returns true if and only if $value is countable (and the count validates against optional values).
     *
     * @param  string $value
     * @return bool
     * @throws Exception\RuntimeException
     */
    public function isValid($value)
    {
        if (! (is_array($value) || $value instanceof \Countable)) {
            throw new RuntimeException($this->messageTemplates[self::NOT_COUNTABLE]);
        }

        $count = count($value);

        if (is_numeric($this->options['count'])) {
            if ($count != $this->options['count']) {
                $this->error(self::NOT_EQUALS);
                return false;
            }

            return true;
        }

        if (is_numeric($this->options['max']) && $count > $this->options['max']) {
            $this->error(self::GREATER_THAN);
            return false;
        }

        if (is_numeric($this->options['min']) && $count < $this->options['min']) {
            $this->error(self::LESS_THAN);
            return false;
        }

        return true;
    }

    /**
     * Returns the count option
     *
     * @return mixed
     */
    public function getCount()
    {
        return $this->options['count'];
    }

    /**
     * Sets the count option
     *
     * @param  int $count
     * @return self Provides a fluent interface
     */
    public function setCount($count)
    {
        $this->options['count'] = $count;
        return $this;
    }

    /**
     * Returns the min option
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->options['min'];
    }

    /**
     * Sets the min option
     *
     * @param  int $min
     * @return self Provides a fluent interface
     */
    public function setMin($min)
    {
        $this->options['min'] = $min;
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->options['max'];
    }

    /**
     * Sets the max option
     *
     * @param  int $max
     * @return self Provides a fluent interface
     */
    public function setMax($max)
    {
        $this->options['max'] = $max;
        return $this;
    }
}
