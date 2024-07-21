<?php

namespace app\controllers;

use app\models\Officer;
use app\models\OfficerSearch;
use app\models\Loan;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class OfficerController extends Controller
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
    $searchModel = new OfficerSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->pagination->pageSize = Yii::$app->params['pageSize'];

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ]);
  }

  public function actionCreate()
  {
    $model = new Officer();
    if ($model->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
        if ($model->imageFile) {
          if ($path = $model->uploadImage()) {
            $model->profile_url = $path;
          }
        }
        $model->imageFile = null;
        if (!$model->save()) throw new Exception("Failed to Save! Code #001");

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Officer created successfully");
        return $this->redirect(['index']);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    return $this->renderAjax('_form', [
      'model' => $model
    ]);
  }

  public function actionUpdate($id)
  {
    $model = Officer::findOne($id);
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
        if (!$model->save()) throw new Exception("Failed to Save! Code #001");

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Officer saved successfully");
        return $this->redirect(Yii::$app->request->referrer);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    return $this->renderAjax('_form', [
      'model' => $model
    ]);
  }

  public function actionView($id)
  {
    $model = Officer::findOne($id);
    if (empty($model)) throw new NotFoundHttpException('The requested page does not exist.');
    if ($model->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {

        if (!$model->save()) throw new Exception("Failed to Save! Code #001");


        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Customer saved successfully");
        return $this->redirect(Yii::$app->request->referrer);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }

    return $this->render('view', [
      'model' => $model,
    ]);
  }


  public function actionLoan($id)
  {
    $model = Officer::findOne($id);
    if (empty($model)) throw new NotFoundHttpException('The requested page does not exist.');

    $dataProvider = new ActiveDataProvider([
      'query' => Loan::find()->andWhere(['customer_id' => $model->id]),
      'pagination' => [
        'pageSize' => Yii::$app->params['pageSize'],
      ],
      'sort' => [
        'defaultOrder' => [
          'created_at' => SORT_DESC,
        ]
      ],
    ]);

    return $this->render('loan', [
      'model' => $model,
      'dataProvider' => $dataProvider
    ]);
  }

  public function actionDelete($id)
  {
    $model = Officer::findOne($id);
    $transaction_exception = Yii::$app->db->beginTransaction();

    try {
      $model->delete();

      $transaction_exception->commit();
      Yii::$app->session->setFlash('success', "Officer deleted successfully");
      return $this->redirect(Yii::$app->request->referrer);
    } catch (Exception $ex) {
      Yii::$app->session->setFlash('warning', $ex->getMessage());
      $transaction_exception->rollBack();
      return $this->redirect(Yii::$app->request->referrer);
    }
  }

  public function actionValidation($id = null)
  {

    $model = $id === null ? new Officer() : Officer::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return \yii\widgets\ActiveForm::validate($model);
    }
  }
}
