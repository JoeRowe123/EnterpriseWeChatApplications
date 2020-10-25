<?php

namespace backend\controllers;


use common\models\FileModel;
use yii\web\Controller;
use yii\web\UploadedFile;

class FileController extends Controller
{

    public $enableCsrfValidation = false;


    public function actions()
    {
        return [
            'uedit-upload' => [
                'class'  => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => \Yii::$app->params['cos']['prefix'],//图片访问路径前缀
                    "imagePathFormat" => "/upload/ueditor/{yyyy}{mm}{dd}/{time}{rand:6}", //上传保存路径
                    "videoUrlPrefix"  => \Yii::$app->params['cos']['prefix'],//视频访问路径前缀
                ],
            ]
        ];
    }


    public function actionUpload()
    {
        \Yii::$app->response->format = 'json';
        \Yii::$app->response->getHeaders()->set('Access-Control-Allow-Origin',"*");
        if (\Yii::$app->request->isPost) {
            $model = new FileModel();
            $model->file = UploadedFile::getInstanceByName('file');
            try{
                $res = $model->upload();
                return [
                    'code' => 0,
                    'url'  => $res['url'],
                    'attachment'  => $res['url'],
                ];
            }catch (\Exception  $e) {
                return [
                    'code' => 1,
                    'msg'  => $e->getMessage()
                ];
            }
        }else{
            return [
                'code' => 1,
                'msg'  => '未知错误'
            ];
        }
    }


    /**
     *  FileInput上传图片
     * @return bool|false|string
     */
    public function actionUpload2()
    {
        if (Yii::$app->request->isAjax){
            $m_id = Yii::$app->request->post('m_id');
            $img_count = MissionImg::find()->where(['m_id' => $m_id])->count();
            if ($img_count >= 3) {
                return json_encode(['error' => '最多上传3张图片']);
            }
            $file = UploadedFile::getInstancesByName("Mission[img]");
            if (!in_array(strtolower($file[0]->extension), array('gif', 'jpg', 'jpeg', 'png'))) {
                $res = ['error' => '请上传标准图片文件, 支持gif,jpg,png和jpeg.'];
                return json_encode($res);
            }
            $dir = 'img/mission_img/';
            $file_name = date('Ymd') . uniqid();
            $database_url = $dir . $file_name . '.' .$file[0]->extension;
            $file[0]->saveAs($database_url);
            // 保存图片路径
            $img_model = new MissionImg();
            $img_model->url = '/' . $database_url;
            $img_model->m_id = $m_id;
            $img_model->cr_time = time();
            $img_model->save(false);
            return true;
        }
    }
}