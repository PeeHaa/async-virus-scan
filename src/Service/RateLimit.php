<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Service;

class RateLimit extends Exception
{
    private const MESSAGE = 'Service rate limit exceeded.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
