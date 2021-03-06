<?php
/**
 *
 * User: develop
 * Date: 27.06.2018
 */

namespace somov\mfiles;


use yii\base\Behavior;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\UrlManager;

/**
 * Class FileModelBehavior
 * @package app\components\behaviors
 * @property string fileTemplate
 * @property-write $attachFile
 * @property array attachedFiles
 */
class FileModelBehavior extends Behavior implements FileModelBehaviorInterface
{

    /**
     * @var Model
     */
    public $owner;

    /** File extension
     * @var string
     */
    public $extension = '';

    /** File suffix
     * @var
     */
    public $modelSuffix = 'default';

    /**
     * @var string
     */
    public $basePath = '@webroot';

    /**
     * @var string
     */
    public $baseUrl = '@web';


    /** Может лм поведение удалять родительскую директорию файлов
     * @var bool
     */
    public $canDeleteParentDir = false;

    public $urlManagerComponentId = 'urlManager';

    /** Шаблон для генерации имени файла
     * @var string
     */
    private $_fileTemplate = "{dS}{pk}-{modelSuffix}{ext}";

    /**
     * @var UrlManager|null
     */
    private static $_urlManager = null;

    /**
     * @var array
     */
    private $_attachedFiles = [];


    /**
     * @return UrlManager|null
     */
    protected function getUrlManager()
    {
        if (isset(self::$_urlManager)) {
            return self::$_urlManager;
        }
        $manager = \Yii::$app->{$this->urlManagerComponentId};
        $properties = ArrayHelper::toArray($manager, [
            $manager::className() => ['hostInfo', 'scriptUrl', 'baseUrl']
        ], false);

        self::$_urlManager = new UrlManager(array_merge($properties, [
            'showScriptName' => false,
            'enablePrettyUrl' => true,
        ]));

        return self::$_urlManager;
    }

