<?php

namespace app\controllers;

use app\models\Company;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class CompanyController extends Controller
{
  public function behaviors()
  {
    return array_merge(
      parent::behaviors(),
      [
        // 'access' => [
        //   'class' => \yii\filters\AccessControl::class,
        //   'rules' => [
        //     [
        //       'actions' => \app\modules\admin\models\User::getUserPermission(Yii::$app->controller->id),
        //       'allow' => true,
        //     ]
        //   ],
        // ],
        'verbs' => [
          'class' => VerbFilter::class,
          'actions' => [
            'delete' => ['POST'],
          ],
        ],
      ]
    );
  }

  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
    ];
  }

  public function actionIndex()
  {
    $model = Company::findOne(1);
    $model = empty($model) ? new Company() : $model;
    if ($model->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
        if ($model->imageFile) {
          $oldImage = $model->profile_url;
          if ($path = $model->uploadImage()) {
            $model->profile_url = $path;
            if ($oldImage && file_exists($oldImage)) {
              unlink($oldImage);
            }
          }
        }
        $model->imageFile = null;

        $model->headerFile = UploadedFile::getInstance($model, 'headerFile');
        if ($model->headerFile) {
          $oldImage = $model->header_url;
          if ($path = $model->uploadHeader()) {
            $model->header_url = $path;
            if ($oldImage && file_exists($oldImage)) {
              unlink($oldImage);
            }
          }
        }
        $model->headerFile = null;
        if (!$model->save()) throw new Exception("Failed to Save! Code #001");

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Company info updated successfully");
        return $this->redirect(Yii::$app->request->referrer);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    return $this->render('index', [
      'model' => $model,
    ]);
  }

  public function actionValidation($id = null)
  {

    $model = $id === null ? new Company() : Company::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return \yii\widgets\ActiveForm::validate($model);
    }
  }
}
