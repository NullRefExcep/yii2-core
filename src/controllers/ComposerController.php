<?php

namespace nullref\core\controllers;

use Composer\Command\InstallCommand;
use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\PackageInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use yii\base\Controller;

/**
 * Manage composer dependencies
 */
class ComposerController extends Controller
{
    /**
     * @return Composer
     */
    protected function getComposer()
    {
        //@TODO make better implementation
        $dir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $path = $dir . '/composer.json';
        $factory = new Factory();
        return $factory->createComposer(new NullIO, $path, false, $dir);
    }

    public function actionIndex()
    {
        $composer = $this->getComposer();

        $repository = $composer->getRepositoryManager()->getLocalRepository();

        $dependencies = [];

        foreach ($repository->getCanonicalPackages() as $currentPackage) {
            /** @var PackageInterface $currentPackage */
            $dependencies[] = ['name' => $currentPackage->getPrettyName(), 'version' => $currentPackage->getPrettyVersion()];
        }

        return $this->render('index', ['dependencies' => $dependencies]);
    }

    public function actionInstall()
    {
        $composer = $this->getComposer();
        $cmd = new InstallCommand();

        $input = new ArrayInput(['packages' => ['rmrevin/yii2-fontawesome']]);

        $output = new BufferedOutput();
        $cmd->setComposer($composer);

        $result = '';
        if ($cmd->run($input, $output)) {
            $result = $output->fetch();
        }

        return $this->render('install', ['result' => $result]);
    }
}

