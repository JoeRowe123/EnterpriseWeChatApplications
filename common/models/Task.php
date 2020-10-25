<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property string $id
 * @property string $sn 任务编号
 * @property string $name 任务名称
 * @property string $status
 * @property string $msg 提示
 * @property int $created_at
 * @property int $updated_at
 */
class Task extends \yii\db\ActiveRecord
{
    public static $status = [
        "wait" => "待执行",
        "process" => "执行中",
        "success" => "执行成功",
        "error" => "执行失败",
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'msg'], 'string'],
            [['created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['sn', 'name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => '任务编号',
            'name' => '任务名称',
            'status' => '任务状态',
            'msg' => '任务提示',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
