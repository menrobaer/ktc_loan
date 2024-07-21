<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Customer;
use app\models\Loan;
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $currentMonthPayment = Loan::find()
            ->select(['COALESCE(SUM(installment_amount),0) installment_amount'])
            ->where(['DATE_FORMAT(date, "%Y-%m")' => date("Y-m")])
            ->andWhere(['NOT IN', 'status', [Loan::DELETED, Loan::CANCELLED]])
            ->one()->installment_amount;
        $previousMonthPayment = Loan::find()
            ->select(['COALESCE(SUM(installment_amount),0) installment_amount'])
            ->where(['DATE_FORMAT(date, "%Y-%m")' =>  date('Y-m', strtotime('-1 month'))])
            ->andWhere(['NOT IN', 'status', [Loan::DELETED, Loan::CANCELLED]])
            ->one()->installment_amount;
        if ($previousMonthPayment != 0) {
            $paymentPercentageChange = (($currentMonthPayment - $previousMonthPayment) / $previousMonthPayment) * 100;
        } else {
            $paymentPercentageChange = 100;
        }
        $paymentPercentageChange = number_format($paymentPercentageChange, 2);

        $currentMonthCustomer = Customer::find()
            ->where(['DATE_FORMAT(created_at, "%Y-%m")' => date("Y-m")])
            ->count();
        $previousMonthCustomer = Customer::find()
            ->where(['DATE_FORMAT(created_at, "%Y-%m")' =>  date('Y-m', strtotime('-1 month'))])
            ->count();
        if ($previousMonthCustomer != 0) {
            $customerPercentageChange = (($currentMonthCustomer - $previousMonthCustomer) / $previousMonthCustomer) * 100;
        } else {
            $customerPercentageChange = 100;
        }
        $customerPercentageChange = number_format($customerPercentageChange, 2);

        $currentMonthLoan = Loan::find()
            ->select(['COALESCE(SUM(original_amount),0) original_amount'])
            ->where(['DATE_FORMAT(date, "%Y-%m")' => date("Y-m")])
            ->andWhere(['NOT IN', 'status', [Loan::DELETED, Loan::CANCELLED]])
            ->one()->original_amount;
        $previousMonthLoan = Loan::find()
            ->select(['COALESCE(SUM(original_amount),0) original_amount'])
            ->where(['DATE_FORMAT(date, "%Y-%m")' =>  date('Y-m', strtotime('-1 month'))])
            ->andWhere(['NOT IN', 'status', [Loan::DELETED, Loan::CANCELLED]])
            ->one()->original_amount;
        if ($previousMonthLoan != 0) {
            $loanPercentageChange = (($currentMonthLoan - $previousMonthLoan) / $previousMonthLoan) * 100;
        } else {
            $loanPercentageChange = 100;
        }
        $loanPercentageChange = number_format($loanPercentageChange, 2);

        $revenueData = Yii::$app->db->createCommand("SELECT 
            SUM(loan.original_amount) original_amount,
            DATE_FORMAT(loan.date, '%b %y') `month`
        FROM loan
        WHERE loan.`status` NOT IN(0, 10)
        AND DATE(loan.`date`) BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 11 MONTH AND CURDATE()
        GROUP BY MONTH(loan.date)
        ORDER BY DATE(loan.date)
        ")->queryAll();
        $chartLabel = json_encode(ArrayHelper::getColumn($revenueData, 'month'));
        $chartAmount = json_encode(ArrayHelper::getColumn($revenueData, function ($data) {
            return floatval($data['original_amount']);
        }));

        $recentlyLoans = Loan::find()
            ->where(['NOT IN', 'status', [Loan::CANCELLED, Loan::DELETED]])
            ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC])
            ->limit(3)
            ->all();

        return $this->render('index', [
            'chartLabel' => $chartLabel,
            'chartAmount' => $chartAmount,
            'currentMonthPayment' => $currentMonthPayment,
            'paymentPercentageChange' => $paymentPercentageChange,
            'currentMonthCustomer' => $currentMonthCustomer,
            'customerPercentageChange' => $customerPercentageChange,
            'currentMonthLoan' => $currentMonthLoan,
            'loanPercentageChange' => $loanPercentageChange,
            'recentlyLoans' => $recentlyLoans
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
