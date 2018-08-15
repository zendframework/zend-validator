<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator\File;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\UploadedFile;
use Zend\Validator\Exception\InvalidArgumentException;
use Zend\Validator\File;

/**
 * @group      Zend_Validator
 */
class UploadTest extends TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $_FILES = [
            'test'  => [
                'name'     => 'test1',
                'type'     => 'text',
                'size'     => 200,
                'tmp_name' => 'tmp_test1',
                'error'    => 0],
            'test2' => [
                'name'     => 'test2',
                'type'     => 'text2',
                'size'     => 202,
                'tmp_name' => 'tmp_test2',
                'error'    => 1],
            'test3' => [
                'name'     => 'test3',
                'type'     => 'text3',
                'size'     => 203,
                'tmp_name' => 'tmp_test3',
                'error'    => 2],
            'test4' => [
                'name'     => 'test4',
                'type'     => 'text4',
                'size'     => 204,
                'tmp_name' => 'tmp_test4',
                'error'    => 3],
            'test5' => [
                'name'     => 'test5',
                'type'     => 'text5',
                'size'     => 205,
                'tmp_name' => 'tmp_test5',
                'error'    => 4],
            'test6' => [
                'name'     => 'test6',
                'type'     => 'text6',
                'size'     => 206,
                'tmp_name' => 'tmp_test6',
                'error'    => 5],
            'test7' => [
                'name'     => 'test7',
                'type'     => 'text7',
                'size'     => 207,
                'tmp_name' => 'tmp_test7',
                'error'    => 6],
            'test8' => [
                'name'     => 'test8',
                'type'     => 'text8',
                'size'     => 208,
                'tmp_name' => 'tmp_test8',
                'error'    => 7],
            'test9' => [
                'name'     => 'test9',
                'type'     => 'text9',
                'size'     => 209,
                'tmp_name' => 'tmp_test9',
                'error'    => 8]
        ];

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test'));
        $this->assertArrayHasKey('fileUploadErrorAttack', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test2'));
        $this->assertArrayHasKey('fileUploadErrorIniSize', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test3'));
        $this->assertArrayHasKey('fileUploadErrorFormSize', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test4'));
        $this->assertArrayHasKey('fileUploadErrorPartial', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test5'));
        $this->assertArrayHasKey('fileUploadErrorNoFile', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test6'));
        $this->assertArrayHasKey('fileUploadErrorUnknown', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test7'));
        $this->assertArrayHasKey('fileUploadErrorNoTmpDir', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test8'));
        $this->assertArrayHasKey('fileUploadErrorCantWrite', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test9'));
        $this->assertArrayHasKey('fileUploadErrorExtension', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test1'));
        $this->assertArrayHasKey('fileUploadErrorAttack', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('tmp_test1'));
        $this->assertArrayHasKey('fileUploadErrorAttack', $validator->getMessages());

        $validator = new File\Upload();
        $this->assertFalse($validator->isValid('test000'));
        $this->assertArrayHasKey('fileUploadErrorFileNotFound', $validator->getMessages());
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testPsrBasic()
    {
        $files = [
            'test'  => new UploadedFile(
                __DIR__ . '/_files/testsize.mo', // has to be real file
                200,
                0,
                'test1',
                'text'
            ),
            'test2' => new UploadedFile(
                'tmp_test2',
                202,
                1,
                'test2',
                'text2'
            ),
            'test3' => new UploadedFile(
                'tmp_test3',
                203,
                2,
                'test3',
                'text3'
            ),
            'test4' => new UploadedFile(
                'tmp_test4',
                204,
                3,
                'test4',
                'text4'
            ),
            'test5' => new UploadedFile(
                'tmp_test5',
                205,
                4,
                'test5',
                'text5'
            ),
            'test6' => new UploadedFile(
                'tmp_test6',
                206,
                5,
                'test6',
                'text6'
            ),
            'test7' => new UploadedFile(
                'tmp_test7',
                207,
                6,
                'test7',
                'text7'
            ),
            'test8' => new UploadedFile(
                'tmp_test8',
                208,
                7,
                'test8',
                'text8'
            ),
            'test9' => new UploadedFile(
                'tmp_test9',
                209,
                8,
                'test9',
                'text9'
            )
        ];

        $validator = new File\Upload();
        $validator->setFiles($files);
        $this->assertFalse($validator->isValid('test'));
        $this->assertArrayHasKey('fileUploadErrorAttack', $validator->getMessages());

        $this->assertFalse($validator->isValid('test2'));
        $this->assertArrayHasKey('fileUploadErrorIniSize', $validator->getMessages());

        $this->assertFalse($validator->isValid('test3'));
        $this->assertArrayHasKey('fileUploadErrorFormSize', $validator->getMessages());

        $this->assertFalse($validator->isValid('test4'));
        $this->assertArrayHasKey('fileUploadErrorPartial', $validator->getMessages());

        $this->assertFalse($validator->isValid('test5'));
        $this->assertArrayHasKey('fileUploadErrorNoFile', $validator->getMessages());

        $this->assertFalse($validator->isValid('test6'));
        $this->assertArrayHasKey('fileUploadErrorUnknown', $validator->getMessages());

        $this->assertFalse($validator->isValid('test7'));
        $this->assertArrayHasKey('fileUploadErrorNoTmpDir', $validator->getMessages());

        $this->assertFalse($validator->isValid('test8'));
        $this->assertArrayHasKey('fileUploadErrorCantWrite', $validator->getMessages());

        $this->assertFalse($validator->isValid('test9'));
        $this->assertArrayHasKey('fileUploadErrorExtension', $validator->getMessages());

        $this->assertFalse($validator->isValid('test1'));
        $this->assertArrayHasKey('fileUploadErrorAttack', $validator->getMessages());

        // not testing lookup by temp file name since PSR does not expose it

        $this->assertFalse($validator->isValid('test000'));
        $this->assertArrayHasKey('fileUploadErrorFileNotFound', $validator->getMessages());
    }

    /**
     * Ensures that getFiles() returns expected value
     *
     * @return void
     */
    public function testGetFiles()
    {
        $_FILES = [
            'test'  => [
                'name'     => 'test1',
                'type'     => 'text',
                'size'     => 200,
                'tmp_name' => 'tmp_test1',
                'error'    => 0],
            'test2' => [
                'name'     => 'test3',
                'type'     => 'text2',
                'size'     => 202,
                'tmp_name' => 'tmp_test2',
                'error'    => 1]];

        $files = [
            'test'  => [
                'name'     => 'test1',
                'type'     => 'text',
                'size'     => 200,
                'tmp_name' => 'tmp_test1',
                'error'    => 0]];

        $files1 = [
            'test2' => [
                'name'     => 'test3',
                'type'     => 'text2',
                'size'     => 202,
                'tmp_name' => 'tmp_test2',
                'error'    => 1]];

        $validator = new File\Upload();
        $this->assertEquals($files, $validator->getFiles('test'));
        $this->assertEquals($files, $validator->getFiles('test1'));
        $this->assertEquals($files1, $validator->getFiles('test3'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('was not found');
        $this->assertEquals([], $validator->getFiles('test5'));
    }

    public function testPsrGetFiles()
    {
        $files = [
            'test'  => new UploadedFile(
                'tmp_test1',
                200,
                0,
                'test1',
                'text'
            ),
            'test2' => new UploadedFile(
                'tmp_test2',
                202,
                1,
                'test3',
                'text2'
            )
        ];

        $validator = new File\Upload();
        $validator->setFiles($files);
        $this->assertEquals(['test' => $files['test']], $validator->getFiles('test'));
        $this->assertEquals(['test' => $files['test']], $validator->getFiles('test1'));
        $this->assertEquals(['test2' => $files['test2']], $validator->getFiles('test3'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('was not found');
        $this->assertEquals([], $validator->getFiles('test5'));
    }

    /**
     * Ensures that setFiles() returns expected value
     *
     * @return void
     */
    public function testSetFiles()
    {
        $files = [
            'test'  => [
                'name'     => 'test1',
                'type'     => 'text',
                'size'     => 200,
                'tmp_name' => 'tmp_test1',
                'error'    => 0],
            'test2' => [
                'name'     => 'test2',
                'type'     => 'text2',
                'size'     => 202,
                'tmp_name' => 'tmp_test2',
                'error'    => 1]];

        $_FILES = [
            'test'  => [
                'name'     => 'test3',
                'type'     => 'text3',
                'size'     => 203,
                'tmp_name' => 'tmp_test3',
                'error'    => 2]];


        $validator = new File\Upload();
        $validator->setFiles([]);
        $this->assertEquals($_FILES, $validator->getFiles());
        $validator->setFiles();
        $this->assertEquals($_FILES, $validator->getFiles());
        $validator->setFiles($files);
        $this->assertEquals($files, $validator->getFiles());
    }

    public function testPsrSetFiles()
    {
        $psrFiles = [
            'test4' => new UploadedFile(
                'tmp_test4',
                204,
                3,
                'test4',
                'text5'
            ),
            'test5' => new UploadedFile(
                'tmp_test5',
                205,
                4,
                'test5',
                'text5'
            )
        ];

        $validator = new File\Upload();
        $validator->setFiles($psrFiles);
        $this->assertEquals($psrFiles, $validator->getFiles());
    }

    /**
     * @group ZF-10738
     */
    public function testGetFilesReturnsEmptyArrayWhenFilesSuperglobalIsNull()
    {
        $_FILES = null;
        $validator = new File\Upload();
        $validator->setFiles();
        $this->assertEquals([], $validator->getFiles());
    }

    /**
     * @group ZF-10738
     */
    public function testGetFilesReturnsEmptyArrayAfterSetFilesIsCalledWithNull()
    {
        $validator = new File\Upload();
        $validator->setFiles(null);
        $this->assertEquals([], $validator->getFiles());
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\Upload();
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertArrayHasKey('fileUploadErrorFileNotFound', $validator->getMessages());
        $this->assertContains("nofile.mo'", current($validator->getMessages()));
    }

    /**
     * @group ZF-12128
     */
    public function testErrorMessage()
    {
        $_FILES = [
            'foo' => [
                'name'     => 'bar',
                'type'     => 'text',
                'size'     => 100,
                'tmp_name' => 'tmp_bar',
                'error'    => 7,
            ]
        ];

        $validator = new File\Upload;
        $validator->isValid('foo');

        $this->assertEquals(
            [
                'fileUploadErrorCantWrite' => "Failed to write file 'bar' to disk",
            ],
            $validator->getMessages()
        );
    }

    /**
     * @group ZF-12128
     */
    public function testPsrErrorMessage()
    {
        $files = [
            'foo' => new UploadedFile(
                'tmp_bar',
                100,
                7,
                'bar',
                'text'
            )
        ];

        $validator = new File\Upload;
        $validator->setFiles($files);
        $validator->isValid('foo');

        $this->assertEquals(
            [
                'fileUploadErrorCantWrite' => "Failed to write file 'bar' to disk",
            ],
            $validator->getMessages()
        );
        $validator->setFiles($files);
    }
}
