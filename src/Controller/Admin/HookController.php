<?php

namespace App\Controller\Admin;

use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HookController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->loadModel('TBLTTimeCard');
        $this->loadModel('TBLMStaff');
        $this->loadModel('TBLMItem');
        $this->loadModel('TBLTSendMail');

        Log::setConfig('SENDMAIL', [
            'className' => 'File',
            'path' => PATH_LOG_SEND,
            'levels' => [],
            'scopes' => ['SENDMAIL'],
            'file' => FILE_LOG_SEND,
        ]);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow(['sendReportCheckin']);
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
    }

    /**
     *
     */
    public function index()
    {
    }

    public function sendReportCheckin()
    {
        // check have sended ?
        // $sended = $this->TBLTSendMail
        //     ->exists([
        //         'Date' => date("Y-m-d"),
        //         'Result' => 'success'
        //     ]);

        // if($sended) { exit; }

        $alert = $this->TBLMItem->find()
            ->where([
                'Code' => 'alert_time',
                'Value IS NOT NULL'
            ])
            ->first();
        $mail1 = $this->TBLMItem->find()
            ->where([
                'Code' => 'mail_receipt_1',
                'Value IS NOT NULL'
            ])
            ->first();
        $mail2 = $this->TBLMItem->find()
            ->where([
                'Code' => 'mail_receipt_2',
                'Value IS NOT NULL'
            ])
            ->first();
            
        $allow = false;
        if (isset($alert['Value']) &&  (isset($mail1['Value']) || isset($mail2['Value']))) {
            if ($alert['Value'] == date("H:i")) {
                $allow = true;
            } 
        }

        // debug
        // $allow = true;

        if ($allow) {
            set_time_limit(0);
            ini_set("memory_limit", "-1");
            $builder = $this->viewBuilder();
            // configure as needed
            $builder->setLayout(false);
            $builder->setTemplate('/Element/Pdf/ExportResultCheckin');
            $builder->setHelpers(['Html']);
            // create a view instance


            $data["checked_in"] = $this->TBLTTimeCard->find()
                ->contain(['TBLMStaff'])
                ->where([
                    'Date' => date("Y-m-d"),
                    // 'TimeIn <=' => $alert
                ])
                ->toArray();

            $data["not_come"] = $this->TBLMStaff->find()
                ->where([
                    "StaffID NOT IN (SELECT StaffID FROM tblTTimeCard WHERE Date ='" . date("Y-m-d") . "')",
                    "Position LIKE '%Leader%'",
                    "FlagDelete = 0"
                ])
                ->order(['StaffID' => 'ASC'])
                ->toArray();

            // echo '<pre>';
            // var_dump($data);
            // echo '</pre>';
            // exit;



            $view = $builder->build(compact('data'));
            $content = $view->render();

            $dompdf = new Dompdf(array('enable_font_subsetting' => true));
            $dompdf->loadHtml(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), "UTF-8");
            $dompdf->set_option('defaultFont', 'Times-Roman');

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4');

            // Render the HTML as PDF
            $dompdf->render();

            /* save file */
            $path = WWW_ROOT . "files/PdfResultCheckin";
            if (!file_exists($path)) {
                mkdir($path);
            }

            $fileNameExport = "ASM SYSTEM " . date("Ymd") . " (" . date("D") . ") " . date("Hi") . ".pdf";
            $output = $path . "/" . $fileNameExport;

            $file = $dompdf->output();
            file_put_contents($output, $file);

            // debug
            // exit;

            $result_sended = self::__sendFileToMail($output, $fileNameExport, $mail1['Value'], $mail2['Value']);
            // mails receipt
            $mails_receipt = "";
            if ($mail1['Value']) {
                $mails_receipt .= $mail1['Value'];
            }

            if ($mail2['Value']) {
                $mails_receipt .= ", " . $mail2['Value'];
            }
            // result
            $result = "";
            if(isset($result_sended['success']) && $result_sended['success'] == 1){
                $result = "success";
            } else {
                $result = "fail";
            }

            // ==> SAVE RESULT TO DB
            $sended = TableRegistry::getTableLocator()->get('TBLTSendMail')->newEntity();
            $sended->Date = date("Y-m-d");
            $sended->Time = date("H:i");
            $sended->ToEmail = $mails_receipt;
            $sended->FileSend = $fileNameExport;
            $sended->Result = $result;
            TableRegistry::getTableLocator()->get('TBLTSendMail')->save($sended);

            // ==> WRITE LOG
            $content = 
                "Date: ".date("Y-m-d") . "\n" .
                "Time: ".date("H:i") . "\n" . 
                "To emails: " . $mails_receipt ."\n".
                "Filename attachment: ".$fileNameExport . "\n" .
                "Result: ".$result . "\n \n";
            Log::info($content, ['scope' => ['SENDMAIL']]);
        }
        exit;
    }

    public static function __sendFileToMail($file, $filename, $mail1, $mail2)
    {
        $result['success'] = 0;

        try {
            $mail = new PHPMailer;

            $mail->setFrom('nsv@vps169121.dotvndns.com', 'Sufex ASM System');

            if ($mail1) {
                $mail->addAddress($mail1);
            }

            if ($mail2) {
                $mail->addAddress($mail2);
            }

            //Provide file path and name of the attachments
            $mail->addAttachment($file, $filename);
            // $mail->AddReplyTo( 'nsv@vps169121.dotvndns.com', 'Admin' );

            $mail->isHTML(true);

            $mail->Subject = 'Report checkin ' . date("Y/m/d");
            $mail->Body = '<h1>File report</h1>';
            // $mail->AltBody = 'Plain text';

            if($mail->send()){
                echo "Message has been sent successfully";
                $result['success'] = 1;
            }
        } catch (\Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }

        return $result;
    }
}
