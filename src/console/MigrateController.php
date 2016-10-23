<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\console;


use nullref\core\interfaces\IHasMigrateNamespace;
use Yii;
use yii\base\Module;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class MigrateController extends \yii\console\controllers\MigrateController
{
    /** @var null|string */
    public $moduleId = null;

    /**
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
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if (count($this->migrationNamespaces) === 0) {
            if ($this->moduleId) {
                $namespaces = $this->getMigrationNamespace(Yii::$app->getModule($this->moduleId));
            } else {

                /** @var Module[] $modules */
                $modules = Yii::$app->getModules();
                $namespaces = [];
                foreach ($modules as $id => $module) {
                    $namespaces = array_merge($namespaces, $this->getMigrationNamespace(Yii::$app->getModule($id)));
                }
            }
            $this->migrationNamespaces = $namespaces;
        }
        return parent::beforeAction($action);
    }


    /**
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
     * @param int $limit
     * @return array
     */
    protected function getMigrationHistory($limit)
    {
        if ($this->moduleId === null) {
            return parent::getMigrationHistory($limit);
        }

        if ($this->db->schema->getTableSchema($this->migrationTable, true) === null) {
            $this->createMigrationHistoryTable();
        }
        $query = new Query();
        $rows = $query->select(['version', 'apply_time'])
            ->from($this->migrationTable)
            ->orderBy('apply_time DESC, version DESC')
            ->andFilterWhere(['like', 'version', $this->migrationNamespaces])
            ->limit($limit)
            ->createCommand($this->db)
            ->queryAll();
        $history = ArrayHelper::map($rows, 'version', 'apply_time');
        unset($history[self::BASE_MIGRATION]);

        return $history;
    }


}