<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace TestApp;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \FSi\Bundle\TerytDatabaseBundle\FSiTerytDbBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(sprintf('%s/config/config_%s.yml', __DIR__, $this->getEnvironment()));
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/FSiTerytDbBundle/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/FSiTerytDbBundle/logs';
    }
}
