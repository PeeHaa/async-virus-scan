<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Service;

class TimeOut extends Exception
{
    private const MESSAGE = 'Service timed out.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
