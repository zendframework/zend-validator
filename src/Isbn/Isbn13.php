<?php

namespace Zend\Validator\Isbn;

class Isbn13
{
    /**
     * @param $value
     * @return int|string
     */
    public function getChecksum($value)
    {
        $sum = $this->_sum($value);
        return $this->_checksum($sum);
    }

    private function _sum($value)
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            if ($i % 2 == 0) {
                $sum += $value{$i};
            } else {
                $sum += 3 * $value{$i};
            }
        }
        return $sum;
    }

    private function _checksum($sum)
    {
        $checksum = 10 - ($sum % 10);

        if ($checksum == 10) {
            $checksum = '0';
        }

        return $checksum;
    }
}
