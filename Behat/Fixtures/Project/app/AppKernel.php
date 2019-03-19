<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new FSi\Bundle\TerytDatabaseBundle\FSiTerytDbBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
        );
    }


    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config_%s.yml', __DIR__, $this->getEnvironment()));
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/FSiTerytDbBundle/cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/FSiTerytDbBundle/logs';
    }
}
