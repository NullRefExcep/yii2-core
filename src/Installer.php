<?php

namespace nullref\core;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


class Installer extends LibraryInstaller
{
    protected function downloadComposer()
    {
        $url = 'https://getcomposer.org/composer.phar';
        $path =  'composer.phar';
        file_put_contents($path, file_get_contents($url));
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
    }

}