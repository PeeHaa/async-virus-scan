<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScanTest\Fakes\Service\VirusTotal;

use Amp\Artax\ConnectionInfo;
use Amp\Artax\MetaInfo;
use Amp\Artax\Request;
use Amp\Artax\Response;
use Amp\ByteStream\InputStream;
use Amp\ByteStream\Message;
use Amp\Success;
use PHPUnit\Framework\MockObject\Generator;
use PHPUnit\Framework\MockObject\MockObject;

class RateLimitResponse implements Response
{
    public function getProtocolVersion(): string
    {
        return '1.1';
    }

    public function getStatus(): int
    {
        return 204;
    }

    public function getReason(): string
    {
        return '';
    }

    public function getRequest(): Request
    {
        return new Request('https://example.com');
    }

    public function getOriginalRequest(): Request
    {
        return new Request('https://example.com');
    }

    public function getPreviousResponse()
    {
        return null;
    }

    public function hasHeader(string $field): bool
    {
        return false;
    }

    public function getHeader(string $field)
    {
        return null;
    }

    public function getHeaderArray(string $field): array
    {
        return [];
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getBody(): Message
    {
        /** @var MockObject|InputStream $inputStream */
        $inputStream = (new Generator())->getMock(InputStream::class);

        $inputStream
            ->method('read')
            ->willReturnOnConsecutiveCalls(new Success(json_encode([
                'resource' => 'abc123',
            ])), new Success(null))
        ;

        return new Message($inputStream);
    }

    public function getMetaInfo(): MetaInfo
    {
        return new MetaInfo(new ConnectionInfo('127.0.0.1', '8.8.8.8'));
    }
}
