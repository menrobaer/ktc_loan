<?php

namespace app\controllers;

use app\models\Customer;
use app\models\CustomerAttachment;
use app\models\Loan;
use app\models\User;
use app\models\UserProfile;
use app\models\UserSearch;
use app\models\UserRole;
use app\models\UserRolePermission;
use app\models\UserRoleSearch;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class UserController extends Controller
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
    $searchModel = new UserSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->pagination->pageSize = Yii::$app->params['pageSize'];

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ]);
  }

  public function actionCreate()
  {
    $model = new User();
    $profile = new UserProfile();
    $profile->user_id = $model->id;
    if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
        if ($model->imageFile) {
          if ($path = $model->uploadImage()) {
            $model->image_url = $path;
          }
        }
        $model->imageFile = null;

        if ($model->password != "") {
          $model->setPassword($model->password);
          $model->generateAuthKey($model->auth_key);
        }
        if (!$model->save()) throw new Exception("Failed to Save! Code #001");
        if (!$profile->save()) throw new Exception("Failed to Save! Code #002");

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
      'model' => $model,
      'profile' => $profile
    ]);
  }

  public function actionUpdate($id)
  {
    $model = User::findOne($id);
    $profile = UserProfile::findOne(['user_id' => $model->id]);
    $profile = empty($profile) ? new UserProfile() : $profile;
    $profile->user_id = $model->id;
    if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
        if ($model->imageFile) {
          $oldImage = $model->image_url;
          if ($path = $model->uploadImage()) {
            $model->image_url = $path;
            if ($oldImage && file_exists($oldImage)) {
              unlink($oldImage);
            }
          }
        }
        $model->imageFile = null;

        if ($model->password != "") {
          $model->setPassword($model->password);
          $model->generateAuthKey($model->auth_key);
        }
        if (!$model->save()) throw new Exception("Failed to Save! Code #001");
        if (!$profile->save()) throw new Exception("Failed to Save! Code #002");

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
      'model' => $model,
      'profile' => $profile
    ]);
  }

  public function actionView($id)
  {
    $model = User::findOne($id);
    if (empty($model)) throw new NotFoundHttpException('The requested page does not exist.');
    $profile = UserProfile::findOne(['user_id' => $model->id]);
    $profile = empty($profile) ? new UserProfile() : $profile;
    $profile->user_id = $model->id;
    if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
        if ($model->imageFile) {
          $oldImage = $model->image_url;
          if ($path = $model->uploadImage()) {
            $model->image_url = $path;
            if ($oldImage && file_exists($oldImage)) {
              unlink($oldImage);
            }
          }
        }
        $model->imageFile = null;

        if ($model->password != "") {
          $model->setPassword($model->password);
          $model->generateAuthKey($model->auth_key);
        }
        if (!$model->save()) throw new Exception("Failed to Save! Code #001");
        if (!$profile->save()) throw new Exception("Failed to Save! Code #002");

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Profile updated successfully");
        return $this->redirect(Yii::$app->request->referrer);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }

    return $this->render('view', [
      'model' => $model,
      'profile' => $profile
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


  public function actionRole()
  {
    $searchModel = new UserRoleSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->pagination->pageSize = Yii::$app->params['pageSize'];

    return $this->render('role/index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionRoleCreate()
  {
    $model = new UserRole();

    if ($this->request->isPost && $model->load($this->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {

        if (!$model->save()) throw new Exception("Failed to Save! Code #001");

        $chkboxAction = $this->request->post('chkboxAction');
        if (!empty($chkboxAction)) {
          $bulkData = [];
          foreach ($chkboxAction as $key => $value) {
            if (!empty($value)) {
              $bulkData[] = [$model->id, $value];
            }
          }
          $batchInsert = Yii::$app->db->createCommand()->batchInsert(
            'user_role_permission',
            [
              'user_role_id',
              'action_id'
            ],
            $bulkData
          );
          if (!$batchInsert->execute()) throw new Exception("Failed to save booking item!");
        }

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Role saved successfully");
        return $this->redirect(['role-update', 'id' => $model->id]);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    $user_role_id = NULL;
    $userRoleAction = Yii::$app->db->createCommand("SELECT 
        user_role_group.`name` group_name,
        user_role_action.*,
        IF(user_role_permission.id IS NULL OR user_role_permission.id = '', 0,1) checked
      FROM user_role_action
      INNER JOIN user_role_group ON user_role_group.id = user_role_action.group_id
      LEFT JOIN user_role_permission ON user_role_permission.action_id = user_role_action.id 
        AND user_role_permission.user_role_id = :user_role_id
        ORDER BY user_role_group.`sort`
    ")->bindParam(':user_role_id', $user_role_id)
      ->queryAll();
    $userRoleActionByGroup = [];
    if (!empty($userRoleAction)) {
      foreach ($userRoleAction as $key => $value) {
        $userRoleActionByGroup[$value['group_name']][] = $value;
      }
    }

    return $this->render('role/form', [
      'model' => $model,
      'userRoleAction' => $userRoleAction,
      'userRoleActionByGroup' => $userRoleActionByGroup
    ]);
  }

  public function actionRoleUpdate($id)
  {
    $model = UserRole::findOne($id);

    if ($this->request->isPost && $model->load($this->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {

        $chkboxAction = $this->request->post('chkboxAction');
        UserRolePermission::deleteAll(['user_role_id' => $model->id]);
        if (!empty($chkboxAction)) {
          $bulkData = [];
          foreach ($chkboxAction as $key => $value) {
            if (!empty($value)) {
              $bulkData[] = [$model->id, $value];
            }
          }
          $batchInsert = Yii::$app->db->createCommand()->batchInsert(
            'user_role_permission',
            [
              'user_role_id',
              'action_id'
            ],
            $bulkData
          );
          if (!$batchInsert->execute()) throw new Exception("Failed to save booking item!");
        }

        if (!$model->save()) throw new Exception("Failed to Save! Code #001");

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Role saved successfully");
        return $this->redirect(Yii::$app->request->referrer);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    $user_role_id = $model->id;
    $userRoleAction = Yii::$app->db->createCommand("SELECT 
        user_role_group.`name` group_name,
        user_role_action.*,
        IF(user_role_permission.id IS NULL OR user_role_permission.id = '', 0,1) checked
      FROM user_role_action
      INNER JOIN user_role_group ON user_role_group.id = user_role_action.group_id
      LEFT JOIN user_role_permission ON user_role_permission.action_id = user_role_action.id 
        AND user_role_permission.user_role_id = :user_role_id
        ORDER BY user_role_group.`sort`
    ")->bindParam(':user_role_id', $user_role_id)
      ->queryAll();
    $userRoleActionByGroup = [];
    if (!empty($userRoleAction)) {
      foreach ($userRoleAction as $key => $value) {
        $userRoleActionByGroup[$value['group_name']][] = $value;
      }
    }

    return $this->render('role/form', [
      'model' => $model,
      'userRoleAction' => $userRoleAction,
      'userRoleActionByGroup' => $userRoleActionByGroup
    ]);
  }


  public function actionValidation($id = null)
  {

    $model = $id === null ? new User() : User::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return \yii\widgets\ActiveForm::validate($model);
    }
  }
}
