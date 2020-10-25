<?php

namespace frontend\modules\web\controllers;

use common\helpers\MoneyHelper;
use common\helpers\StringHelper;
use common\models\DepartureCity;
use common\models\Goods;
use common\models\Order;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class IndexController extends Controller
{


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

    public function actionListByCity($cityId,$limit)
    {
        $departureCities = DepartureCity::findAll(['city_id' => $cityId]);
        $goodsId = [];
        foreach ($departureCities as $departureCity) {
            $goodsId[] = $departureCity->goods_id;
        }
        $goods =  Goods::find()
            ->where(['id' => $goodsId, 'goods_type' => Goods::GOODS_CATEGORY_CPYD])
            ->orderBy('sort desc')
            ->limit($limit)
            ->all();
        $outData = [];
        foreach ($goods as $item) {
            $outData[] =  [
                'title' => $item->title,
                'image' => $item->image[0],
                'images' => $item->images,
                'desc' => $item->desc,
                'travelLineName' => $item->travelLine->name ?? '',
                'minxPrice' => $item->min_price,
                'dateRange' => [
                    'begin_at' => $item->begin_at,
                    'end_at' => $item->end_at,
                ],
            ];
        }
        return $outData;
    }

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
AND (DATE_FORMAT(`goods_price`.`date`,"%Y-%m")='$date') 
group by goods_id,title,goods_price.date
SQL;
        $command = $connection->createCommand($sql);
        $datas = $command->queryAll();
        foreach ($datas as &$data) {
            $data['price'] = MoneyHelper::f2y($data['price']);
        }
        return ArrayHelper::index($datas,null,'date');
    }


}
