<?php
/**
 *
 * User: develop
 * Date: 21.11.2017
 */

namespace somov\mfiles;

/**
 * Class ImageModelBehavior
 * @package app\components\behaviors
 */
class ImageModelBehavior extends FileModelBehavior implements ImageModelBehaviorInterface
{

    public $default = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    /**
     * @var callable
     */
    public $defaultCallback;

    /**
     * @var string
     */
    public $path = '';

    public $extension = 'jpg';

    /**
     * @var  array additional params to image url
     */
    public $params = [];


    public function getFileTemplate()
    {
        return $this->path . parent::getFileTemplate();
    }

    /**
     * @param string|null $suffix
     * @param array $params
     * @param bool $schema
     * @return string
     */
    public function getImageUrl($suffix = null, $params = [], $schema = false)
    {
        if ($this->isFileExists(null, $suffix)) {
            return $this->getFileUrl(null, $suffix, $schema, array_merge($this->params, $params));
        } else {
            if (isset($this->defaultCallback) && is_callable($this->defaultCallback)) {
                return call_user_func($this->defaultCallback);
            }
        }
        return $this->default;
    }

    /**
     * @param string|null $suffix
     * @param bool $schema
     * @return string
     */
    public function getImageUrlTime($suffix = null, $schema = false)
    {
        return $this->getImageUrl($suffix, ['t' => time()], $schema);
    }

    /**
     * @param string|null $suffix
     * @return string
     */
    public function getImageFile($suffix = null)
    {
        return $this->getFullFileName(null, $suffix);
    }

    /**
     * @param array $images
     * @param string $fileHandlerType
     * @throws \yii\base\Exception
     */
    public function saveImages(array $images, $fileHandlerType = self::FILE_HANDLER_TYPE_COPY)
    {
        $this->addFile($images, null, $fileHandlerType);
    }

    /**
     * @param string|null $suffix
     * @return boolean
     */
    public function imageExists($suffix = null)
    {
        return $this->isFileExists($this->extension, $suffix);
    }
}