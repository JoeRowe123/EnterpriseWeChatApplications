<?php

namespace common\helpers;


use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\web\BadRequestHttpException;

class ActiveFormHelper
{

    private static $parseValidateResultString;

    /**
     * 解析activeForm的验证结果,抛出异常
     * @param Model ...$models
     * @return string
     * @throws BadRequestHttpException
     */
    public static function parseValidateResultException(Model ...$models): void
    {
        $result = [];
        foreach ($models as $item) {
            if (!empty(($re = ActiveForm::validate($item)))) {
                $result[] = $re;
            }
        }
        if (!empty($result)) {
            static::doParseValidateResult($result);
            throw new BadRequestHttpException(self::$parseValidateResultString);
        }
    }


    /**
     * 解析activeForm的验证结果,把数组翻译成字符串
     * @param Model ...$models
     * @return string
     */
    public static function parseValidateResult(Model ...$models): string
    {
        $result = [];
        foreach ($models as $item) {
            if (!empty(($re = ActiveForm::validate($item)))) {
                $result[] = $re;
            }
        }
        if (empty($result)) {
            return '';
        } else {
            static::doParseValidateResult($result);
            return self::$parseValidateResultString;
        }
    }

    /**
     * 递归处理字符串
     * @param array $array
     * @return mixed
     */
    protected static function doParseValidateResult(array $array): void
    {
        foreach ($array as $item) {
            if (is_array($item)) {
                self::$parseValidateResultString .= static::doParseValidateResult($item);
            } else {
                self::$parseValidateResultString .= $item;
            }
        }
    }
}