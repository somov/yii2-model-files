<?php
/**
 *
 * User: develop
 * Date: 21.11.2017
 */

namespace mtest;

use Codeception\TestCase\Test;
use mtest\fixtures\FileModelBehaviorFixture;
use mtest\models\FileModel;
use somov\mfiles\FileModelBehavior;
use yii\helpers\ArrayHelper;

class FileModelBehaviorTest extends Test
{

    const BC = [
        'class' => FileModelBehavior::class,
        'basePath' => '@ext',
        'fileTemplate' => "{dS}_data{dS}{model_id}{dS}{mS}{ext}"
    ];

    public function _fixtures()
    {
        return [
            FileModelBehaviorFixture::class
        ];
    }

    private function getFile(){
        return \Yii::getAlias('@ext/files/test-file1.jpg');
    }

    /**
     * @param array $behavior
     * @return FileModel
     */
    private function getModel(array $behavior = [])
    {
        $file = $this->getFile();

        $m = new FileModel();
        $m->attachBehavior('file', ArrayHelper::merge(self::BC, $behavior));
        $m->addFile([
            'first' => $file,
            'second' => $file
        ]);


        return $m;
    }

    public function testFileName()
    {
        $model = $this->getModel();
        $model->model_id = 1;
        $this->assertSame('/_data/1/suffix.jpg', $model->getFileName('jpg', 'suffix'));
        $this->assertContains('tests', $model->getFullFileName('jpg', 'suffix'));
    }

    public function testAddSingleFile()
    {

        /** @var FileModel $model */
        $model = $this->getModel([
            'fileTemplate' => "{dS}_data{dS}{pK}-{mS}{ext}"
        ]);

        $model->addFile($this->getFile(), 'default');

        $model->save();
        $f = $model->getFullFileName(null, 'default');
        $this->assertContains('tests/_data/'.$model->model_id.'-file-model.jpg', $f);
        $this->assertFileExists($f);

        $model->delete();

        $this->assertFileNotExists($model->getFullFileName());

    }


    public function testAddUpdate()
    {
        $model = FileModel::findOne(99);
        $model->attachBehavior('file', array_merge(self::BC, [
            'fileTemplate' => "{dS}_data{dS}{pK}-{mS}{ext}"
        ]));

        $f = $model->getFullFileName('jpg');
        $this->assertContains('_data/'.$model->model_id.'-file-model.jpg', $f);
        $this->assertFalse($model->isFileExists());

        $model->addFile($this->getFile());
        $model->save();

        $this->assertFileExists($f);

        $model->delete();

        $this->assertFileNotExists($model->getFullFileName());

    }


    public function testAddMultipleAddFiles()
    {

        /** @var FileModel $model */
        $model = $this->getModel([
            'fileTemplate' => "{dS}_data{dS}{pK}-{mS}{ext}"
        ]);
        $model->model_id = 10;
        $model->save();

        $f = $model->getFullFileName(null, 'first');
        $this->assertContains('tests/_data/10-first.jpg', $f);

        $this->assertFileExists($model->getFullFileName(null, 'first'));
        $this->assertFileExists($model->getFullFileName(null, 'second'));

        $model->delete();

        $this->assertFileNotExists($model->getFullFileName(null, 'first'));
        $this->assertFileNotExists($model->getFullFileName(null, 'second'));

    }


    public function testRemoveModelDirectory()
    {
        /** @var FileModel $model */
        $model = $this->getModel([
            'fileTemplate' => "{dS}_data{dS}{pK}{dS}{mS}{ext}",
            'canDeleteParentDir' => true
        ]);

        $model->save();

        $this->assertTrue($model->isFileExists(null, 'first'));
        $this->assertTrue($model->isFileExists(null, 'second'));

        $this->assertTrue($model->deleteFile(null, 'second'));

        $model->delete();

        $this->assertFileNotExists($model->getFullFileName(null, 'first'));
        $this->assertFileNotExists($model->getFullFileName(null, 'second'));

    }

    public function testFindFilesTemplates(){
        return [
            ['1', '{dS}_data{dS}{model_id}{dS}{mS}{ext}'],
            ['2', '{dS}_data{dS}{pK}-{title}-{mS}{ext}'],
            ['3', '{dS}_data{dS}{pK}-{mS}{ext}'],
            ['4', "{dS}_data{dS}{pK}{dS}{mS}{ext}"],
            ['5', '{dS}_data{dS}{mS}{model_id}{ext}'],
            ['6', '{dS}_data{dS}{mS}{model_id}-{title}{ext}']
        ];
    }

    /**
     *
     * @dataProvider testFindFilesTemplates
     * @param $id
     * @param $template
     */
    public function testFindFiles($id, $template){


        /** @var FileModel $model */
        $model = $this->getModel([
            'fileTemplate' => $template,
        ]);

        $model->model_id = $id;
        $model->title  = 'Test images';

        $model->save();

        $files = $model->findFiles();
        $this->assertArrayHasKey( 'first', $files);
        $this->assertArrayHasKey('second', $files);
        $this->assertCount(2, $files);

        $cnt = $model->deleteFiles();
        $this->assertEquals(2, $cnt);

    }

}