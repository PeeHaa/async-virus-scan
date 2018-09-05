<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Service\VirusTotal;

class ApiKey
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
