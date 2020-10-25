<?php

namespace common\helpers;

use yii\data\ActiveDataProvider;
use yii\data\BaseDataProvider;

class DataProviderHelper
{
    /**
     * @param $query
     * @return ActiveDataProvider
     */
    public static function getInstance($query, $page = 0)
    {
        return new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'defaultPageSize' => 20,
                'page' => $page
            ],
            'sort'       => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
        ]);
    }

    /**
     * 统一格式的分页数据结构
     * @param BaseDataProvider $dataProvider
     * @return array
     */
    public static function page(BaseDataProvider $dataProvider)
    {
        return [
            'lists' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()->getPageCount(),
            'total' => $dataProvider->getTotalCount(),
        ];
    }
}