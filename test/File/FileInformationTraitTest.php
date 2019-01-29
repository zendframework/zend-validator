<?php
/**
 * @see       https://github.com/zendframework/zend-validator for the canonical source repository
 * @copyright Copyright (c) 2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-validator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Validator\File;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use ZendTest\Validator\File\TestAsset\FileInformation;
use Zend\Validator\Exception\InvalidArgumentException;

class FileInformationTraitTest extends TestCase
{
    /** @var ObjectProphecy|StreamInterface */
    public $stream;

    /** @var ObjectProphecy */
    public $upload;

    public function setUp()
    {
        $this->stream = $this->prophesize(StreamInterface::class);
        $this->upload = $this->prophesize(UploadedFileInterface::class);
    }

    public function testLegacyFileInfoBasic()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';
        $basename = basename($testFile);
        $file = [
          'name'     => $basename,
          'tmp_name' => $testFile
        ];

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $basename,
            $file
        );

        $this->assertEquals($fileInfo, [
          'filename' => $file['name'],
          'file'     => $file['tmp_name'],
        ]);
    }

    public function testLegacyFileInfoWithFiletype()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';
        $basename = basename($testFile);
        $file = [
          'name'     => $basename,
          'tmp_name' => $testFile,
          'type' => 'mo',
        ];

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $basename,
            $file,
            true
        );

        $this->assertEquals($fileInfo, [
          'filename' => $file['name'],
          'file'     => $file['tmp_name'],
          'filetype' => $file['type'],
        ]);
    }

    public function testLegacyFileInfoWithBasename()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';
        $basename = basename($testFile);
        $file = [
          'name'     => $basename,
          'tmp_name' => $testFile,
        ];

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $basename,
            $file,
            false,
            true
        );

        $this->assertEquals($fileInfo, [
          'filename' => $file['name'],
          'file'     => $file['tmp_name'],
          'basename' => basename($file['tmp_name']),
        ]);
    }

    public function testSapiFileInfoBasic()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';
        $file = [
          'name'     => basename($testFile),
          'tmp_name' => $testFile
        ];

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $file
        );

        $this->assertEquals($fileInfo, [
          'filename' => $file['name'],
          'file'     => $file['tmp_name'],
        ]);
    }

    public function testSapiFileInfoWithFiletype()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';
        $file = [
          'name'     => basename($testFile),
          'tmp_name' => $testFile,
          'type'     => 'mo',
        ];

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $file,
            null,
            true
        );

        $this->assertEquals($fileInfo, [
          'filename' => $file['name'],
          'file'     => $file['tmp_name'],
          'filetype' => $file['type'],
        ]);
    }

    public function testSapiFileInfoWithBasename()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';
        $file = [
          'name'     => basename($testFile),
          'tmp_name' => $testFile,
        ];

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $file,
            null,
            false,
            true
        );

        $this->assertEquals($fileInfo, [
          'filename' => $file['name'],
          'file'     => $file['tmp_name'],
          'basename' => basename($file['tmp_name']),
        ]);
    }

    public function testPsr7FileInfoBasic()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';

        $this->stream->getMetadata('uri')->willReturn($testFile);
        $this->upload->getClientFilename()->willReturn(basename($testFile));
        $this->upload->getClientMediaType()->willReturn(mime_content_type($testFile));
        $this->upload->getStream()->willReturn($this->stream->reveal());

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $this->upload->reveal()
        );

        $this->assertEquals($fileInfo, [
          'filename' => basename($testFile),
          'file'     => $testFile,
        ]);
    }

    public function testPsr7FileInfoBasicWithFiletype()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';

        $this->stream->getMetadata('uri')->willReturn($testFile);
        $this->upload->getClientFilename()->willReturn(basename($testFile));
        $this->upload->getClientMediaType()->willReturn(mime_content_type($testFile));
        $this->upload->getStream()->willReturn($this->stream->reveal());

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $this->upload->reveal(),
            null,
            true
        );

        $this->assertEquals($fileInfo, [
          'filename' => basename($testFile),
          'file'     => $testFile,
          'filetype' => mime_content_type($testFile),
        ]);
    }

    public function testPsr7FileInfoBasicWithBasename()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';

        $this->stream->getMetadata('uri')->willReturn($testFile);
        $this->upload->getClientFilename()->willReturn(basename($testFile));
        $this->upload->getClientMediaType()->willReturn(mime_content_type($testFile));
        $this->upload->getStream()->willReturn($this->stream->reveal());

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $this->upload->reveal(),
            null,
            false,
            true
        );

        $this->assertEquals($fileInfo, [
          'filename' => basename($testFile),
          'file'     => $testFile,
          'basename' => basename($testFile),
        ]);
    }

    public function testFileBasedFileInfoBasic()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $testFile
        );

        $this->assertEquals($fileInfo, [
          'filename' => basename($testFile),
          'file'     => $testFile,
        ]);
    }

    public function testFileBasedFileInfoBasicWithFiletype()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $testFile,
            null,
            true
        );

        $this->assertEquals($fileInfo, [
          'filename' => basename($testFile),
          'file'     => $testFile,
          'filetype' => null
        ]);
    }

    public function testFileBasedFileInfoBasicWithBasename()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';

        $fileInformation = new FileInformation();
        $fileInfo = $fileInformation->checkFileInformation(
            $testFile,
            null,
            false,
            true
        );

        $this->assertEquals($fileInfo, [
          'filename' => basename($testFile),
          'file'     => $testFile,
          'basename' => basename($testFile)
        ]);
    }
}
