<?php

namespace common\models;

use Exception;
use yii\base\Model;

class FileModel extends Model
{

    public $file;
    /**
     * @var string 保存路径
     */
    public $savePath;


    private $randomPath;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false],
        ];
    }


    public function init()
    {
        $this->randomPath = DIRECTORY_SEPARATOR . date("Ymd") . DIRECTORY_SEPARATOR;
        $path = \Yii::getAlias("@backend/web/upload") . $this->randomPath;
        if (!is_dir($path)) {
            mkdir($path);
            chmod($path, 0777);
        }
        $this->savePath = $path;
    }


    /**
     * @throws \yii\base\Exception
     */
    public function upload()
    {
        if ($this->validate()) {
            $name = $this->getFileName();
            $this->file->saveAs($this->savePath . $name);
            return [
                "url" => \Yii::$app->params['cos']['prefix'] . '/upload' . $this->randomPath . $name,
                "attachment" => '/upload' . $this->randomPath . $name,
            ];
        } else {
            throw new Exception(current($this->getFirstErrors()));
        }
    }

    /**
     * @param int $count
     *
     * @return string
     */
    public function getFileName($count = 0)
    {
        if ($count == 0) {
            $fileName = $this->file->baseName . '.' . $this->file->extension;
        } else {
            $fileName = $this->file->baseName . "($count)" . '.' . $this->file->extension;
        }
        if (file_exists($this->savePath . $fileName)) {
            $fileName = $this->getFileName(++$count);
        }

        return $fileName;
    }


}