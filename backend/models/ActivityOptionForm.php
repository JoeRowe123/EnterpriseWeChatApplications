<?php
namespace backend\models;

use common\models\QuestionBankItem;
use yii\base\Model;

/**
 * Class ActivityOptionForm
 * @package backend\models
 */
class ActivityOptionForm extends Model
{
    public $name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '题目选项'
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function definedRule($attribute, $params)
    {
        if ($this->index == 1 || $this->index == 2)
        {
            if (!$this->name)
                $this->addError($attribute, "题目选项不能为空。");
            return;
        }
    }
}
