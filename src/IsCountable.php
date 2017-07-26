<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Validator;

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

    public function setOptions($options = [])
    {
        if (is_array($options) && isset($options['count'])) {
            unset($options['min'], $options['max']);
        }

        return parent::setOptions($options);
    }

    /**
     * Returns true if and only if $value is countable (and the count validates against optional values).
     *
     * @param  iterable $value
     * @return bool
     */
    public function isValid($value)
    {
        if (! (is_array($value) || $value instanceof \Countable)) {
            $this->error(self::NOT_COUNTABLE);
            return false;
        }

        $count = count($value);

        if (is_numeric($this->getCount())) {
            if ($count != $this->getCount()) {
                $this->error(self::NOT_EQUALS);
                return false;
            }

            return true;
        }

        if (is_numeric($this->getMax()) && $count > $this->getMax()) {
            $this->error(self::GREATER_THAN);
            return false;
        }

        if (is_numeric($this->getMin()) && $count < $this->getMin()) {
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
     * Returns the min option
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->options['min'];
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
}
