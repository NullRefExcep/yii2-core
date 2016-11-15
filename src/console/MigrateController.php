<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\console;


use nullref\core\interfaces\IHasMigrateNamespace;
use Yii;
use yii\base\Exception;
use yii\base\Module;
use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class MigrateController extends BaseMigrateController
{
    /** @var null|string */
    public $moduleId = null;

    /**
     * Add param `moduleId` for actions: `up`, `down` and `create`
     * @param string $actionID
     * @return array
     */
    public function options($actionID)
    {
        $array_merge = array_merge(
            parent::options($actionID),
            in_array($actionID, ['up', 'down', 'create']) ? ['moduleId'] : []
        );
        return $array_merge;
    }

    /**
     * Set `migrationNamespaces` if it empty
     * @param \yii\base\Action $action
     * @return bool
     * @throws Exception
     */
    public function beforeAction($action)
    {
        if (count($this->migrationNamespaces) === 0) {
            if ($this->moduleId) {
                $namespaces = $this->getMigrationNamespace(Yii::$app->getModule($this->moduleId));
                if (count($namespaces) === 0) {
                    throw new Exception('Can\'t find any one migration namespace');
                }
            } else {
                /** @var Module[] $modules */
                $modules = Yii::$app->getModules();
                $namespaces = ['app\migrations'];
                foreach ($modules as $id => $module) {
                    $namespaces = array_merge($namespaces, $this->getMigrationNamespace(Yii::$app->getModule($id)));
                }
            }
            $this->migrationNamespaces = $namespaces;
        }
        return parent::beforeAction($action);
    }


    /**
     * Get all possible namespace of migration by module
     * @param $module Module
     * @return array
     */
    public function getMigrationNamespace($module)
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

    /**
     * @param string $name
     */
    public function actionCreate($name)
    {
        if (!$this->nameHasNamespace($name)) {
            $migrationNamespaces = $this->migrationNamespaces;
            $namespace = array_shift($migrationNamespaces);
            $name = $namespace . '\\' . $name;
        }
        parent::actionCreate($name);
    }

    /**
     * @param $name
     * @return bool
     */
    protected function nameHasNamespace($name)
    {
        $namespaces = $this->migrationNamespaces;
        foreach ($namespaces as $namespace) {
            if (strpos($name, $namespace) === 0) {
                $file = substr($name, strlen($namespace) + 1);
                if (preg_match('/M[0-9]{12}\w+/', $file, $matches)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Return migrations only with namespace
     * @param int $limit
     * @return array
     */
    protected function getMigrationHistory($limit)
    {
        if ($this->db->schema->getTableSchema($this->migrationTable, true) === null) {
            $this->createMigrationHistoryTable();
        }
        $query = new Query();
        $rows = $query->select(['version', 'apply_time'])
            ->from($this->migrationTable)
            ->orderBy('apply_time DESC, version DESC')
            ->createCommand($this->db)
            ->queryAll();

        $history = ArrayHelper::map($rows, 'version', 'apply_time');

        unset($history[self::BASE_MIGRATION]);

        $filteredHistory = [];
        /** Get migrations only with namespaces **/
        foreach ($history as $key => $timestamp) {
            if ($this->nameHasNamespace($key)) {
                $filteredHistory[$key] = $timestamp;
                if (count($filteredHistory) >= $limit) {
                    break;
                }
            }
        }

        return $filteredHistory;
    }

    /**
     * Filter migrations only with namespace
     * @return array
     */
    protected function getNewMigrations()
    {
        $migrations = parent::getNewMigrations();
        $migrations = array_filter($migrations, [$this, 'nameHasNamespace']);

        return $migrations;
    }
}
