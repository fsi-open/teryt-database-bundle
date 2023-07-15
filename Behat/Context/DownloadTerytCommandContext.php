<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Assert\Assertion;
use Behat\Behat\Context\Context;
use Symfony\Component\Filesystem\Filesystem;

class DownloadTerytCommandContext implements Context
{
    /**
     * @var string
     */
    protected $fixturesPath;

    public function __construct(string $fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
    }

    /**
     * @Then /^"([^"]*)" file should be downloaded into "([^"]*)" folder$/
     */
    public function fileShouldBeDownloadedIntoFolder(string $fileName, string $targetFilesPath): void
    {
        $downloadPath = realpath(__DIR__ . '/../../' . $targetFilesPath);
        $filePath = $downloadPath . '/' . $fileName;
        Assertion::true(file_exists($filePath));
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(): void
    {
        $terytDownloadPath = $this->fixturesPath . '/teryt';
        if (!file_exists($terytDownloadPath)) {
            return;
        }

        $fileSystem = new Filesystem();
        $fileSystem->remove($terytDownloadPath);
    }
}
