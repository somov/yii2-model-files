<?php
/**
 *
 * User: develop
 * Date: 27.06.2018
 */

namespace mtest\fixtures;

use mtest\models\FileModel;
use yii\test\ActiveFixture;

class FileModelBehaviorFixture extends ActiveFixture
{
    use TestTableCreator;

    public $dataDirectory = '@mtest/fixtures/data';

    public $modelClass = FileModel::class;

    protected function getColumns()
    {
        return [
            'model_id' => 'pk',
            'title' => 'varchar(50)'
        ];

    }
}