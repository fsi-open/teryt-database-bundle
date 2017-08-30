<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use FSi\Bundle\TerytDatabaseBundle\Teryt\Api\Client;
use SplTempFileObject;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Filesystem\Filesystem;

abstract class TerytDownloadCommand extends ContainerAwareCommand
{
    protected function getDefaultTargetPath()
    {
        return $this->getContainer()->getParameter('kernel.root_dir') . '/teryt';
    }

    protected function getApiClient() : Client
    {
        return $this->getContainer()->get('fsi_teryt_db.api_client');
    }

    protected function saveFile(SplTempFileObject $file, string $path, string $fileName)
    {
        $filesystem = new Filesystem();
        $filesystem->dumpFile(sprintf('%s/%s', $path, $fileName), $file->fread($this->getFileSize($file)));
    }

    private function getFileSize(SplTempFileObject $file) : int
    {
        $file->fseek(0, SEEK_END);
        $size = $file->ftell();
        $file->fseek(0, SEEK_SET);

        return $size;
    }
}
