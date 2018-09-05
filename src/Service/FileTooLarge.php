<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Service;

use PeeHaa\AsyncVirusScan\FileSystem\File;

class FileTooLarge extends Exception
{
    private const MESSAGE = 'File %s is too large. Allowed file size is %d bytes, but supplied file is %d bytes.';

    public function __construct(File $file, int $allowedSize, int $fileSize)
    {
        parent::__construct(sprintf(self::MESSAGE, $file->getPath(), $allowedSize, $fileSize));
    }
}
