<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Examples;

use Amp\Artax\DefaultClient;
use PeeHaa\AsyncVirusScan\Http\Client;
use PeeHaa\AsyncVirusScan\Service\VirusTotal\ApiKey;
use PeeHaa\AsyncVirusScan\Service\VirusTotal\Service;
use function Amp\Promise\wait;

require_once __DIR__ . '/../vendor/autoload.php';

$apiKey     = new ApiKey('yourapikey');
$httpClient = new Client(new DefaultClient());

$service    = new Service($apiKey, $httpClient);

$result     = wait($service->scan(__DIR__ . '/files/cleanfile.txt'));

var_dump('Is infected: ', $result);
