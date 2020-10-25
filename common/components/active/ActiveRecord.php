<?php

namespace common\components\active;


use yii\web\BadRequestHttpException;

/**
 * Created by PhpStorm.
 * User: huy ang
 * Date: 2018/2/5
 * Time: 上午10:49
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @param bool $runValidation
     * @param null $attributeNames
     *
     * @return $this
     * @throws BadRequestHttpException
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $result = parent::save($runValidation, $attributeNames);
        if ($result === true) {
            return $this;
        } else {
            throw new BadRequestHttpException(current($this->getFirstErrors()));
        }
    }


}