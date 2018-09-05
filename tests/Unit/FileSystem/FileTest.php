<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Unit\FileSystem;

use Amp\Loop;
use PeeHaa\AsyncVirusScan\FileSystem\Exception\InvalidFile;
use PeeHaa\AsyncVirusScan\FileSystem\Exception\NotFound;
use PeeHaa\AsyncVirusScan\FileSystem\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testBuildFileThrowsWhenFileDoesNotExist()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('The file ' . TEST_DATA_DIR . '/non-existent.txt could not be found.');

        Loop::run(function() {
            yield File::build(TEST_DATA_DIR . '/non-existent.txt');
        });
    }

    public function testBuildFileThrowsWhenPathIsNotAFile()
    {
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage(TEST_DATA_DIR . '/test-directory is not a file.');

        Loop::run(function() {
            yield File::build(TEST_DATA_DIR . '/test-directory');
        });
    }

    public function testBuildFileReturnsInstanceOnValidFilePath()
    {
        Loop::run(function() {
            $file = yield File::build(TEST_DATA_DIR . '/test-file.txt');

            $this->assertInstanceOf(File::class, $file);
        });
    }

    public function testGetSize()
    {
        Loop::run(function() {
            /** @var File $file */
            $file = yield File::build(TEST_DATA_DIR . '/test-file.txt');

            $this->assertSame(10, yield $file->getSize());
        });
    }

    public function getPath()
    {
        Loop::run(function() {
            /** @var File $file */
            $file = yield File::build(TEST_DATA_DIR . '/test-file.txt');

            $this->assertSame(TEST_DATA_DIR . '/test-file.txt', yield $file->getPath());
        });
    }
}
