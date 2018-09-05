<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Unit\VirusTotal;

use Amp\Artax\Request;
use Amp\Artax\Response;
use Amp\ByteStream\InputStream;
use Amp\ByteStream\Message;
use Amp\Loop;
use Amp\Success;
use PeeHaa\AsyncVirusScan\Http\Client;
use PeeHaa\AsyncVirusScan\Service\BadRequest;
use PeeHaa\AsyncVirusScan\Service\FileTooLarge;
use PeeHaa\AsyncVirusScan\Service\Forbidden;
use PeeHaa\AsyncVirusScan\Service\RateLimit;
use PeeHaa\AsyncVirusScan\Service\VirusTotal\ApiKey;
use PeeHaa\AsyncVirusScan\Service\VirusTotal\Service;
use PeeHaa\AsyncVirusScanTest\Fakes\FileSystem\LargeFile;
use PeeHaa\AsyncVirusScanTest\Fakes\FileSystem\ValidFile;
use PeeHaa\AsyncVirusScanTest\Fakes\Service\VirusTotal\BadRequestResponse;
use PeeHaa\AsyncVirusScanTest\Fakes\Service\VirusTotal\ForbiddenResponse;
use PeeHaa\AsyncVirusScanTest\Fakes\Service\VirusTotal\RateLimitResponse;
use PeeHaa\AsyncVirusScanTest\Fakes\Service\VirusTotal\ValidResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    /** @var ApiKey */
    private $apiKey;

    /** @var MockObject|Client */
    private $httpClient;

    public function setUp()
    {
        $this->apiKey     = new ApiKey('mykey');
        $this->httpClient = $this->createMock(Client::class);
    }

    public function testScanThrowsWhenFileIsTooLarge()
    {
        $this->expectException(FileTooLarge::class);
        $this->expectExceptionMessage(sprintf(
            'File %s is too large. Allowed file size is %d bytes, but supplied file is %d bytes.',
            'large-file.txt',
            33554432,
            40000000
        ));

        Loop::run(function() {
            $service = new Service($this->apiKey, $this->httpClient);

            yield $service->scan(new LargeFile());
        });
    }

    public function testScanBuildsScanRequestCorrectly()
    {
        $this->httpClient
            ->expects($this->at(0))
            ->method('request')
            ->willReturnCallback(function(Request $request) {
                $this->assertSame('POST', $request->getMethod());
                $this->assertSame('https://www.virustotal.com/vtapi/v2/file/scan', $request->getUri());

                return new Success(new ValidResponse());
            })
        ;

        Loop::run(function() {
            $service = new Service($this->apiKey, $this->httpClient);

            yield $service->scan(new ValidFile());
        });
    }

    public function testScanThrowsWhenRateLimited()
    {
        $this->expectException(RateLimit::class);
        $this->expectExceptionMessage('Service rate limit exceeded.');

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn(new Success(new RateLimitResponse()))
        ;

        Loop::run(function() {
            $service = new Service($this->apiKey, $this->httpClient);

            yield $service->scan(new ValidFile());
        });
    }

    public function testScanThrowsOnBadRequest()
    {
        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage('Verbose error message');

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn(new Success(new BadRequestResponse()))
        ;

        Loop::run(function() {
            $service = new Service($this->apiKey, $this->httpClient);

            yield $service->scan(new ValidFile());
        });
    }

    public function testScanThrowsOnForbiddenRequest()
    {
        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('You are not allowed to execute this action.');

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn(new Success(new ForbiddenResponse()))
        ;

        Loop::run(function() {
            $service = new Service($this->apiKey, $this->httpClient);

            yield $service->scan(new ValidFile());
        });
    }
}
