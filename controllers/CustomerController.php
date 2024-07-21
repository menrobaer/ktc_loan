<?php

namespace app\controllers;

use app\models\Customer;
use app\models\CustomerAttachment;
use app\models\CustomerDetail;
use app\models\CustomerSearch;
use app\models\Loan;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class CustomerController extends Controller
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
    $searchModel = new CustomerSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->pagination->pageSize = Yii::$app->params['pageSize'];

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ]);
  }

  public function actionPrint($id)
  {
    $this->layout = false;
    $model = Customer::findOne($id);
    return $this->render('_print_location', [
      'model' => $model
    ]);
  }

  public function actionCreate()
  {
    $model = new Customer();
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

        $modelDetail = new CustomerDetail();
        $modelDetail->customer_id = $model->id;
        $modelDetail->name = $model->name;
        $modelDetail->phone = $model->phone_number;
        $modelDetail->current_address = $model->address;
        if (!$modelDetail->save(false)) throw new Exception("Failed to Save! Code #002");

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Customer created successfully");
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
    $model = Customer::findOne($id);
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
        Yii::$app->session->setFlash('success', "Customer saved successfully");
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
    $model = Customer::findOne($id);
    if (empty($model)) throw new NotFoundHttpException('The requested page does not exist.');

    $customerDetail = CustomerDetail::findOne(['customer_id' => $model->id]);
    if (empty($customerDetail)) {
      $customerDetail = new CustomerDetail();
      $customerDetail->customer_id = $model->id;
    }
    if ($model->load(Yii::$app->request->post()) && $customerDetail->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {

        if (!$model->save()) throw new Exception("Failed to Save! Code #001");

        if (!$customerDetail->save()) throw new Exception("Failed to Save! Code #002");

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
      'customerDetail' => $customerDetail
    ]);
  }

  public function actionAttachment($id)
  {
    $model = Customer::findOne($id);
    if (empty($model)) throw new NotFoundHttpException('The requested page does not exist.');
    $attachments = CustomerAttachment::find()
      ->where(['customer_id' => $model->id])
      ->orderBy(['created_at' => SORT_DESC])
      ->all();

    return $this->render('attachment', [
      'model' => $model,
      'attachments' => $attachments
    ]);
  }

  public function actionAddAttachment($customerID)
  {
    $customer = Customer::findOne($customerID);
    $model = new CustomerAttachment();
    $model->customer_id = $customer->id;
    if ($model->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        $model->files = UploadedFile::getInstances($model, 'files');
        if (!$model->upload()) throw new Exception("Failed to Save! Code #001");

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Attachments saved successfully");
        return $this->redirect(Yii::$app->request->referrer);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    return $this->renderAjax('_form_attachment', [
      'model' => $model,
      'customer' => $customer
    ]);
  }

  public function actionDeleteAttachment($id)
  {
    $model = CustomerAttachment::findOne($id);
    $transaction_exception = Yii::$app->db->beginTransaction();

    try {
      if ($model->path && file_exists($model->path)) {
        unlink($model->path);
      }
      $model->delete();

      $transaction_exception->commit();
      Yii::$app->session->setFlash('success', "Item deleted successfully");
      return $this->redirect(Yii::$app->request->referrer);
    } catch (Exception $ex) {
      Yii::$app->session->setFlash('warning', $ex->getMessage());
      $transaction_exception->rollBack();
      return $this->redirect(Yii::$app->request->referrer);
    }
  }

  public function actionLoan($id)
  {
    $model = Customer::findOne($id);
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
    $model = Customer::findOne($id);
    $transaction_exception = Yii::$app->db->beginTransaction();

    try {
      $model->delete();

      $transaction_exception->commit();
      Yii::$app->session->setFlash('success', "Item deleted successfully");
      return $this->redirect(Yii::$app->request->referrer);
    } catch (Exception $ex) {
      Yii::$app->session->setFlash('warning', $ex->getMessage());
      $transaction_exception->rollBack();
      return $this->redirect(Yii::$app->request->referrer);
    }
  }

  public function actionValidation($id = null)
  {

    $model = $id === null ? new Customer() : Customer::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return \yii\widgets\ActiveForm::validate($model);
    }
  }
}
