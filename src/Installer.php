<?php

namespace nullref\core;


use nullref\core\components\ModuleInstaller;

class Installer extends ModuleInstaller
{
    public $moduleId = 'core';

    public function getModuleId()
    {
        return 'core';
    }

    public function install()
    {
        $this->downloadComposer();
        parent::install();
    }

    public function uninstall()
    {
        $path = $this->getComposerPath();
        if (file_exists($path)) {
            @unlink($path);
        }
        parent::uninstall();
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