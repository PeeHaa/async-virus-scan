<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Unit\VirusTotal;

use PeeHaa\AsyncVirusScan\Service\VirusTotal\ApiKey;
use PHPUnit\Framework\TestCase;

class ApiKeyTest extends TestCase
{
    public function testGetKey()
    {
        $this->assertSame('mykey', (new ApiKey('mykey'))->getKey());
    }
}
