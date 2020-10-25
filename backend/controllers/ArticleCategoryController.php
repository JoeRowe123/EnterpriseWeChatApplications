<?php

namespace backend\controllers;

use common\models\Article;
use Yii;
use common\models\ArticleCategory;
use backend\models\ArticleCategorySearch;
use yii\data\ArrayDataProvider;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArticleCategoryController implements the CRUD actions for ArticleCategory model.
 */
class ArticleCategoryController extends Controller
{
    public function actions()
    {
        return [
            'update-status' => [
                'modelClass' => ArticleCategory::class,
                'class' => 'common\widgets\switchery\SwitcheryAction'
            ],
        ];
    }

    /**
     * Lists all ArticleCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleCategorySearch();
        $name = Yii::$app->request->queryParams['name'] ?? null;
        $ids = null;
        if($name) {
            $models = ArticleCategory::find()->where(['type' => Article::TYPE_TYKX])->where(['like', 'name', $name])->all();
            foreach ($models as $model) {
                ArticleCategory::getTopCategory($model, $ids);
            }
        }
        if($ids && $name) {
            $data = ArticleCategory::makeCategory(ArticleCategory::find()
                ->where(['type' => Article::TYPE_TYKX])
                ->andFilterWhere(['id' => $ids])
                ->orderBy('id desc')->all());
        } else {
            if($name) {
                $data = [];
            } else {
                $data = ArticleCategory::makeCategory(ArticleCategory::find()
                    ->where(['type' => Article::TYPE_TYKX])
                    ->orderBy('id desc')->all());
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data
        ]);
        $dataProvider->pagination->setPageSize(1000);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'type' => Article::TYPE_TYKX,
        ]);
    }

    /**
     * Lists all ArticleCategory models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new ArticleCategorySearch();
        $name = Yii::$app->request->queryParams['name'] ?? null;
        $ids = null;
        if($name) {
            $models = ArticleCategory::find()->where(['type' => Article::TYPE_WXT])->where(['like', 'name', $name])->all();
            foreach ($models as $model) {
                ArticleCategory::getTopCategory($model, $ids);
            }
        }

        if($ids && $name) {
            $data = ArticleCategory::makeCategory(ArticleCategory::find()
                ->where(['type' => Article::TYPE_WXT])
                ->andFilterWhere(['id' => $ids])
                ->orderBy('id desc')->all());
        } else {
            if($name) {
                $data = [];
            } else {
                $data = ArticleCategory::makeCategory(ArticleCategory::find()
                    ->where(['type' => Article::TYPE_WXT])
                    ->orderBy('id desc')->all());
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data
        ]);
        $dataProvider->pagination->setPageSize(1000);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'type' => Article::TYPE_WXT,
        ]);
    }

    /**
     * Displays a single ArticleCategory model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ArticleCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new ArticleCategory();
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->p_id) {
                $model->p_id = 0;
            }
            $model->type = $type;
            $model->created_at = time();
            $model->updated_at = time();
            if($model->save()) {
                \Yii::$app->session->setFlash("success", "操作成功");
            } else {
                \Yii::$app->session->setFlash("error", "操作失败:".current($model->getFirstErrors()));
            }
            $index = $model->type == Article::TYPE_TYKX ? 'index' : 'list';
            return $this->redirect([$index]);
        }

        $index = $type == Article::TYPE_TYKX ? 'create' : 'create_wxt';
        return $this->render($index, [
            'model' => $model,
            'type' => $type,
        ]);
    }

    /**
     * Updates an existing ArticleCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldPid = $model->p_id;
        if ($model->load(Yii::$app->request->post())) {
           if(!$model->p_id) {
               $model->p_id = 0;
           }
            $trans = Yii::$app->db->beginTransaction();
            try{
                if($oldPid != $model->p_id) {
                    if(($articleModels = Article::find()->where(['first_category_id' => $id])->orWhere(['second_category_id' => $id])->orWhere(['third_category_id' => $id])->all())) {
                        if($model->p_id != 0) {
                            $pModel = $this->findModel($model->p_id);
                            foreach ($articleModels as $articleModel) {
                                if($pModel->p_id == 0) {
                                    $articleModel->first_category_id = $pModel->id;
                                    $articleModel->second_category_id = $id;
                                    $articleModel->third_category_id = null;
                                } else {
                                    $articleModel->first_category_id = $pModel->p_id;
                                    $articleModel->second_category_id = $pModel->id;
                                    $articleModel->third_category_id = $id;
                                }
                                if(!$articleModel->save(false)) {
                                    throw new \Exception("相关文章分类修改失败【{$articleModel->id}】:".current($articleModel->getFirstErrors()));
                                }
                            }
                        } else {
                            foreach ($articleModels as $articleModel) {
                                $articleModel->first_category_id = $id;
                                $articleModel->second_category_id = null;
                                $articleModel->third_category_id = null;
                                if(!$articleModel->save(false)) {
                                    throw new \Exception("相关文章分类修改失败【{$articleModel->id}】:".current($articleModel->getFirstErrors()));
                                }
                            }
                        }
                    }
                }
                $model->updated_at = time();
                if(!$model->save(false)) {
                    throw new \Exception(current($model->getFirstErrors()));
                }
                $trans->commit();
                \Yii::$app->session->setFlash("success", "操作成功");
            } catch (\Exception $e) {
                $trans->rollBack();
                \Yii::$app->session->setFlash("error", "操作失败:".current($model->getFirstErrors()));
            }
            $index = $model->type == Article::TYPE_TYKX ? 'index' : 'list';
            return $this->redirect([$index]);
        }

        $index = $model->type == Article::TYPE_TYKX ? 'update' : 'update_wxt';
        return $this->render($index, [
            'model' => $model,
            'type' => $model->type,
        ]);
    }

    /**
     * Deletes an existing ArticleCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(Article::find()->where(['first_category_id' => $id])->orWhere(['second_category_id' => $id])->orWhere(['third_category_id' => $id])->exists()) {
            \Yii::$app->session->setFlash("error", "该分类有文章，不可删除");
        } else {
            $model->delete();
            \Yii::$app->session->setFlash("success", "操作成功");
        }

        $index = $model->type == Article::TYPE_TYKX ? 'index' : 'list';
        return $this->redirect([$index]);
    }

    /**
     * Finds the ArticleCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ArticleCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArticleCategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 批量删除
     * @param $ids
     * @return \yii\web\Response
     */
    public function actionBatchDelete($ids, $type)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $ids = explode(",", $ids);

            $models = ArticleCategory::findAll(['id' => $ids]);
            foreach ($models as $model) {

                if(Article::find()->where(['first_category_id' => $model->id])->orWhere(['second_category_id' => $model->id])->orWhere(['third_category_id' => $model->id])->exists()) {
                    throw new \Exception("该分类【{$model->name}】有文章，不可删除");
                }

                $cModels = ArticleCategory::find()->where(['p_id' => $model->id])->asArray()->all();
                $cids = array_column($cModels, 'id');
                if(array_diff($cids, $ids)) {
                    throw new \Exception("批量选择的分类下还存在子分类，请先处理以后在删除！");
                }
            }

            ArticleCategory::deleteAll(['id' => $ids]);
            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        $index = $type == Article::TYPE_TYKX ? 'index' : 'list';
        return $this->redirect([$index]);
    }
}
