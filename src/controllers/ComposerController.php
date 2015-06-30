<?php

namespace nullref\core\controllers;

use Composer\Command\RequireCommand;
use Composer\Composer;
use Composer\Console\Application;
use Composer\Factory;
use Composer\IO\BufferIO;
use Composer\IO\NullIO;
use Composer\Package\PackageInterface;
use nullref\core\Module;
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
        $dir = Module::getRootDir();
        $path = $dir . '/composer.json';
        \Dotenv::setEnvironmentVariable('COMPOSER', $path);
        $factory = new Factory();
        return $factory->createComposer(new NullIO, $path, false, $dir);
    }

    /**
     * Show list of dependencies
     * @return string
     */
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

    /**
     *
     * @return string
     * @throws \Exception
     */
    public function actionInstall()
    {
        $composer = $this->getComposer();
        $cmd = new RequireCommand();

        $input = new ArrayInput(['packages' => ['rmrevin/yii2-fontawesome']]);
        $output = new BufferedOutput();
        $app = new Application();
        $io = new BufferIO();

        $composer->getConfig()->getRepositories();


        $cmd->setComposer($composer);
        $cmd->setApplication($app);
        $cmd->setIO($io);

        $result = '';
        if ($cmd->run($input, $output)) {
            $result = $output->fetch();
        }

        return $this->render('install', ['result' => $result]);
    }
}

