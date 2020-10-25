<?php

namespace common\behaviors;


use common\components\active\ActiveRecord;
use common\helpers\StringHelper;
use common\models\Log;
use yii\base\Behavior;

class LogBehavior extends Behavior
{
    public $attribute = [];

    public $callBack = [];

    public static $sn = '';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    public function init()
    {
        parent::init();
        $this::$sn = StringHelper::generateSn("log");
    }

    /**
     * @param $event
     */
    public function afterUpdate($event)
    {
        if ($event->name == ActiveRecord::EVENT_BEFORE_UPDATE && empty($this->owner->dirtyAttributes)) {
            return;
        }
        $changedAttribute = $event->changedAttributes;

        foreach ((array)$this->attribute as $item) {
            if (isset($changedAttribute[$item])) {
                $model = new Log();
                $model->table_name = ($this->owner)::tableName();
                $model->attr_name = $item;
                $model->before_value = (string)$changedAttribute[$item];
                $model->attr_after_value = (string)$this->owner->getAttribute($item);
                $model->sn = $this::$sn;
                $model->target_id = $this->owner->getPrimaryKey();
                $model->user_id = \Yii::$app->user->identity->getId();
                $model->user_name = \Yii::$app->user->identity->username;
                if (isset($this->callBack[$item])) {
                    $fun = $this->callBack[$item];
                    call_user_func($fun, $model, $model->before_value, $model->attr_after_value);
                }
                $model->save(false);
            }
        }
    }

}