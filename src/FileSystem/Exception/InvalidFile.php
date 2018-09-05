<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\FileSystem\Exception;

use PeeHaa\AsyncVirusScan\Exception;

class InvalidFile extends Exception
{
    private const MESSAGE = '%s is not a file.';

    public function __construct(string $path)
    {
        parent::__construct(sprintf(self::MESSAGE, $path));
    }
}
