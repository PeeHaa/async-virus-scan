<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\Service\VirusTotal;

use Amp\Artax\FormBody;
use Amp\Artax\Request;
use Amp\Artax\Response;
use Amp\Delayed;
use Amp\Promise;
use PeeHaa\AsyncVirusScan\FileSystem\File;
use PeeHaa\AsyncVirusScan\Http\Client;
use PeeHaa\AsyncVirusScan\Service\BadRequest;
use PeeHaa\AsyncVirusScan\Service\Exception;
use PeeHaa\AsyncVirusScan\Service\FileTooLarge;
use PeeHaa\AsyncVirusScan\Service\Forbidden;
use PeeHaa\AsyncVirusScan\Service\RateLimit;
use PeeHaa\AsyncVirusScan\Service\TimeOut;
use function Amp\call;

class Service
{
    // 1 minute interval to check the report status
    private const REPORT_INTERVAL = 60 * 1000;

    // 10 minute before timing out
    private const REPORT_TIMEOUT  = 10 * 60;

    private const SCAN_ENDPOINT   = 'https://www.virustotal.com/vtapi/v2/file/scan';

    private const REPORT_ENDPOINT = 'https://www.virustotal.com/vtapi/v2/file/report?apikey=%s&resource=%s';

    // max file size in bytes (32MB)
    private const MAX_FILE_SIZE = 32 * 1024 * 1024;

    private $apiKey;

    private $httpClient;

    public function __construct(ApiKey $apiKey, Client $httpClient)
    {
        $this->apiKey     = $apiKey;
        $this->httpClient = $httpClient;
    }

    public function scan(string $filePath): Promise
    {
        return call(function() use ($filePath) {
            /** @var File $file */
            $file = yield new File($filePath);

            if ((yield $file->getSize()) > self::MAX_FILE_SIZE) {
                throw new FileTooLarge($file, self::MAX_FILE_SIZE, yield $file->getSize());
            }

            $scanResult = yield $this->processResponse(
                yield $this->httpClient->request($this->buildScanRequest($file))
            );

            $timeOut = (new \DateTimeImmutable())->add(new \DateInterval(sprintf('PT%dM', self::REPORT_TIMEOUT)));

            while (true) {
                if (new \DateTimeImmutable() > $timeOut) {
                    throw new TimeOut();
                }

                $reportResult = yield $this->processResponse(
                    yield $this->httpClient->request($this->buildReportRequest($scanResult['resource']))
                );

                if ($reportResult['response_code'] === 0) {
                    throw new Exception('File not correctly uploaded to the service.');
                }

                if ($reportResult['response_code'] === 1) {
                    return (bool) $reportResult['positives'];
                }

                yield new Delayed(self::REPORT_INTERVAL);
            }
        });
    }

    private function buildScanRequest(File $file): Request
    {
        $body = new FormBody();
        $body->addField('apikey', $this->apiKey->getKey());
        $body->addFile('file', $file->getPath());

        return (new Request(self::SCAN_ENDPOINT, 'POST'))->withBody($body);
    }

    private function buildReportRequest(string $resourceId): Request
    {
        return new Request(sprintf(self::REPORT_ENDPOINT, $this->apiKey->getKey(), $resourceId));
    }

    private function processResponse(Response $response): Promise
    {
        return call(static function() use ($response) {
            $result = json_decode(yield $response->getBody(), true);

            if ($response->getStatus() === 204) {
                throw new RateLimit();
            }

            if ($response->getStatus() === 400) {
                throw new BadRequest($result['verbose_msg']);
            }

            if ($response->getStatus() === 403) {
                throw new Forbidden();
            }

            return $result;
        });
    }
}
