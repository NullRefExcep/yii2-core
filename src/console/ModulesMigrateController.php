<?php
/**
 * Controller with commands for modules migrations
 * Migration files lookup in directories of all included modules and in app directory
 */

namespace nullref\core\console;

use Yii;
use yii\console\Exception;
use yii\helpers\Console;
use yii\helpers\FileHelper;

class ModulesMigrateController extends \yii\console\controllers\MigrateController
{
    public $migrationPath = null;
    public $moduleId = null;
    protected $migrationPaths = [];

    protected $defaultMigrationPath = '@app/migrations';

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            in_array($actionID, ['up', 'down', 'create']) ? ['moduleId'] : []
        );
    }


    public function actionCreate($name)
    {
        if (!preg_match('/^\w+$/', $name)) {
            throw new Exception('The migration name should contain letters, digits and/or underscore characters only.');
        }

        $className = 'm' . gmdate('ymd_His') . '_' . $name;
        $file = $this->migrationPath . DIRECTORY_SEPARATOR . $className . '.php';
        FileHelper::createDirectory(dirname($file));
        if ($this->confirm("Create new migration '$file'?")) {
            $content = $this->generateMigrationSourceCode([
                'name' => $name,
                'className' => $className,
            ]);
            file_put_contents($file, $content);
            $this->stdout("New migration created successfully.\n", Console::FG_GREEN);
        }
    }

    public function beforeAction($action)
    {
        $oldPath = $this->migrationPath;

        $this->migrationPath = $this->defaultMigrationPath;

        if (parent::beforeAction($action)) {
            $this->stdout('Migration files will be searched in folders: ' . PHP_EOL);
            $this->migrationPath = $oldPath;

            if ($this->moduleId) {
                $this->migrationPath = $this->getMigrationPath($this->moduleId);
            }

            if ($this->migrationPath) {
                $this->migrationPaths[] = $this->migrationPath;
            } else {
                $this->migrationPaths[] = $this->defaultMigrationPath;
                foreach (Yii::$app->modules as $name => $module) {
                    $this->migrationPaths[] = $this->getMigrationPath($name);
                }
            }

            $this->migrationPaths = array_unique($this->migrationPaths);

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

    /**
     * @param $moduleId
     * @return string
     */
    protected function getMigrationPath($moduleId)
    {
        $module = Yii::$app->getModule($moduleId);
        return $module->getBasePath() . DIRECTORY_SEPARATOR . 'migrations';
    }

    public function actionDownModule($moduleId, $limit = 'all')
    {
        if ($limit === 'all') {
            $limit = null;
        } else {
            $limit = (int)$limit;
            if ($limit < 1) {
                throw new Exception('The step argument must be greater than 0.');
            }
        }

        $migrations = $this->getMigrationHistory($limit);
        $modulesMigration = $this->getModuleMigrations($moduleId);

        $filteredMigration = [];
        foreach ($migrations as $key => $migration) {
            if (isset($modulesMigration[$key])) {
                $filteredMigration[$key] = $migration;
            }
        }
        $migrations = $filteredMigration;
        if (empty($migrations)) {
            $this->stdout("No migration has been done before.\n", Console::FG_YELLOW);

            return self::EXIT_CODE_NORMAL;
        }

        $migrations = array_keys($migrations);

        $n = count($migrations);
        $this->stdout("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be reverted:\n", Console::FG_YELLOW);
        foreach ($migrations as $migration) {
            $this->stdout("\t$migration\n");
        }
        $this->stdout("\n");

        $reverted = 0;
        if ($this->confirm('Revert the above ' . ($n === 1 ? 'migration' : 'migrations') . '?')) {
            foreach ($migrations as $migration) {
                if (!$this->migrateDown($migration)) {
                    $this->stdout("\n$reverted from $n " . ($reverted === 1 ? 'migration was' : 'migrations were') . " reverted.\n", Console::FG_RED);
                    $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);

                    return self::EXIT_CODE_ERROR;
                }
                $reverted++;
            }
            $this->stdout("\n$n " . ($n === 1 ? 'migration was' : 'migrations were') . " reverted.\n", Console::FG_GREEN);
            $this->stdout("\nMigrated down successfully.\n", Console::FG_GREEN);
        }
    }

    protected function getMigrationHistory($limit)
    {
        if ($this->moduleId) {
            $module = Yii::$app->getModule($this->moduleId);
            if ($module === null) {
                throw new Exception('Module "' . $this->moduleId . '" not found');
            }

            $migrationHistory = parent::getMigrationHistory(false);

            $modulesMigration = $this->getModuleMigrations($this->moduleId);
            $filteredMigration = [];
            foreach ($migrationHistory as $key => $migration) {
                if (isset($modulesMigration[$key])) {
                    $filteredMigration[$key] = $migration;
                }
            }
            $migrationHistory = $filteredMigration;
        } else {

            $migrationHistory = parent::getMigrationHistory($limit);
        }
        return $migrationHistory;
    }

    protected function getModuleMigrations($moduleId, $applied = [])
    {
        $path = $this->getMigrationPath($moduleId);
        return $this->getMigrationsFromDir($path, $applied);
    }

    protected function getMigrationsFromDir($dirPath, $applied = [])
    {
        $migrations = [];
        if (is_dir($dirPath)) {
            $handle = opendir($dirPath);
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $dirPath . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && is_file($path) && !isset($applied[$matches[1]])) {
                    $migrations[$matches[1]] = $matches[1];
                }
            }
            closedir($handle);
        }
        return $migrations;
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
            $migrations = array_merge($migrations, $this->getMigrationsFromDir($dirPath, $applied));
        }
        sort($migrations);
        return $migrations;
    }
} 