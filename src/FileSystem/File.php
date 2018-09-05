<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\FileSystem;

use Amp\Promise;
use PeeHaa\AsyncVirusScan\FileSystem\Exception\InvalidFile;
use PeeHaa\AsyncVirusScan\FileSystem\Exception\NotFound;
use function Amp\call;
use function Amp\File\exists;
use function Amp\File\isfile;
use function Amp\File\size;

class File implements Promise
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getSize(): Promise
    {
        return size($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function onResolve(callable $onResolved): void
    {
        call(function() use ($onResolved) {
            if (!yield exists($this->path)) {
                throw new NotFound($this->path);
            }

            if (!yield isfile($this->path)) {
                throw new InvalidFile($this->path);
            }

            $onResolved(null, $this);
        });
    }
}
