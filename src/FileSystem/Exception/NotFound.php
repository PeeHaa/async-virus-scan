<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\FileSystem\Exception;

use PeeHaa\AsyncVirusScan\Exception;

class NotFound extends Exception
{
    private const MESSAGE = 'The file %s could not be found.';

    public function __construct(string $path)
    {
        parent::__construct(sprintf(self::MESSAGE, $path));
    }
}