    /**
     * @return string
     */
    private function getDefaultSuffix()
    {
        return Inflector::camel2id(StringHelper::basename(get_class($this->owner)));
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => [$this, '_afterDelete']
        ];
    }

    /**
     * After delete ActiveRecord
     *
     * @access private
     */
    public function _afterDelete()
    {
        $this->deleteFiles();
    }


    /**
     * @param null $mask
     * @return int|boolean count deleted files or true on deleted parent folder
     * @throws \yii\base\ErrorException
     */
    public function deleteFiles($mask = null)
    {
        $count = 0;

        if ($this->canDeleteParentDir) {
            FileHelper::removeDirectory(dirname($this->getFullFileName()));
            return true;
        }

        $mask = (isset($mask)) ? $mask : $this->getSearchFilesMask();

        foreach ($this->findFiles($mask) as $file) {
            if (unlink($file)) {
                $count++;
            }
        }

        return $count;
    }

    /** Формирование маски поиска файлов модели
     * @return string
     */
    protected function getSearchFilesMask()
    {
        $oldExtension = $this->extension;
        $oldSuffix = $this->modelSuffix;

        $mask = $this->getFileName('*', '*');

        $this->modelSuffix = $oldSuffix;
        $this->extension = $oldExtension;

        return $mask;
    }

    /**
     * @param string|null $mask
     * @return array
     */
    public function findFiles($mask = null)
    {
        $mask = (isset($mask)) ? $mask : $this->getSearchFilesMask();

        $files = [];
        $dir = \Yii::getAlias($this->basePath) . dirname($mask);

        if (!is_dir($dir)) {
            return [];
        }

        foreach (FileHelper::findFiles($dir, [
            'only' => ['pattern' => basename($mask)]
        ]) as $file) {
            $pattern = preg_replace('/_replace/', '(.*?)', strtr($mask, ['*' => '_replace', '/' => '\/', '.' => '\.']));
            if (preg_match('/' . $pattern . '/', $file, $m)) {
                $files[isset($m[1]) ? basename($m[1]) : null] = $file;
            } else {
                $files[] = $file;
            }
        }
        return $files;
    }

    /**
     * @param string|null $extension
     * @param string|null $suffix
     * @return bool
     */
    public function deleteFile($extension = null, $suffix = null)
    {
        if ($this->isFileExists($extension, $suffix, $file)) {
            return unlink($file);
        }
        return false;
    }


    /** Get primary key
     * @return string|null
     */
    protected function getPk()
    {
        if ($this->owner instanceof ActiveRecord) {
            return is_array($this->owner->primaryKey) ? implode('-', $this->owner->primaryKey)
                : $this->owner->primaryKey;
        }
        // возвращаем первый  атрибут модели
        if ($this->owner instanceof Model) {
            $attributes = $this->owner->attributes;
            if (count($attributes) > 0) {
                return reset($attributes);
            }
        }
        return null;
    }

    /** Get Directory separator
     * @return string
     */
    protected function getDs()
    {
        return DIRECTORY_SEPARATOR;
    }

    /** File extension
     * @return string
     */
    protected function getExt()
    {
        return !empty($this->extension) ? '.' . $this->extension : '';
    }

    /** Get model suffix
     * @return string
     */
    protected function getMs()
    {
        if ($this->modelSuffix == 'default') {
            return $this->getDefaultSuffix();
        }
        return $this->modelSuffix;
    }

    /**
     * @return string
     */
    public function getFileTemplate()
    {
        return $this->_fileTemplate;
    }

    /**
     * @param string $fileTemplate
     */
    public function setFileTemplate($fileTemplate)
    {
        $this->_fileTemplate = $fileTemplate;
    }


    /** Генерирует имя файла из шаблона
     * @return string
     */
    private function resolveFileTemplate()
    {
        $owner = $this->owner;
        $file = preg_replace_callback("/\{([a-zA-z-]+)\}/", function ($matches) use ($owner) {
            $property = $matches[1];
            return $owner->{$property};
        }, $this->fileTemplate);
        return $file;
    }

    /** Генерировать имя файлы
     * @param string|null $extension
     * @param null|string $suffix
     * @return string
     */
    public function getFileName($extension = null, $suffix = null)
    {
        if (isset($extension)) {
            $this->extension = $extension;
        }

        if (isset($suffix)) {
            $this->modelSuffix = $suffix;
        }

        return $this->resolveFileTemplate();
    }

    /**
     * @param string|null $extension
     * @param string|null $suffix
     * @param bool $scheme
     * @param array $params
     * @return string
     */
    public function getFileUrl($extension = null, $suffix = null, $scheme = false, $params = [])
    {
        Url::$urlManager = $this->getUrlManager();

        $url = Url::to([
                \Yii::getAlias(
                    $this->baseUrl . $this->getFileName($extension, $suffix))
            ] + $params,
            $scheme
        );
        Url::$urlManager = null;

        return $url;
    }

    /**
     * @param string|null $extension
     * @param string|null $suffix
     * @return string
     */
    public function getFullFileName($extension = null, $suffix = null)
    {
        return \Yii::getAlias($this->basePath) . $this->getFileName($extension, $suffix);
    }

    /**
     * @param string|null $extension
     * @param string|null $suffix
     * @param string|null $file
     * @return bool
     */
    public function isFileExists($extension = null, $suffix = null, &$file = null)
    {
        $file = $this->getFullFileName($extension, $suffix);
        return file_exists($file);
    }

    /**
     * @param string|array $files
     * @param string|null $suffix
     * @param string $fileHandlerType
     * @throws Exception
     */
    private function saveFiles($files, $suffix = null, $fileHandlerType = self::FILE_HANDLER_TYPE_COPY)
    {
        $sourceFile = null;

        if (is_array($files)) {
            foreach ($files as $suffix => $file) {
                $this->saveFiles($file, $suffix, $fileHandlerType);
            }
            return;
        } else {
            $sourceFile = $files;
        }

        $info = pathinfo($sourceFile);

        if (!isset($info['extension'])) {
            throw new Exception($sourceFile . ' - File without extension not supported');
        }

        $destinationFile = $this->getFullFileName(strtok($info['extension'], '?'), $suffix);

        $path = dirname($destinationFile);

        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }

        if (!$u = parse_url($sourceFile)) {
            throw new  Exception('Error parse image source ' . $sourceFile);
        }

        if (isset($u['host'])) {
            file_put_contents($destinationFile, file_get_contents($sourceFile));
        } else {

            if (!file_exists($sourceFile)) {
                throw new Exception('File not found ' . $sourceFile);
            }

            switch ($fileHandlerType) {
                case self::FILE_HANDLER_TYPE_COPY :
                    copy($sourceFile, $destinationFile);
                    break;
                case self::FILE_HANDLER_TYPE_MOVE :
                    rename($sourceFile, $destinationFile);
                    break;
                case self::FILE_HANDLER_TYPE_LINK :
                    link($sourceFile, $destinationFile);
                    break;
            }
        }
    }

    /**
     * @param string|array $files
     * @param string|null $suffix
     * @param string $fileHandlerType
     * @return Model|ActiveRecord
     * @throws Exception
     */
    public function addFile($files, $suffix = null, $fileHandlerType = self::FILE_HANDLER_TYPE_COPY)
    {
        if ($this->owner instanceof ActiveRecord) {
            /** @var ActiveRecord|self $behavior */
            $behavior = $this;
            $this->owner->on((($this->owner->isNewRecord) ? ActiveRecord::EVENT_AFTER_INSERT : ActiveRecord::EVENT_AFTER_UPDATE),
                function () use ($behavior, $files, $suffix, $fileHandlerType) {
                    $behavior->saveFiles($files, $suffix, $fileHandlerType);
                });
        } else {
            $this->saveFiles($files, $suffix, $fileHandlerType);
        }
        return $this->owner;
    }

    /**
     * @param string $file
     * @throws Exception
     */
    public function setAttachFile($file)
    {
        $this->addFile($file);
    }

    /**
     * @return array
     */
    public function getAttachedFiles()
    {
        return array_merge($this->_attachedFiles, $this->findFiles());
    }

    /**
     * @param array $attachedFiles
     * @throws Exception
     */
    public function setAttachedFiles($attachedFiles)
    {
        if (!empty($this->_attachedFiles)) {
            return;
        }
        $this->_attachedFiles = $attachedFiles;
        $this->addFile($attachedFiles);
    }


}