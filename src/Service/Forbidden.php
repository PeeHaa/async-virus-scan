<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Service;

class Forbidden extends Exception
{
    private const MESSAGE = 'You are not allowed to execute this action.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
