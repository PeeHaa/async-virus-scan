<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Service;

class BadRequest extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
