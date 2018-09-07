<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Integration\Service\VirusTotal;

use Amp\Artax\DefaultClient;
use Amp\Loop;
use PeeHaa\AsyncVirusScan\FileSystem\File;
use PeeHaa\AsyncVirusScan\Http\Client;
use PeeHaa\AsyncVirusScan\Service\VirusTotal\ApiKey;
use PeeHaa\AsyncVirusScan\Service\VirusTotal\Service;
use PHPUnit\Framework\TestCase;

class CleanFileTest extends TestCase
{
    public function testReturnsFalseOnCleanFile()
    {
        $apiKey     = new ApiKey(getenv('virusTotalKey'));
        $httpClient = new Client(new DefaultClient());

        $service    = new Service($apiKey, $httpClient);

        Loop::run(static function() use ($service) {
            $result = yield $service->scan(yield File::build(TEST_DATA_DIR . '/samples/clean.txt'));

            $this->assertFalse($result);
        });
    }
}
