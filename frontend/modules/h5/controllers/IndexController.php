<?php

namespace frontend\modules\h5\controllers;

use common\helpers\DataProviderHelper;
use common\helpers\MoneyHelper;
use common\helpers\StringHelper;
use common\models\City;
use common\models\DepartureCity;
use common\models\Goods;
use common\models\Order;
use common\models\TravelLine;
use frontend\models\GoodsSearch;
use yii\web\Controller;

class IndexController extends Controller
{

    /**
     * 船期表
     */
    public function actionGoodsTravelLineAndDate()
    {
        $lineId = \Yii::$app->request->body('travelLineId');
        $date = \Yii::$app->request->body('date');
        $status = Goods::STATUS_ACTIVE;
        $connection= \Yii::$app->getDb();
        $sql = <<<SQL
SELECT goods.id as goods_id,brand.name as title,min(goods_price.price) price,goods_price.date FROM `goods` 
LEFT JOIN `goods_price` ON `goods`.`id` = `goods_price`.`goods_id` 
LEFT JOIN `brand` ON `goods`.`brand_id` = `brand`.`id` 
WHERE (`goods`.`status`=$status) 
AND (`travel_line_id`=$lineId) 
AND (`goods`.`goods_type`='cpyd') 
AND (DATE_FORMAT(`goods_price`.`date`,"%Y-%m-%d")='$date') 
group by goods_id,title,goods_price.date
SQL;
        $command = $connection->createCommand($sql);
        $datas = $command->queryAll();
        foreach ($datas as &$data) {
            $data['price'] = MoneyHelper::f2y($data['price']);
        }
        return $datas;
    }

    /**
     * 订单实时信息 累计出游+最新订单信息
     * fixme 数据量大了 这里会很慢
     * @return array
     */
    public function actionRealInfo()
    {
        $order = Order::find()->orderBy('id desc')->one();
        return [
            'count' => Order::find()->count(1),
            'info' => [
                'phoneNumber' => StringHelper::formatPhoneNumber($order->member_phone ?? ''),
                'title' => $order->goods_title,
                'date'  => $order->created_at
            ]
        ];
    }

    /**
     * 出发游三峡
     * @param $cityName
     * @param $limit
     * @return array
     */
    public function actionListByCity($cityName,$limit)
    {
        $city = City::find()->where(['like', 'name', $cityName])->one();
        $goodsId = [];
        if($city) {
            $departureCities = DepartureCity::findAll(['city_id' => $city->id]);
            foreach ($departureCities as $departureCity) {
                $goodsId[] = $departureCity->goods_id;
            }
        }
        $goods =  Goods::find()
            ->where(['id' => $goodsId, 'goods_type' => Goods::GOODS_CATEGORY_GTLY])
            ->orderBy('sort desc')
            ->limit($limit)
            ->all();
        $outData = [];
        foreach ($goods as $item) {
            $outData[] =  [
                'id' => $item->id,
                'title' => $item->title,
                'image' => $item->image[0],
                'images' => $item->images,
                'desc' => $item->desc,
                'tag' => $item->tag,
                'travelLineName' => $item->travelLine->name ?? '',
                'minxPrice' => MoneyHelper::f2y($item->min_price),
                'dateRange' => [
                    'begin_at' => $item->begin_at,
                    'end_at' => $item->end_at,
                ],
            ];
        }
        return $outData;
    }

    /**
     * 查询商品接口
     */
    public function actionPage()
    {
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->body());
        $datas = $dataProvider->getModels();
        foreach ($datas as &$model) {
            $model['min_price'] = MoneyHelper::f2y($model['min_price']);
            $model['max_price'] = MoneyHelper::f2y($model['max_price']);
            $model['tag'] = json_decode($model['tag'], true);
        }

        return [
            'lists' => $datas,
            'pages' => $dataProvider->getPagination()->getPageCount(),
            'total' => $dataProvider->getTotalCount(),
        ];
    }
}
