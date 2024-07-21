<?php

namespace app\controllers;

use app\models\CustomerDetail;
use app\models\Loan;
use app\models\LoanActivity;
use app\models\LoanPayment;
use app\models\LoanSearch;
use app\models\LoanTerm;
use DateTime;
use PhpOffice\PhpWord\PhpWord;
use Mpdf\Mpdf;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

class LoanDailyController extends Controller
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
    $searchModel = new LoanSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->pagination->pageSize = Yii::$app->params['pageSize'];

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ]);
  }

  public function actionCreate()
  {
    $model = new Loan();
    if ($model->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        if (!$model->save()) throw new Exception("Failed to Save! Code #001");

        $principal = $model->original_amount;
        $interestRate = $model->interest_rate / 100;
        $periods = $model->payment_term_id;

        $numerator = $principal * $interestRate;
        $denominator = 1 - pow(1 + $interestRate, -$periods);

        $installmentPayment = $numerator / $denominator;
        $installmentPayment = ceil($installmentPayment);
        $model->installment_amount = $this->roundUpToNearest500($installmentPayment);
        $originalBalanceInvertal = $model->original_amount;

        $bulkData = [];
        $startDate = new DateTime($model->date);
        $startDate->modify('+1 day');

        $termDates = [];
        for ($weekdayCount = 0; $weekdayCount < $model->payment_term_id; $startDate->modify('+1 day')) :
          if ($model->is_exclude_weekend == 1) {
            if ($startDate->format('N') < 6) {
              $termDates[] = $startDate->format('Y-m-d');
              $weekdayCount++;
            }
          } else {
            $termDates[] = $startDate->format('Y-m-d');
            $weekdayCount++;
          }
        endfor;

        $totalInterestAmount = 0;
        foreach ($termDates as $key => $value) :
          $key += 1;
          $interestAmount = $originalBalanceInvertal * $interestRate;
          $originalPayment = $installmentPayment - $interestAmount;
          $originalBalanceInvertal -= $originalPayment;
          $originalBalance = $key == $model->payment_term_id ? 0 : floatval($originalBalanceInvertal);
          $bulkData[] = [
            $model->id,
            $value,
            $this->roundUpToNearest500($installmentPayment),
            floatval(round($originalPayment, 2)),
            floatval(round($interestAmount, 2)),
            floatval(round($originalBalance, 2)),
            $key == 1 ? floatval($model->service_charge_amount) : 0,
            0
          ];
          $totalInterestAmount += round($interestAmount, 2);
        endforeach;

        if (!empty($bulkData)) {
          if (!Yii::$app->db->createCommand()->batchInsert('loan_term', ['loan_id', 'date', 'amount', 'original_paid', 'interest_amount', 'original_balance_amount', 'service_charge', 'status'], $bulkData)->execute())  throw new Exception("Failed to Save! Code #002");
        }
        $model->interest_rate_amount = $totalInterestAmount;
        $model->grand_total = $model->original_amount + $model->interest_rate_amount;
        if (!$model->save()) throw new Exception("Failed to Save! Code #003");

        LoanActivity::create(['type' => LoanActivity::CREATE, 'loan_id' => $model->id]);

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Loan created successfully");
        return $this->redirect(['view', 'code' => $model->generate_code]);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    return $this->render('create', [
      'model' => $model
    ]);
  }

  public function actionUpdate($id)
  {
    $model = Loan::findOne($id);
    if (empty($model)) throw new NotFoundHttpException('The requested page does not exist.');
    if ($model->paid_amount > 0)  return $this->redirect(['view', 'code' => $model->generate_code]);
    if ($model->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        if (!$model->save()) throw new Exception("Failed to Save! Code #001");

        if ($model->paid_amount == 0) {
          LoanTerm::deleteAll(['loan_id' => $model->id]);

          $principal = $model->original_amount;
          $interestRate = $model->interest_rate / 100;
          $periods = $model->payment_term_id;

          $numerator = $principal * $interestRate;
          $denominator = 1 - pow(1 + $interestRate, -$periods);

          $installmentPayment = $numerator / $denominator;
          $installmentPayment = ceil($installmentPayment);
          $model->installment_amount = $this->roundUpToNearest500($installmentPayment);
          $originalBalanceInvertal = $model->original_amount;

          $bulkData = [];
          $startDate = new DateTime($model->date);
          $startDate->modify('+1 day');

          $termDates = [];
          for ($weekdayCount = 0; $weekdayCount < $model->payment_term_id; $startDate->modify('+1 day')) :
            if ($model->is_exclude_weekend == 1) {
              if ($startDate->format('N') < 6) {
                $termDates[] = $startDate->format('Y-m-d');
                $weekdayCount++;
              }
            } else {
              $termDates[] = $startDate->format('Y-m-d');
              $weekdayCount++;
            }
          endfor;

          $totalInterestAmount = 0;
          foreach ($termDates as $key => $value) :
            $key += 1;
            $interestAmount = $originalBalanceInvertal * $interestRate;
            $originalPayment = $installmentPayment - $interestAmount;
            $originalBalanceInvertal -= $originalPayment;
            $originalBalance = $key == $model->payment_term_id ? 0 : floatval($originalBalanceInvertal);
            $bulkData[] = [
              $model->id,
              $value,
              $this->roundUpToNearest500($installmentPayment),
              floatval(round($originalPayment, 2)),
              floatval(round($interestAmount, 2)),
              floatval(round($originalBalance, 2)),
              $key == 1 ? floatval($model->service_charge_amount) : 0,
              0
            ];
            $totalInterestAmount += round($interestAmount, 2);
          endforeach;

          if (!empty($bulkData)) {
            if (!Yii::$app->db->createCommand()->batchInsert('loan_term', ['loan_id', 'date', 'amount', 'original_paid', 'interest_amount', 'original_balance_amount', 'service_charge', 'status'], $bulkData)->execute())  throw new Exception("Failed to Save! Code #002");
          }
          $model->interest_rate_amount = $totalInterestAmount;
          $model->grand_total = $model->original_amount + $model->interest_rate_amount;
          if (!$model->save()) throw new Exception("Failed to Save! Code #003");
        }
        LoanActivity::create(['type' => LoanActivity::UPDATE, 'loan_id' => $model->id]);

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Loan updated successfully");
        return $this->redirect(['view', 'code' => $model->generate_code]);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    return $this->render('create', [
      'model' => $model
    ]);
  }

  public function actionView($code)
  {
    $model = Loan::findOne(['generate_code' => $code]);
    if (empty($model)) throw new NotFoundHttpException('The requested page does not exist.');

    $loanTerms = LoanTerm::find()
      ->where(['loan_id' => $model->id])
      ->orderBy(['date' => SORT_ASC])
      ->all();

    $activities = LoanActivity::find()
      ->where(['loan_id' => $model->id])
      ->orderBy(['created_at' => SORT_DESC])
      ->all();

    return $this->render('view', [
      'model' => $model,
      'loanTerms' => $loanTerms,
      'activities' => $activities
    ]);
  }

  public function actionExportToWord($code)
  {
    $model = Loan::findOne(['generate_code' => $code]);
    $customerDetail = CustomerDetail::findOne(['customer_id' => $model->customer_id]);
    // Render the HTML content
    $htmlContent = $this->renderPartial('contract-word', [
      'model' => $model,
      'profile' => $customerDetail
    ]);

    // Create a new PHPWord object
    $phpWord = new PhpWord();

    // Define the section properties, including page size and margins
    $sectionStyle = [
      'orientation' => 'portrait', // or 'landscape'
      'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(8.3), // Width in inches
      'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(11.7), // Height in inches
      'marginTop' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.5),
      'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0),
      'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.5),
      'marginRight' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.5),
    ];

    // Add a new section with the specified properties
    $section = $phpWord->addSection($sectionStyle);

    // Add HTML content to the section with custom font styles
    \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlContent);

    $footerFontStyle = ['name' => 'Khmer OS Siemreap', 'size' => '9pt', 'color' => '0096FF'];
    $footer = $section->addFooter();
    $bankAccount = nl2br(Yii::$app->setup->company->bank_account);
    $footer->addText($bankAccount, $footerFontStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
    // $footer->addText("ទូរស័ព្ទទំនាក់ទំនង៖ 012 456 556 - 015 456 556 - 014 456 666", $footerFontStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
    // $footer->addText('លេខទូរស័ព្ទវេរលុយ៖ 014 456 666 និង ABA Account៖ KAN SIKANO កុងដុល្លារ៖ 012 456 556 កុងខ្មែរ៖ 015 456 556', $footerFontStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

    // Generate a temporary file name
    $customerName = str_replace(' ', '_', $model->customer->name);
    $fileName = "loan_contract_with_{$customerName}.docx";
    $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');

    // Save the document to the temporary file
    $phpWord->save($tempFile, 'Word2007');

    // Send the file to the browser as a download
    return Yii::$app->response->sendFile($tempFile, $fileName, [
      'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'inline' => false,
    ])->on(Response::EVENT_AFTER_SEND, function ($event) {
      // Clean up the temporary file
      unlink($event->data);
    }, $tempFile);
  }

  public function actionViewTerm($code)
  {
    $model = Loan::findOne(['generate_code' => $code]);
    $customerDetail = CustomerDetail::findOne(['customer_id' => $model->customer_id]);
    $loanTerms = LoanTerm::find()
      ->where(['loan_id' => $model->id])
      ->orderBy(['date' => SORT_ASC])
      ->all();
    $html = $this->renderPartial('term-pdf', [
      'model' => $model,
      'profile' => $customerDetail,
      'loanTerms' => $loanTerms
    ]);
    $customerName = str_replace(' ', '_', $model->customer->name);
    $fileName = "តារាងបង់ប្រាក់_{$customerName}";
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

  public function actionExportToPdf($code)
  {
    $model = Loan::findOne(['generate_code' => $code]);
    $customerDetail = CustomerDetail::findOne(['customer_id' => $model->customer_id]);
    $html = $this->renderPartial('contract-pdf', [
      'model' => $model,
      'profile' => $customerDetail,
    ]);
    $customerName = str_replace(' ', '_', $model->customer->name);
    $fileName = "ktc_loan_contract_with_{$customerName}";
    $css = "
      td {
        line-height: 15pt;
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
    $bankAccount = nl2br(Yii::$app->setup->company->bank_account);
    $mpdf->SetHTMLFooter("<div style='text-align: center; font-family: khmersiemreap; font-size: 10pt;'>{$bankAccount}</div>");
    // $mpdf->baseScript = 1;
    $mpdf->title = $filename;
    $mpdf->WriteHTML($css, 1);
    $mpdf->WriteHTML($content, 2);
    return $mpdf->Output($filename . '.pdf', $saveType);
  }

  public function actionAddPayment($code, $termID)
  {
    $loan = Loan::findOne(['generate_code' => $code]);
    $loanTerm = LoanTerm::findOne($termID);
    $model = new LoanPayment();
    $model->loan_id = $loan->id;
    $model->loan_term_id = $loanTerm->id;
    if ($model->load(Yii::$app->request->post())) {

      $transaction_exception = Yii::$app->db->beginTransaction();

      try {
        $model->file = UploadedFile::getInstance($model, 'file');
        if ($model->file) {
          if (!$model->uploadFile()) throw new Exception("Failed to Save! Code #001");
        }

        $model->file = null;
        if (!$model->save()) throw new Exception("Failed to Save! Code #002");

        $loan->paid_amount = $loan->paid_amount + $model->amount;
        $loan->balance_amount -= $loan->paid_amount;
        $loan->balance_amount = $loan->balance_amount <= 1 ? 0 : $loan->balance_amount;
        $loan->original_balance_amount -= $loan->original_installment_amount;
        $loan->original_balance_amount = $loan->original_balance_amount <= 0 ? 0 : $loan->original_balance_amount;
        $loan->status = $loan->original_balance_amount == 1 ? Loan::PAID : Loan::PARTIAL_PAID;
        if (!$loan->save()) throw new Exception("Failed to Save! Code #003");

        $loanTerm->status = 1;
        if (!$loanTerm->save()) throw new Exception("Failed to Save! Code #004");

        LoanActivity::create(['type' => LoanActivity::PAYMENT, 'loan_id' => $loan->id, 'payment_id' => $model->id]);

        $transaction_exception->commit();
        Yii::$app->session->setFlash('success', "Payment saved successfully");
        return $this->redirect(Yii::$app->request->referrer);
      } catch (Exception $ex) {
        Yii::$app->session->setFlash('warning', $ex->getMessage());
        $transaction_exception->rollBack();
        return $this->redirect(Yii::$app->request->referrer);
      }
    }
    return $this->renderAjax('_add_payment', [
      'model' => $model,
      'loan' => $loan,
      'loanTerm' => $loanTerm
    ]);
  }

  public function actionVoid($id)
  {
    $model = Loan::findOne($id);
    $transaction_exception = Yii::$app->db->beginTransaction();

    try {
      $model->status = Loan::CANCELLED;
      $model->save();
      LoanActivity::create(['type' => LoanActivity::CANCEL, 'loan_id' => $model->id]);

      $transaction_exception->commit();
      Yii::$app->session->setFlash('success', "Loan voided successfully");
      return $this->redirect(Yii::$app->request->referrer);
    } catch (Exception $ex) {
      Yii::$app->session->setFlash('warning', $ex->getMessage());
      $transaction_exception->rollBack();
      return $this->redirect(Yii::$app->request->referrer);
    }
  }

  public function actionValidation($id = null)
  {

    $model = $id === null ? new Loan() : Loan::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return \yii\widgets\ActiveForm::validate($model);
    }
  }

  public function roundUpToNearest500($number)
  {
    return ceil($number / 500) * 500;
  }
}
