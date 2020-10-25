<?php
namespace backend\models;

use common\models\QuestionBankItem;
use yii\base\Model;

/**
 * Class OptionForm
 * @package backend\models
 */
class OptionForm extends Model
{
    public $name;
    public $type;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'definedRule', 'skipOnEmpty' => false, 'skipOnError' => false],
            ['type', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '选项信息'
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function definedRule($attribute, $params)
    {
        if ($this->type == QuestionBankItem::TYPE_SINGLE || $this->type == QuestionBankItem::TYPE_MULTIPLE)
        {
            if ($this->name != 0 && !$this->name) {
                $this->addError($attribute, "选项信息不能为空1。");
                return;
            }

        }
    }
}
