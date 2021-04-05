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
            // set_time_limit(0);
            // ini_set("memory_limit", "-1");
            // $builder = $this->viewBuilder();
            // // configure as needed
            // $builder->setLayout(false);
            // $builder->setTemplate('/Element/Pdf/ExportResultCheckin');
            // $builder->setHelpers(['Html']);
            // create a view instance


            $data["checked_in"] = $this->TBLTTimeCard->find()
                ->contain(['TBLMStaff'])
                ->where([
                    'Date' => date("Y-m-d"),
                    // 'TimeIn <=' => $alert
                ])
                ->order(['TimeIn' => 'ASC'])
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



            // $view = $builder->build(compact('data'));
            // $content = $view->render();

            // $dompdf = new Dompdf(array('enable_font_subsetting' => true));
            // $dompdf->loadHtml(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), "UTF-8");
            // $dompdf->set_option('defaultFont', 'Times-Roman');

            // // (Optional) Setup the paper size and orientation
            // $dompdf->setPaper('A4');

            // // Render the HTML as PDF
            // $dompdf->render();

            // /* save file */
            // $path = WWW_ROOT . "files/PdfResultCheckin";
            // if (!file_exists($path)) {
            //     mkdir($path);
            // }

            // $fileNameExport = "ASM SYSTEM " . date("Ymd") . " (" . date("D") . ") " . date("Hi") . ".pdf";
            // $output = $path . "/" . $fileNameExport;

            // $file = $dompdf->output();
            // file_put_contents($output, $file);

            // debug
            // exit;

            $result_sended = self::__sendFileToMail($data, $mail1['Value'], $mail2['Value']);
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
            $sended->FileSend = '';
            $sended->Result = $result;
            TableRegistry::getTableLocator()->get('TBLTSendMail')->save($sended);

            // ==> WRITE LOG
            $content = 
                "Date: ".date("Y-m-d") . "\n" .
                "Time: ".date("H:i") . "\n" . 
                "To emails: " . $mails_receipt ."\n".
                "Filename attachment: ".'' . "\n" .
                "Result: ".$result . "\n \n";
            Log::info($content, ['scope' => ['SENDMAIL']]);
        }
        exit;
    }

    public static function __sendFileToMail($data, $mail1, $mail2)
    {
        $result['success'] = 0;
                   
        try {
            $mail = new PHPMailer;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
 
            $mail->setFrom('nsv@vps169121.dotvndns.com', 'Sufex ASM System');

            if ($mail1) {
                $mail->addAddress($mail1);
            }

            if ($mail2) {
                $mail->addAddress($mail2);
            }

            //Provide file path and name of the attachments
            // $mail->addAttachment($file, $filename);
            // $mail->AddReplyTo( 'nsv@vps169121.dotvndns.com', 'Admin' );

            $mail->isHTML(true);

            $mail->Subject = 'Report checkin ' . date("Y/m/d");
            $mail->Body = '
                <html lang="en">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <style>
                        body {
                            font-family: "Word-Font";
                            font-size: 16px;
                            color: #000;
                            margin-top: 0px;
                        }
                        .content {
                            padding-left: 150px;
                            text-align: center;
                        }
                        .page_break {
                            page-break-before: always;
                        }
                        .middle-table .border td {
                            border: 1px solid #000;
                            border-bottom: 0px;
                            border-right: 0px;
                            /* background */
                            text-align: center;
                        }
                        .middle-table {
                            border-spacing: 0px;
                            /* width:380px; */
                        }
                        .border-bottom {
                            border-bottom: 1px solid #000 !important;
                        }
                        .text-header2{
                            text-align:left !important;
                            padding:5px 0px !important;
                            font-size:16px !important;
                        }
                    </style>
                </head>
                <body class="main">
                    <div class="content">
                        <table class="middle-table">
                            <tr>
                                <td align="right" style="width:60px">&ensp;&ensp;&ensp;</td>
                                <td align="center" style="padding:0px 15px;width:100px">&ensp;&ensp;&ensp;&ensp;&ensp;</td>
                                <td style="padding-left:5px;width:250px;text-align:left !important">&ensp;&ensp;&ensp;&ensp;&ensp;</td>
                                <td align="center" style="width:200px">&ensp;&ensp;&ensp;&ensp;&ensp;</td>
                            </tr>
                            <tr>
                                <td colspan="4" align="center" style="width:610px;font-weight:900;font-size:24px">Check in Report</td>
                            </tr>
                            <tr>
                                <td colspan="4" align="right" style="width:610px;font-size:16px">Datetime: '.date('Y/m/d H:i:s').'</td>
                            </tr>
            ';
            
            
            if (!empty($data['checked_in'])){
                $i = 0;
                $mail->Body .= '
                    <tr>
                        <td colspan="4" class="text-header2">出 社 状況</td>
                    </tr>
                    <tr class="border" style="border-right: 1px solid #000 !important">
                        <td align="center" style="width:20px !important;text-align:center !important;font-weight:900;font-size:16px">No.</td>
                        <td align="center" style="text-align:center !important;font-weight:900;font-size:16px">ID</td>
                        <td align="center" style="text-align:center !important;font-weight:900;font-size:16px">Name</td>
                        <td align="center" style="width:200px !important;border-right:1px solid #000 !important;font-weight:900;font-size:16px">Check-in Time</td>
                    </tr>
                ';
                foreach ($data['checked_in'] as $item){
                    $i++;
                    if ($i < count($data['checked_in'])){
                        $mail->Body .= '
                            <tr class="border">
                                <td align="right" style="width:20px !important;text-align:right !important;padding-right:4px;font-size:16px">'.$i.'</td>
                                <td align="center" style="text-align:center !important;font-size:16px">'.$item->StaffID.'</td>
                                <td align="left" style="text-align:left !important;padding-left:5px;font-size:16px">'.$item->Tblmstaff->Name.'</td>
                                <td align="center" style="border-right:1px solid #000 !important;font-size:16px">'.date("H:i", strtotime($item->TimeIn)).'</td>
                            </tr>
                        ';
                    } else {
                        $mail->Body .= '
                            <tr class="border">
                                <td align="right" style="border-bottom:1px solid #000 !important; width:20px !important;text-align:right !important;padding-right:4px;font-size:16px">'.$i.'</td>
                                <td align="center" style="border-bottom:1px solid #000 !important; text-align:center !important;font-size:16px">'.$item->StaffID.'</td>
                                <td align="left" style="border-bottom:1px solid #000 !important; text-align:left !important;padding-left:5px;font-size:16px">'.$item->Tblmstaff->Name.'</td>
                                <td align="center" style="border-bottom:1px solid #000 !important; border-right:1px solid #000 !important;font-size:16px">'.date("H:i", strtotime($item->TimeIn)).'</td>
                            </tr>
                        ';
                    }
                }
                
            }
            

            $mail->Body .= '
                <tr>
                    <td style="padding-bottom:10px;" colspan="4">
                    <td>
                </tr>
            ';

            if (!empty($data['not_come'])){
                $i = 0;
                $mail->Body .= '
                    <tr>
                        <td colspan="3" class="text-header2">Not yet come</td>
                    </tr>
                    <tr class="border" style="border-right: 1px solid #000 !important;font-size:16px">
                        <td align="center" style="width:20px !important;text-align:center !important;font-weight:900;font-size:16px">No.</td>
                        <td align="center" style="text-align:center !important;font-weight:900;font-size:16px">ID</td>
                        <td align="center" style="text-align:center !important;border-right:1px solid #000 !important;font-weight:900;font-size:16px">Name</td>
                    </tr>
                ';
                foreach ($data['not_come'] as $item){
                    $i++;
                    if ($i < count($data['not_come'])){
                        $mail->Body .= '
                            <tr class="border">
                                <td align="right" style="width:20px !important;text-align:right !important;padding-right:4px;font-size:16px">'.$i.'</td>
                                <td align="center" style="text-align:center !important;font-size:16px">'.$item->StaffID.'</td>
                                <td align="left" style="text-align:left !important;padding-left:5px;border-right:1px solid #000 !important;font-size:16px">'.$item->Name.'</td>
                            </tr>
                        ';
                    } else {
                        $mail->Body .= '
                            <tr class="border">
                                <td align="right" style="border-bottom:1px solid #000 !important;width:20px !important;text-align:right !important;padding-right:4px;font-size:16px">'.$i.'</td>
                                <td align="center" style="border-bottom:1px solid #000 !important;text-align:center !important;font-size:16px">'.$item->StaffID.'</td>
                                <td align="left" style="border-bottom:1px solid #000 !important;text-align:left !important;padding-left:5px;border-right:1px solid #000 !important;font-size:16px">'.$item->Name.'</td>
                            </tr>
                        ';
                    }
                }
            }

            $mail->Body .= '
                        </table>
                    </div>
                </body>
                </html>
            ';

            print('check');
            var_dump($mail->Body);

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
