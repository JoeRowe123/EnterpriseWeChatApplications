<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/5 0005
 * Time: 14:20
 *
 */

namespace frontend\controllers;


use function AlibabaCloud\Client\json;
use backend\models\ArticleCommentSearch;
use common\models\Article;
use common\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Controller;

class ArticleCategoryController extends Controller
{
    public function actionCategory()
    {
        $ret = ["retCode"=>0,"retMsg"=>"","retData"=>""];
        $query = ArticleCategory::find();
        $pager = new Pagination();
        $pager->pageSize = 10;
        $pager->totalCount = $query->count();
        $ret["retData"] = $query->limit($pager->limit)->offset($pager->offset)->asArray()->all();
        //var_dump($ret["retData"]);die;
        return $ret;
    }



}