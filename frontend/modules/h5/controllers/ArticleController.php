<?php
/**
 * Created by PhpStorm.
 * User: dongxinyun
 * Date: 2019/8/15
 * Time: 下午3:06
 */

namespace frontend\modules\h5\controllers;

use common\helpers\DataProviderHelper;
use common\models\Article;
use yii\web\Controller;

class ArticleController extends Controller
{
    /**
     * 获取文章列表
     */
    public function actionList($limit,$type = null)
    {
        $query = Article::find();
        $query->where(['status' => 10]);
        $query->andFilterWhere(['type' => $type]);
        return $query->limit($limit)->all();
    }

    /**
     * 获取文章分页
     */
    public function actionPage($type, $page=0)
    {
        $query = Article::find();
        $query->where(['status' => 10]);
        $query->andFilterWhere(['type' => $type]);
        $query->with("articleComments")->asArray();
        return DataProviderHelper::page(DataProviderHelper::getInstance(
            $query,$page
        ));
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionXcgl()
    {
        return Article::find()->where(['title' => '长江三峡游轮选船攻略', 'status' => 10])->one();
    }

}