<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 11.02.2018
 * Time: 21:44
 */

namespace mtest\models;
use somov\mfiles\ImageModelBehavior;


/**
 *
 *
 * @method string getImageUrl(string $suffix = null,  $params = [], $schema = false)
 * @method string getImageUrlTime(string $suffix = null,  $schema = false)
 * @method string getImageFile(string $suffix = null)
 * @method array saveImages(array $images, string $fileHandlerType = ImageModelBehavior::FILE_HANDLER_TYPE_COPY)
 */
class ImageModel extends FileModel
{

    public static function tableName()
    {
        return '_test_image_behavior';
    }

    public function rules()
    {
        return [
            [['model_id', 'title'], 'safe']
        ];
    }



}