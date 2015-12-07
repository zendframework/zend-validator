<?php

namespace Zend\Validator\Isbn;

class Isbn10
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
        for ($i = 0; $i < 9; $i++) {
            $sum += (10 - $i) * $value{$i};
        }
        return $sum;
    }

    private function _checksum($sum)
    {
        $checksum = 11 - ($sum % 11);

        if ($checksum == 11) {
            $checksum = '0';
        } elseif ($checksum == 10) {
            $checksum = 'X';
        }

        return $checksum;
    }
}
