<?php

namespace app\controllers;

use app\models\Loan;
use app\models\LoanSearch;
use app\models\User;
use Mpdf\Mpdf;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ReportController extends Controller
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

  public function actionBalanceStatement()
  {
    if (Yii::$app->setup->isLoanMonthly()) {
      $fromDate = date("Y-m-d");
      $toDate = date("Y-m-d");

      if (!empty($this->request->get('dateRange'))) {
        $dateRange = explode(' to ', $this->request->get('dateRange'));
        if (count($dateRange) == 2) {
          $fromDate = $dateRange[0];
          $toDate = $dateRange[1];
        } else {
          $fromDate = $this->request->get('dateRange');
          $toDate = $this->request->get('dateRange');
        }
      }
      $data = Yii::$app->db->createCommand("SELECT 
        customer.`name`,
        loan.balance_amount,
        loan.installment_amount
      FROM loan
      INNER JOIN customer ON customer.id = loan.customer_id
      WHERE loan.`status` NOT IN (0,10)
      AND loan.balance_amount > 0
      ORDER BY customer.`name`
      ")
        ->queryAll();
      $totalProfit = Yii::$app->db->createCommand("SELECT
        SUM(loan_term.interest_amount + loan_term.service_charge)
        FROM loan_term
        INNER JOIN loan ON loan.id = loan_term.loan_id
        WHERE loan.`status` NOT IN (0,10)
        AND loan_term.date BETWEEN :fromDate AND :toDate
        	GROUP BY loan_term.date
      ", [':fromDate' => $fromDate, ':toDate' => $toDate])->queryScalar();
      $totalProfit = floatval($totalProfit);
    } else {
      $fromDate = date("Y-m-d");
      $toDate = date("Y-m-d");

      if (!empty($this->request->get('dateRange'))) {
        $dateRange = explode(' to ', $this->request->get('dateRange'));
        if (count($dateRange) == 2) {
          $fromDate = $dateRange[0];
          $toDate = $dateRange[1];
        } else {
          $fromDate = $this->request->get('dateRange');
          $toDate = $this->request->get('dateRange');
        }
      }
      $data = Yii::$app->db->createCommand("SELECT 
        customer.`name`,
        loan.balance_amount,
        loan.installment_amount
      FROM loan
      INNER JOIN customer ON customer.id = loan.customer_id
      WHERE loan.`status` NOT IN (0,10)
      AND loan.balance_amount > 0
      ORDER BY customer.`name`
      ")
        ->queryAll();
      $totalProfit = Yii::$app->db->createCommand("SELECT
        SUM(loan_term.interest_amount + loan_term.service_charge)
        FROM loan_term
        INNER JOIN loan ON loan.id = loan_term.loan_id
        WHERE loan.`status` NOT IN (0,10)
        AND loan_term.date BETWEEN :fromDate AND :toDate
        	GROUP BY loan_term.date
      ", [':fromDate' => $fromDate, ':toDate' => $toDate])->queryScalar();
      $totalProfit = intval($totalProfit);
    }

    return $this->render('balance-statement/index', [
      'data' => $data,
      'fromDate' => $fromDate,
      'toDate' => $toDate,
      'totalProfit' => $totalProfit
    ]);
  }

  public function actionPaymentReceivable()
  {
    if (Yii::$app->setup->isLoanMonthly()) {
      $selectedDate = $this->request->get('selectedDate', date("Y-m-d"));
      $selectedOfficer = $this->request->get('selectedOfficer');
      $data = Yii::$app->db->createCommand("SELECT 
          CONCAT(`user`.first_name, ' ',`user`.last_name) officer_name,
          IF(:selectedOfficer = '', SUM(loan_term.amount), loan_term.amount) amount,
          customer.name customer_name,
          IF(loan.`status` = 1, loan_term.amount,0) paid
        FROM loan
        INNER JOIN loan_term ON loan_term.loan_id = loan.id
        INNER JOIN `user` ON `user`.id = loan.officer_id
        INNER JOIN customer ON customer.id = loan.customer_id
        WHERE loan.`status` NOT IN (0,10)
        AND loan_term.date = :selectedDate
        AND loan.balance_amount > 0
        AND IF(:selectedOfficer = '', true, loan.officer_id = :selectedOfficer)
        GROUP BY IF(:selectedOfficer != '', loan_term.id, loan.officer_id)
        ", [':selectedDate' => $selectedDate, ':selectedOfficer' => $selectedOfficer])
        ->queryAll();
    } else {
      $selectedDate = $this->request->get('selectedDate', date("Y-m-d"));
      $selectedOfficer = $this->request->get('selectedOfficer');
      $data = Yii::$app->db->createCommand("SELECT 
            CONCAT(`user`.first_name, ' ',`user`.last_name) officer_name,
            IF(:selectedOfficer = '', SUM(loan_term.amount), loan_term.amount) amount,
            customer.name customer_name,
            IF(loan.`status` = 1, loan_term.amount,0) paid
          FROM loan
          INNER JOIN loan_term ON loan_term.loan_id = loan.id
          INNER JOIN `user` ON `user`.id = loan.officer_id
          INNER JOIN customer ON customer.id = loan.customer_id
          WHERE loan.`status` NOT IN (0,10)
          AND loan_term.date = :selectedDate
          AND loan.balance_amount > 0
          AND IF(:selectedOfficer = '', true, loan.officer_id = :selectedOfficer)
          GROUP BY IF(:selectedOfficer != '', loan_term.id, loan.officer_id)
       ", [':selectedDate' => $selectedDate, ':selectedOfficer' => $selectedOfficer])
        ->queryAll();
    }

    return $this->render('payment-receivable/index', [
      'data' => $data,
      'selectedDate' => $selectedDate,
      'selectedOfficer' => $selectedOfficer
    ]);
  }

  public function actionViewPaymentReceivable()
  {
    if (Yii::$app->setup->isLoanMonthly()) {
      $selectedDate = $this->request->get('selectedDate', date("Y-m-d"));
      $selectedOfficer = $this->request->get('selectedOfficer');
      $data = Yii::$app->db->createCommand("SELECT 
          CONCAT(`user`.first_name, ' ',`user`.last_name) officer_name,
          IF(:selectedOfficer = '', SUM(loan_term.amount), loan_term.amount) amount,
          customer.name customer_name,
          IF(loan.`status` = 1, loan_term.amount,0) paid
        FROM loan
        INNER JOIN loan_term ON loan_term.loan_id = loan.id
        INNER JOIN `user` ON `user`.id = loan.officer_id
        INNER JOIN customer ON customer.id = loan.customer_id
        WHERE loan.`status` NOT IN (0,10)
        AND loan_term.date = :selectedDate
        AND loan.balance_amount > 0
        AND IF(:selectedOfficer = '', true, loan.officer_id = :selectedOfficer)
        GROUP BY IF(:selectedOfficer != '', loan_term.id, loan.officer_id)
        ", [':selectedDate' => $selectedDate, ':selectedOfficer' => $selectedOfficer])
        ->queryAll();
    } else {
      $selectedDate = $this->request->get('selectedDate', date("Y-m-d"));
      $selectedOfficer = $this->request->get('selectedOfficer');
      $data = Yii::$app->db->createCommand("SELECT 
            CONCAT(`user`.first_name, ' ',`user`.last_name) officer_name,
            IF(:selectedOfficer = '', SUM(loan_term.amount), loan_term.amount) amount,
            customer.name customer_name,
            IF(loan.`status` = 1, loan_term.amount,0) paid
          FROM loan
          INNER JOIN loan_term ON loan_term.loan_id = loan.id
          INNER JOIN `user` ON `user`.id = loan.officer_id
          INNER JOIN customer ON customer.id = loan.customer_id
          WHERE loan.`status` NOT IN (0,10)
          AND loan_term.date = :selectedDate
          AND loan.balance_amount > 0
          AND IF(:selectedOfficer = '', true, loan.officer_id = :selectedOfficer)
          GROUP BY IF(:selectedOfficer != '', loan_term.id, loan.officer_id)
       ", [':selectedDate' => $selectedDate, ':selectedOfficer' => $selectedOfficer])
        ->queryAll();
    }
    $officer = User::findOne($selectedOfficer);
    $officerName = !empty($officer) ? "{$officer->getFullName()} - {$officer->profile->phone}" : "";

    $html = $this->renderPartial('payment-receivable/view', [
      'data' => $data,
      'selectedDate' => $selectedDate,
      'selectedOfficer' => $selectedOfficer,
      'officerName' => $officerName
    ]);
    $fileName = "payment-receivable-report-{$selectedDate}";
    $css = "
      td,th {
        font-family: khmersiemreap;
        line-height: 18pt;
      }
      #table-sheet th,#table-sheet td {
        padding: 2px 5px;
      }
    ";
    // $html = str_replace("'", "\'", $html); 

    // echo $html;
    return $this->pdfDownload($html, $css, $fileName, \Mpdf\Output\Destination::INLINE);
  }

  protected function pdfDownload($content, $css = '', $filename, $saveType = 'D')
  {
    ini_set("pcre.backtrack_limit", "10000000");

    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];
    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new Mpdf([
      'fontDir' => array_merge($fontDirs, [Yii::getAlias('@vendor') . '/mpdf/mpdf/ttfonts']),
      'fontdata' => $fontData + [
        "khmermuol" => [
          'B' => 'Moul-Regular.ttf',
          'R' => 'KhmerOS_muol.ttf',
          'useOTL' => 0xFF,
          'useKashida' => 75,
        ],
        'khmersiemreap' => [
          'R' => 'Khmer OS Siemreap Regular.ttf',
          'useOTL' => 0xFF,
          'useKashida' => 75,
        ],
        'DejaVuSans' => [
          'R' => 'DejaVuSans.ttf',
          'B' => 'DejaVuSans-Bold.ttf',
        ],
        'default_font' => 'DejaVuSans'
      ],
      // 'autoScriptToLang' => true,
      'autoLangToFont' => true,
      'format' => 'A4',
      'orientation' => 'P',
      'margin_top' => 20,
      'margin_bottom' => 30
    ]);
    // $mpdf->baseScript = 1;
    $mpdf->title = $filename;
    $mpdf->WriteHTML($css, 1);
    $mpdf->WriteHTML($content, 2);
    return $mpdf->Output($filename . '.pdf', $saveType);
  }
}
