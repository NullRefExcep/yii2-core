<?php

namespace nullref\core\controllers;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\PackageInterface;
use yii\base\Controller;

/**
 *
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

        $content = '<h1>'
            . $composer->getPackage()->getName()
            . '</h1><table>';
        foreach ($repository->getCanonicalPackages() as $currentPackage) {
            /** @var PackageInterface $currentPackage */
            $content .= '<tr><td>'
                . $currentPackage->getPrettyName()
                . '</td><td>'
                . $currentPackage->getPrettyVersion() . '</td></tr>';
        }
        $content .= '</table>';
        return $this->render('index', ['content' => $content]);
    }
}

