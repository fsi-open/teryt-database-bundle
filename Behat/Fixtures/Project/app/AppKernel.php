<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new FSi\Bundle\TerytDatabaseBundle\FSiTerytDbBundle(),
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