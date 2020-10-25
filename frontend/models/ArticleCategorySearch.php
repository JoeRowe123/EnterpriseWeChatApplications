<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/11 0011
 * Time: 14:36
 *
 */

namespace frontend\models;


use common\models\ArticleCategory;

class ArticleCategorySearch extends ArticleCategory
{
    public function getParents($pid,$tree = [])
    {
        $info = ArticleCategory::findOne(['id'=>$pid]);
        array_unshift( $tree,$info['name']);

        if ($info['p_id'] == 0)
        {
            return $info['name'];
        }

        array_unshift($tree,$this->getParents($info['p_id'], $tree));
        return $tree;
    }

}