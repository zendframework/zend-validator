<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Validator\File\TestAsset;

use Zend\Validator\File\FileInformationTrait;

/**
* Validator which checks if the file already exists in the directory
*/
class FileInformation
{
    use FileInformationTrait;

    /**
     * Returns array if the procedure is identified
     *
     * @param  string|array|object $value    Filename to check
     * @param  null|array          $file     File data (when using legacy Zend_File_Transfer API)
     * @param  bool                $hasType  Return with filetype (optional)
     * @param  bool                $basename Return with basename - is calculated from location path (optional)
     * @return array
     */
    public function checkFileInformation(
        $value,
        array $file = null,
        $hasType = false,
        $hasBasename = false
    ) {
        return $this->getFileInfo($value, $file, $hasType, $hasBasename);
    }
}
