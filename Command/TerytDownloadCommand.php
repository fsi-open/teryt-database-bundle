<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Assert\Assertion;
use FSi\Bundle\TerytDatabaseBundle\Teryt\Api\Client;
use SplTempFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

abstract class TerytDownloadCommand extends Command
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $projectDir;

    public function __construct(Client $client, string $projectDir)
    {
        parent::__construct();

        $this->client = $client;
        $this->projectDir = $projectDir;
    }

    protected function getDefaultTargetPath(): string
    {
        return $this->projectDir . '/teryt';
    }

    protected function getApiClient(): Client
    {
        return $this->client;
    }

    protected function saveFile(SplTempFileObject $file, string $path, string $fileName): void
    {
        $filesystem = new Filesystem();
        $content = $file->fread($this->getFileSize($file));
        Assertion::string($content);

        $filesystem->dumpFile(sprintf('%s/%s', $path, $fileName), $content);
    }

    private function getFileSize(SplTempFileObject $file): int
    {
        $file->fseek(0, SEEK_END);
        $size = $file->ftell();
        $file->fseek(0);
        Assertion::integer($size);

        return $size;
    }
}
