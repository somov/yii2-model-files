<?php
/**
 *
 * User: develop
 * Date: 21.11.2017
 */

namespace mtest;

;

use Codeception\TestCase\Test;
use mtest\fixtures\ImageModelBehaviorFixture;
use mtest\models\ImageModel;
use somov\ImageModelBehavior;
use yii\base\InvalidConfigException;

class ImageModelBehaviorTest extends Test
{

    /** @var  ImageModel */
    private $model;

    public function _fixtures()
    {
        return [
            ImageModelBehaviorFixture::class
        ];
    }

    protected function setUp()
    {
        \Yii::setAlias('@webroot', '@app/web');

        \Yii::$app->urlManager->enablePrettyUrl = false;
        \Yii::$app->urlManager->showScriptName = true;

        $this->model = new ImageModel();
        $this->model->attachBehavior(
            'image', [
                'class' => ImageModelBehavior::class,
                'path' => '/media/video_tags'
            ]
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        $this->model->delete();
        parent::tearDown();
    }


    public function testImage()
    {

        $this->model->title = 'Test';
        $this->model->model_id = '9999';

        $this->model->saveImages([
            's' => \Yii::getAlias('@ext/files/test-file1.jpg'),
            'b' => 'https://i.stack.imgur.com/q2za0.jpg?s=32&g=1'
        ]);

        $this->model->save();

        $f = $this->model->getImageUrl('s');
        $this->assertContains('/media/video_tags/9999-s.jpg', $f);

        $f = $this->model->getImageUrl('notexists');
        $this->assertContains('data:image/gif;', $f);


    }


}