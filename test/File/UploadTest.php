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
use Psr\Http\Message\UploadedFileInterface;
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

    public function invalidPsr7UploadedFiles()
    {
        $uploads = [];

        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test2');
        $upload->getError()->willReturn(1);
        yield 'size' => [['test2' => $upload->reveal()], 'test2', 'fileUploadErrorIniSize'];

        $uploads['test2'] = $upload->reveal();

        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test3');
        $upload->getError()->willReturn(2);
        yield 'form-size' => [['test3' => $upload->reveal()], 'test3', 'fileUploadErrorFormSize'];

        $uploads['test3'] = $upload->reveal();

        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test4');
        $upload->getError()->willReturn(3);
        yield 'partial' => [['test4' => $upload->reveal()], 'test4', 'fileUploadErrorPartial'];

        $uploads['test4'] = $upload->reveal();

        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test5');
        $upload->getError()->willReturn(4);
        yield 'no-file' => [['test5' => $upload->reveal()], 'test5', 'fileUploadErrorNoFile'];

        $uploads['test5'] = $upload->reveal();

        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test6');
        $upload->getError()->willReturn(5);
        yield 'unknown' => [['test6' => $upload->reveal()], 'test6', 'fileUploadErrorUnknown'];

        $uploads['test6'] = $upload->reveal();

        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test7');
        $upload->getError()->willReturn(6);
        yield 'no-tmp-dir' => [['test7' => $upload->reveal()], 'test7', 'fileUploadErrorNoTmpDir'];

        $uploads['test7'] = $upload->reveal();

        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test8');
        $upload->getError()->willReturn(7);
        yield 'cannot write' => [['test8' => $upload->reveal()], 'test8', 'fileUploadErrorCantWrite'];

        $uploads['test8'] = $upload->reveal();

        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test9');
        $upload->getError()->willReturn(8);
        yield 'cannot write' => [['test9' => $upload->reveal()], 'test9', 'fileUploadErrorExtension'];

        $uploads['test9'] = $upload->reveal();

        yield 'not-found' => [$uploads, 'test000', 'fileUploadErrorFileNotFound'];
    }

    /**
     * Validate invalid PSR-7 file uploads
     *
     * Not testing lookup by temp file name since PSR does not expose it.
     *
     * @dataProvider invalidPsr7UploadedFiles
     * @param UploadedFileInterface[] $files
     * @param string $fileName
     * @param string $expectedErrorKey
     * @return void
     */
    public function testRaisesExpectedErrorsForInvalidPsr7UploadedFileInput($files, $fileName, $expectedErrorKey)
    {
        $validator = new File\Upload();
        $validator->setFiles($files);
        $this->assertFalse($validator->isValid($fileName));
        $this->assertArrayHasKey($expectedErrorKey, $validator->getMessages());
    }

    public function testCanValidateCorrectlyFormedPsr7UploadedFiles()
    {
        $upload = $this->prophesize(UploadedFileInterface::class);
        $upload->getClientFilename()->willReturn('test');
        $upload->getError()->willReturn(0);

        $validator = new File\Upload();
        $validator->setFiles(['upload' => $upload->reveal()]);

        $this->assertTrue($validator->isValid('test'));
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

    public function testGetFilesReturnsArtifactsFromPsr7UploadedFiles()
    {
        $upload1 = $this->prophesize(UploadedFileInterface::class);
        $upload1->getClientFilename()->willReturn('test1');

        $upload2 = $this->prophesize(UploadedFileInterface::class);
        $upload2->getClientFilename()->willReturn('test3');

        $files = [
            'test'  => $upload1->reveal(),
            'test2' => $upload2->reveal(),
        ];

        $validator = new File\Upload();
        $validator->setFiles($files);

        // Retrieve by index
        $this->assertEquals(['test' => $files['test']], $validator->getFiles('test'));
        $this->assertEquals(['test2' => $files['test2']], $validator->getFiles('test2'));

        // Retrieve by client filename
        $this->assertEquals(['test' => $files['test']], $validator->getFiles('test1'));
        $this->assertEquals(['test2' => $files['test2']], $validator->getFiles('test3'));

        return $validator;
    }

    /**
     * @depends testGetFilesReturnsArtifactsFromPsr7UploadedFiles
     */
    public function testGetFilesRaisesExceptionWhenPsr7UploadedFilesArrayDoesNotContainGivenFilename(
        File\Upload $validator
    ) {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('was not found');
        $validator->getFiles('test5');
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

    public function testCanPopulateFilesFromArrayOfPsr7UploadedFiles()
    {
        $upload1 = $this->prophesize(UploadedFileInterface::class);
        $upload2 = $this->prophesize(UploadedFileInterface::class);

        $psrFiles = [
            'test4' => $upload1->reveal(),
            'test5' => $upload2->reveal(),
        ];

        $validator = new File\Upload();
        $validator->setFiles($psrFiles);
        $this->assertSame($psrFiles, $validator->getFiles());
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
}
