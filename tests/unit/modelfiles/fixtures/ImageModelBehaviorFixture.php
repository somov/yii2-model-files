<?php
/**
 *
 * User: develop
 * Date: 27.06.2018
 */

namespace mtest\fixtures;

use mtest\models\ImageModel;
use yii\test\ActiveFixture;

class ImageModelBehaviorFixture extends ActiveFixture
{
    use TestTableCreator;

    public $dataDirectory = '@mtest/fixtures/data';

    public $modelClass = ImageModel::class;

    protected function getColumns()
    {
        return [
            'model_id' => 'pk',
            'title' => 'varchar(50)'
        ];

    }
}