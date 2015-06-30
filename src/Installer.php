<?php

namespace nullref\core;


use Composer\Installer\PackageEvent;
use nullref\core\components\ModuleInstaller;

class Installer extends ModuleInstaller
{

    public function install(PackageEvent $event)
    {
        $this->downloadComposer();
        parent::install($event);
    }

    public function uninstall(PackageEvent $event)
    {
        $path = $this->getComposerPath();
        if (file_exists($path)) {
            @unlink($path);
        }
        parent::uninstall($event);
    }

    protected function downloadComposer()
    {
        $url = 'https://getcomposer.org/composer.phar';
        file_put_contents($this->getComposerPath(), file_get_contents($url));
    }

    protected function getComposerPath()
    {
        return 'composer.phar';
    }

}