<?php

namespace Aureka\VBBundle\Tests\Application;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Aureka\VBBundle\AurekaVBBundle(),
            );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }

    public function getCacheDir()
    {
        return __DIR__.'/../../../../../testcache/cache';
    }

    public function getLogsDir()
    {
        return __DIR__.'/../../../../../testcache/logs';

    }
}
