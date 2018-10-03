<?php
/**
 *
 * User: develop
 * Date: 16.01.2018
 */

namespace mtest\fixtures;


use Codeception\Exception\ConfigurationException;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

trait TestTableCreator
{
    public function beforeLoad()
    {
        try {
                $this->getTableSchema();
        } catch (InvalidConfigException $exception) {
            $table = $this->getTableName();
            Yii::$app->db->createCommand()->createTable($table, $this->getColumns())->execute();
        }
        parent::beforeLoad();
    }

    public function unload()
    {
        $table = $this->getTableName();
        if ($s = Yii::$app->db->schema->getTableSchema($table, true)) {
            Yii::$app->db->createCommand()->dropTable($table)->execute();
        }
    }

    private function getTableName()
    {
        if (isset($this->modelClass)) {
            return $this->modelClass::tableName();
        }

        if (isset($this->tableName)) {
            return $this->tableName;
        }

        throw new ConfigurationException('Table name empty');
    }


    /** Поля существующей таблицы
     * @param $tableName
     * @param bool $needPk
     * @return array
     */
    protected function getSchemaColumns($tableName, $needPk = true)
    {
        $schema = \Yii::$app->db->getTableSchema($tableName);
        $columns = ArrayHelper::getColumn($schema->columns, 'type');

        if ($needPk) {
            $pk = reset($schema->primaryKey);
            $columns[$pk] = 'pk';
        }

        return $columns;
    }

    protected abstract function getColumns();


}