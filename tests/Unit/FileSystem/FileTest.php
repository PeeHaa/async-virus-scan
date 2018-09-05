<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Unit\FileSystem;

use Amp\Loop;
use PeeHaa\AsyncVirusScan\FileSystem\Exception\NotFound;
use PeeHaa\AsyncVirusScan\FileSystem\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testInstantiationThrowsWhenFileDoesNotExist()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('The file ' . TEST_DATA_DIR . '/non-existent.txt could not be found.');

        Loop::run(function() {
            yield new File(TEST_DATA_DIR . '/non-existent.txt');
        });
    }
}
