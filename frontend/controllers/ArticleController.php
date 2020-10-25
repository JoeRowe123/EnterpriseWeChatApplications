<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/8 0008
 * Time: 10:01
 *
 */

namespace frontend\controllers;


use common\models\Article;
use common\models\ArticleCategory;
use common\models\ArticleComment;
use common\models\ArticleLike;
use common\models\ArticleReadObject;
use common\models\CommentLike;
use frontend\models\ArticleSearch;
use yii\db\Expression;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class ArticleController extends BaseController
{
    /**
     * @return array
     */
    public function actionArticleList()
    {
        $data = \Yii::$app->request->queryParams;
        $query = new ArticleSearch();
        $dataProvider = $query->search($data);
        return $dataProvider;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionClassCategory($type)
    {
        $model = ArticleCategory::find()
            ->with(["articleCategories" => function ($query)
            {
                $query->with(["articleCategories" => function ($query)
                {
                    $query->with("thirdCount");
                }, "secondCount"]);
            }, "firstCount"])
            ->where(["type" => $type, "status" => 10, "p_id" => 0])
            ->asArray()
            ->all();

        return $model;
    }

    /**文章详情
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionArticleDetail($id)
    {
        $auth = ArticleReadObject::findOne(['article_id' => $id, "user_id" => \Yii::$app->session->get("userid")]);
        if (!$auth)
        {
            throw new HttpException(402, "您没有访问权限");
        }
        $model = Article::find()
            ->with(["comments" => function ($query)
            {
                return $query->with(["children" => function ($query)
                {
                    $query->with("quest");
                }, "like"]);
            },
                "first", "second", "third", "like"
            ])
            ->where(['id' => $id, 'status' => Article::STATUS_ACTIVE])
            ->asArray()
            ->one();
        if (!$model)
        {
            throw new NotFoundHttpException("对应内容已被删除");
        }
        //指定人员阅读
        ArticleReadObject::updateAll(["is_read" => 1, "updated_at" => time(), "read_time" => time()], ["article_id" => $id, "user_id" => \Yii::$app->session->get('userid')]);
        //查看增加
        Article::updateAll(['view_number' => ArticleReadObject::find()->where(['is_read' => 1, "article_id" => $id])->count()],
            ['id' => $id]);

        return $model;
    }

    /**文章评论
     * @return array
     */
    public function actionCreateComment()
    {
        $model = new ArticleComment();
        $model->loadDefaultValues();
        $model->load(\Yii::$app->request->post());
        $model->user_id = \Yii::$app->session->get('uid');
        $model->username = \Yii::$app->session->get("username");
        $model->article_id = \Yii::$app->request->post('article_id');
        $model->content = \Yii::$app->request->post('content');
        $model->pid = \Yii::$app->request->post('pid');
        $model->rid = \Yii::$app->request->post('rid');
        $model->del = 1;
        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save())
        {
            Article::updateAllCounters(["comment_number" => 1], ['id' => \Yii::$app->request->post('article_id')]);
            return ["retMsg" => "操作成功"];
        } else
        {
            return ["retMsg" => "操作失败"];
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function actionDelComment($id)
    {
        //$info = ArticleComment::findOne(['id' => $id]);
        if (ArticleComment::updateAll(["del" => 0], ["id" => $id]))
        {
            //Article::updateAllCounters(["comment_number"=>-1],['id'=>$info['article_id']]);
            return ["retMsg" => "操作成功"];
        } else
        {
            return ["retMsg" => "操作失败"];
        }

    }

    /**文章喜欢
     * @param $id
     * @return array
     */
    public function actionArticleLike($id)
    {
        $model = new ArticleLike();
        $model->user_id = \Yii::$app->session->get('uid');
        $model->username = \Yii::$app->session->get("username");
        $model->article_id = $id;
        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save())
        {
            Article::updateAllCounters(["like_number" => 1], ['id' => $id]);
            return ["retMsg" => "操作成功"];
        } else
        {
            return ["retMsg" => "操作失败"];
        }
    }

    /**
     * @param $article_id
     * @return array
     */
    public function actionCancelArticleLike($article_id)
    {
        $like = ArticleLike::find()
            ->where(['article_id' => $article_id, 'user_id' => \Yii::$app->session->get('uid')])
            ->one();
        if (!$like)
        {
            throw new NotFoundHttpException("无点赞记录");
        }
        if ($like->delete())
        {
            Article::updateAllCounters(["like_number" => -1], ['id' => $article_id]);
            return ["retMsg" => "操作成功"];
        } else
        {
            return ["retMsg" => "操作失败"];
        }
    }

    /**
     * @param $comment_id
     * @param $article_id
     * @return array
     */
    public function actionCommentLike($comment_id)
    {
        $model = new CommentLike();
        $model->user_id = \Yii::$app->session->get('uid');
        $model->type = CommentLike::ARTICLE;
        $model->comment_id = $comment_id;
        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save())
        {
            return ["retMsg" => "操作成功"];
        } else
        {
            return ["retMsg" => "操作失败"];
        }
    }

    /**
     * @param $comment_id
     * @return array
     */
    public function actionCancelCommentLike($comment_id)
    {
        $model = CommentLike::find()->where(['comment_id' => $comment_id, 'type' => CommentLike::ARTICLE, 'user_id' => \Yii::$app->session->get('uid')])->one();
        if (!$model)
        {
            throw new NotFoundHttpException("无点赞记录");
        }
        if ($model->delete())
        {
            return ["retMsg" => "操作成功"];
        } else
        {
            return ["retMsg" => "操作失败"];
        }
    }

    public function actionDown($filePath)
    {
        //return \Yii::$app->response->sendFile($filePath);

        $sendStr = htmlspecialchars_decode(file_get_contents($filePath));

        $fileInfo = explode("/", $filePath);
        $outfile = end($fileInfo);
        header('Content-type: application/octet-stream; charset=utf8');
        Header("Accept-Ranges: bytes");
        header('Content-Disposition: attachment; filename=' . $outfile);
        echo $sendStr;
        exit();
    }
}
