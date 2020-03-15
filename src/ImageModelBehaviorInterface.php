<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 30.01.20
 * Time: 20:43
 */

namespace somov\mfiles;

/**
 * Interface ImageModelBehaviorInterface
 * @package somov\mfiles
 * @method string getImageUrl($suffix = null, $params = [], $schema = false);
 * @method string getImageFile($suffix = null)
 * @method saveImages(array $images, $fileHandlerType = FileModelBehaviorInterface::FILE_HANDLER_TYPE_COPY)
 * @method boolean imageExists(string $suffix = null)
 *
 */
interface ImageModelBehaviorInterface extends FileModelBehaviorInterface
{


}