<?php
/**
 * Controller with commands for modules migrations
 * Migration files lookup in directories of all included modules and in app directory
 */

namespace nullref\core\console;

use \yii\console\controllers\MigrateController;
use Yii;

class ModulesMigrateController extends MigrateController
{
    protected $migrationPaths = [];

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            foreach (Yii::$app->modules as $name => $module) {
                $module = Yii::$app->getModule($name);
                $this->migrationPaths[] = $module->getBasePath() . DIRECTORY_SEPARATOR . 'migrations';
            }
            $this->stdout('Migration files will be searched in folders: ' . PHP_EOL);
            $this->migrationPaths[] = $this->migrationPath;
            for ($i = 0; $i < count($this->migrationPaths); $i++) {
                $this->migrationPaths[$i] = Yii::getAlias($this->migrationPaths[$i]);
                $this->stdout(' - ' . $this->migrationPaths[$i] . PHP_EOL);
            }
            $this->stdout(PHP_EOL);
            return true;
        } else {
            return false;
        }
    }

    protected function createMigration($class)
    {
        foreach ($this->migrationPaths as $dirPath) {
            $file = $dirPath . DIRECTORY_SEPARATOR . $class . '.php';
            if (is_dir($dirPath) && (file_exists($dirPath . DIRECTORY_SEPARATOR . $class . '.php'))) {

                require_once($file);

                return new $class(['db' => $this->db]);
            }
        }
    }

    protected function getNewMigrations()
    {
        $applied = [];
        foreach ($this->getMigrationHistory(null) as $version => $time) {
            $applied[$version] = true;
        }
        $migrations = [];
        foreach ($this->migrationPaths as $dirPath) {
            if (is_dir($dirPath)) {
                $handle = opendir($dirPath);
                while (($file = readdir($handle)) !== false) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $path = $dirPath . DIRECTORY_SEPARATOR . $file;
                    if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && is_file($path) && !isset($applied[$matches[1]])) {
                        $migrations[] = $matches[1];
                    }
                }
                closedir($handle);
            }
        }
        sort($migrations);
        return $migrations;
    }


} 