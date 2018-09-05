<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Unit\Http;

use Amp\Artax\Client as ArtaxClient;
use Amp\Artax\Request;
use Amp\Loop;
use PeeHaa\AsyncVirusScan\Http\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testRequestPassesThroughRequestObject()
    {
        Loop::run(function() {
            $request = new Request('https://exmaple.com');

            /** @var MockObject|ArtaxClient $artaxClient */
            $artaxClient = $this->createMock(ArtaxClient::class);

            $artaxClient
                ->expects($this->once())
                ->method('request')
                ->with($request)
            ;

            $client = new Client($artaxClient);

            $client->request($request);
        });
    }
}
