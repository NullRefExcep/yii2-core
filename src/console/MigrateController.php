<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\console;


use nullref\core\interfaces\IHasMigrateNamespace;
use Yii;
use yii\base\Module;

class MigrateController extends \yii\console\controllers\MigrateController
{
    /**
     *
     */
    public function init()
    {
        parent::init();

        if (count($this->migrationNamespaces) === 0) {
            /** @var Module[] $modules */
            $modules = Yii::$app->getModules();
            $namespaces = [];
            foreach ($modules as $id => $module) {
                $namespaces = array_merge($namespaces, $this->getMigrationNamespace(Yii::$app->getModule($id)));
            }
            $this->migrationNamespaces = $namespaces;
        }
    }

    /**
     * @param $module Module
     * @return array
     */
    protected function getMigrationNamespace($module)
    {
        $reflection = new \ReflectionClass($module->className());
        $baseNamespace = $reflection->getNamespaceName() . '\\migrations';
        $namespaces = [$baseNamespace];

        $path = $module->getBasePath() . DIRECTORY_SEPARATOR . 'migrations';

        if (file_exists($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                    $namespaces[] = $baseNamespace . '\\' . $file;
                }
            }
        }
        $namespaces = array_unique($namespaces);

        if ($module instanceof IHasMigrateNamespace) {
            return $module->getMigrationNamespaces($namespaces);
        }

        return $namespaces;
    }
}