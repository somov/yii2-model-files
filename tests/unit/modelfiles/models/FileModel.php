<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 11.02.2018
 * Time: 21:44
 */

namespace mtest\models;


use somov\mfiles\FileModelBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * Class TitleModel
 *
 * @property string $model_id
 * @property string $title
 *
 * @method string getFileName(string $extension = null, string|null $suffix)
 * @method string getFullFileName(string|null $extension = null, string $suffix = null)
 * @method ActiveRecord|Model addFile(array|string $file, string $suffix = null, string $fileHandlerType = FileModelBehavior::FILE_HANDLER_TYPE_COPY)
 * @method string getFileUrl(string $extension = null, string $suffix = null, boolean $shema = false)
 * @method bool deleteFile(string $extension = null, string  $suffix = null)
 * @method int|bool deleteFiles(string $mask = null)
 * @method findFiles(string $mask = null)
 * @method isFileExists(string $extension = null, string  $suffix = null, string &$file = null)
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