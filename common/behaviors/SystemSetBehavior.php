<?php

namespace common\behaviors;


use common\components\active\ActiveRecord;
use common\models\SystemSet;
use yii\base\Behavior;

class SystemSetBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert'
        ];
    }

    /**
     * @param $event
     */
    public function afterUpdate($event)
    {
        $this->setCache();
    }

    /**
     * @param $event
     */
    public function afterDelete($event)
    {
        $this->setCache();
    }

    /**
     * @param $event
     */
    public function afterInsert($event)
    {
        $this->setCache();
    }

    /**
     * 设置缓存
     */
    public function setCache()
    {
        $cacheKey = SystemSet::$cacheKey;
        $data = \Yii::$app->cache->get($cacheKey);
        if($data) {
            \Yii::$app->cache->delete($cacheKey);
            $data = SystemSet::find()->asArray()->one();
            if($data) {
                \Yii::$app->cache->set($cacheKey, json_encode($data));
            }

        }
    }

}