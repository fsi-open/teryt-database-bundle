<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class DownloadTerytCommandContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    protected $fixturesPath;

    public function __construct($fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
    }

    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * @Then /^"([^"]*)" file should be downloaded into "([^"]*)" folder$/
     */
    public function fileShouldBeDownloadedIntoFolder(string $fileName, string $targetFilesPath): void
    {
        $downloadPath = realpath( __DIR__ . '/../' . $targetFilesPath);
        $filePath = $downloadPath . '/' . $fileName;
        expect(file_exists($filePath))->toBe(true);
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(): void
    {
        $terytDownloadPath = $this->fixturesPath . '/Project/app/teryt';
        if (!file_exists($terytDownloadPath)) {
            return;
        }

        $fileSystem = new Filesystem();
        $fileSystem->remove($terytDownloadPath);
    }
}
