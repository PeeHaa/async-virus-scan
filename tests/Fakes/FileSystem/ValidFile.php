<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Fakes\FileSystem;

use Amp\Promise;
use Amp\Success;
use PeeHaa\AsyncVirusScan\FileSystem\File;

class ValidFile extends File
{
    public function __construct()
    {
    }

    public function getSize(): Promise
    {
        return new Success(32);
    }

    public function getPath(): string
    {
        return 'valid-file.txt';
    }
}
