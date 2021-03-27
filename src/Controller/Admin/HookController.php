<?php

namespace App\Controller\Admin;

use Cake\Event\Event;
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
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
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
        if ($alert['Value'] &&  ($mail1['Value'] || $mail2['Value'])) {
            
            // $alert = date("H:i", strtotime($alert));
            if ($alert['Value'] == date("H:i")) {
                $allow = true;
            } else {
                print($alert['Value']);
            }
            // print($alert['Value'] . "<br/>");
            // print(date("H:i"));
        }

        // print($alert . "<br/>");
        // print($mail1 . "<br/>");
        // print($mail2 . "<br/>");

        // tmp
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
                    "Position LIKE '%Leader%'"
                ])
                ->order(['StaffID' => 'ASC'])
                ->toArray();

            // echo '<pre>';
            // var_dump($data);
            // echo '</pre>';

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


            $result['success'] = 1;

            self::sendFileToMail($output, $fileNameExport, $mail1['Value'], $mail2['Value']);
        }
        // $result['pdf'] = "excel/output/" . $fileNameExport;
        // print("Not sended");
        exit;
    }

    public static function sendFileToMail($file, $filename, $mail1, $mail2)
    {
        try {
            $mail = new PHPMailer;

            $mail->setFrom('nsv@vps169121.dotvndns.com', 'Sufex ASM System');
            // $mail->FromName = "";


            // $mail->addAddress("info.website.server@gmail.com");
            if ($mail1) {
                $mail->addAddress($mail1);
            }

            if ($mail2) {
                $mail->addAddress($mail2);
            }

            //Provide file path and name of the attachments
            $mail->addAttachment($file, $filename);
            // $mail->addAttachment("images/profile.png"); //Filename is optional
            // $mail->AddReplyTo( 'nsv@vps169121.dotvndns.com', 'Admin' );

            $mail->isHTML(true);

            $mail->Subject = 'Report checkin ' . date("Y/m/d");
            $mail->Body = '<h1>File report</h1>';
            // $mail->AltBody = 'File report';

            $mail->send();
            echo "Message has been sent successfully";
        } catch (\Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }

        // // mail 2

        // $mail2 = new PHPMailer;

        // // $mail2->From = "sufex-asm@vps169121.dotvndns.com";
        // $mail2->FromName = "Sufex ASM System";

        // $mail2->addAddress("tranquang6896@gmail.com");

        // //Provide file path and name of the attachments
        // $mail2->addAttachment($file, $filename);
        // // $mail2->addAttachment("images/profile.png"); //Filename is optional

        // $mail2->isHTML(true);

        // $mail2->Subject = "Subject Text";
        // $mail2->Body = "<i>Mail body in HTML</i>";
        // $mail2->AltBody = "This is the plain text version of the email content";

        // try {
        //     $mail2->send();
        //     echo "Message has been sent successfully 2";
        // } catch (\Exception $e) {
        //     echo "Mailer Error: " . $mail2->ErrorInfo;
        // }



        // // just edit these 
        // $to          = "tran681796@gmail.com"; // addresses to email pdf to
        // $from        = "Sufex ASM System"; // address message is sent from
        // $subject     = "Your PDF email subject"; // email subject
        // $body        = "<p>The PDF is attached.</p>"; // email body
        // $pdfLocation = $file; // file location
        // $pdfName     = $filename; // pdf file name recipient will get
        // $filetype    = "application/pdf"; // type

        // // creates headers and mime boundary
        // $eol = PHP_EOL;
        // $semi_rand     = md5(time());
        // $mime_boundary = "==Multipart_Boundary_$semi_rand";
        // $headers       = "MIME-Version: 1.0$eol" .
        //     "Content-Type: multipart/mixed;$eol boundary=\"$mime_boundary\"";

        // // add html message body
        // $message = "--$mime_boundary$eol" .
        //     "Content-Type: text/html; charset=\"iso-8859-1\"$eol" .
        //     "Content-Transfer-Encoding: 7bit$eol$eol$body$eol";

        // // fetches pdf
        // $file = fopen($pdfLocation, 'rb');
        // $data = fread($file, filesize($pdfLocation));
        // fclose($file);
        // $pdf = chunk_split(base64_encode($data));

        // // attaches pdf to email
        // $message .= "--$mime_boundary$eol" .
        //     "Content-Type: $filetype;$eol name=\"$pdfName\"$eol" .
        //     "Content-Disposition: attachment;$eol filename=\"$pdfName\"$eol" .
        //     "Content-Transfer-Encoding: base64$eol$eol$pdf$eol--$mime_boundary--";

        // // Sends the email
        // if (mail($to, $subject, $message, $headers)) {
        //     echo "The email was sent.";
        // } else {
        //     echo "There was an error sending the mail.";
        // }
    }
}
