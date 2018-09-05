<?php declare(strict_types=1);

namespace PeeHaa\AsyncVirusScan\FileSystem;

use Amp\Promise;
use PeeHaa\AsyncVirusScan\FileSystem\Exception\InvalidFile;
use PeeHaa\AsyncVirusScan\FileSystem\Exception\NotFound;
use function Amp\call;
use function Amp\File\exists;
use function Amp\File\isfile;
use function Amp\File\size;

class File
{
    private $path;

    private function __construct() {}

    public static function build(string $path): Promise
    {
        return call(static function() use ($path) {
            if (!yield exists($path)) {
                throw new NotFound($path);
            }

            if (!yield isfile($path)) {
                throw new InvalidFile($path);
            }

            $instance = new self();

            $instance->path = $path;

            return $instance;
        });
    }

    public function getSize(): Promise
    {
        return size($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
