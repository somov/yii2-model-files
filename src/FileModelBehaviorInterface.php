<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 24.09.19
 * Time: 15:10
 */

namespace somov\mfiles;

use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * Class FileModelBehaviorInterface
 * @package somov\mfiles
 *
 * @method integer deleteFiles($mask = null)
 * @method array findFiles($mask = null)
 * @method boolean deleteFile($extension = null, $suffix = null)
 * @method string getFileName($extension = null, $suffix = null)
 * @method string getFileUrl($extension = null, $suffix = null, $scheme = false, $params = [])
 * @method string getFullFileName($extension = null, $suffix = null)
 * @method boolean isFileExists($extension = null, $suffix = null, &$file = null)
 * @method Model|ActiveRecord addFile(string|array $files, $suffix = null, $fileHandlerType = FileModelBehaviorInterface::FILE_HANDLER_TYPE_COPY)
 */
interface FileModelBehaviorInterface
{

    const FILE_HANDLER_TYPE_COPY = 'copyFile';

    const FILE_HANDLER_TYPE_MOVE = 'moveFile';

    const FILE_HANDLER_TYPE_LINK = 'linkFile';
}