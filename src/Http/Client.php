<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Http;

use Amp\Artax\Client as ArtaxClient;
use Amp\Artax\Request;
use Amp\Promise;

class Client
{
    private $client;

    public function __construct(ArtaxClient $client)
    {
        $this->client = $client;
    }

    public function request(Request $request): Promise
    {
        return $this->client->request($request);
    }
}
