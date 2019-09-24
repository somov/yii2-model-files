<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 11.02.2018
 * Time: 21:44
 */

namespace mtest\models;


use yii\db\ActiveRecord;

/**
 * Class TitleModel
 *
 * @property string $model_id
 * @property string $title
 *
 */
class FileModel extends ActiveRecord
{

    public static function tableName()
    {
        return '_test_file_model_behavior';
    }

    public function rules()
    {
        return [
            [['model_id', 'title'], 'safe']
        ];
    }

}