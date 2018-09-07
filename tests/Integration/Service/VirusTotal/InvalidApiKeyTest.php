<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Integration\Service\VirusTotal;

use Amp\Artax\DefaultClient;
use Amp\Loop;
use PeeHaa\AsyncVirusScan\FileSystem\File;
use PeeHaa\AsyncVirusScan\Http\Client;
use PeeHaa\AsyncVirusScan\Service\Forbidden;
use PeeHaa\AsyncVirusScan\Service\VirusTotal\ApiKey;
use PeeHaa\AsyncVirusScan\Service\VirusTotal\Service;
use PHPUnit\Framework\TestCase;

class InvalidApiKeyTest extends TestCase
{
    public function testThrowsForbiddenExceptionOnInvalidApiKey()
    {
        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('You are not allowed to execute this action.');

        $apiKey     = new ApiKey('invalidkey');
        $httpClient = new Client(new DefaultClient());

        $service    = new Service($apiKey, $httpClient);

        Loop::run(static function() use ($service) {
            yield $service->scan(yield File::build(TEST_DATA_DIR . '/samples/clean.txt'));
        });
    }
}
