<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;


class GPSPoint extends AbstractValidator
{

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {

        list($lat, $long) = explode(',', $value);

        if ($this->isValidLatitude($lat) && $this->isValidLongitude($long)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $lat
     * @return bool
     */
    public function isValidLatitude($lat)
    {

        if ($this->isDMSValue($lat)) {
            $lat = $this->convertValue($lat);
        }

        if ($lat === false) {
            return false;
        }

        $doubleLatitude = (double)$lat;

        if ($doubleLatitude <= 90.0000 && $doubleLatitude >= -90.0000) {
            return true;
        }

        return false;
    }

    /**
     * @param string $long
     * @return bool
     */
    public function isValidLongitude($long)
    {

        if ($this->isDMSValue($long)) {
            $long = $this->convertValue($long);
        }

        if ($long === false) {
            return false;
        }

        $doubleLongitude = (double)$long;

        if ($doubleLongitude <= 180.0000 && $doubleLongitude >= -180.0000) {
            return true;
        }

        return false;
    }

    /**
     * @param string $value
     * @return bool|string
     */
    private function convertValue($value)
    {
        $matches = [];
        $result = preg_match_all('/(-?\d{1,3})º(\d{1,2})\'(\d{1,2})"[NESW]+/', $value, $matches);

        if ($result === false || $result === 0) {
            return false;
        }

        return $matches[0] + $matches[1]/60 + $matches[2]/3600;
    }


    /**
     * Determines if the give value is a Degrees Minutes Second Definition
     *
     * @param $value
     * @return bool
     */
    private function isDMSValue($value)
    {
        return preg_match('/([º\'"NESW]*)/', $value) > 0;
    }

}