<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */


use Cake\Chronos\Chronos;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Constants;
use Cake\I18n\Time;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Exception;
use PDO;
use Stichoza\GoogleTranslate\TranslateClient;
class MypageController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('TBLMStaff');
        $this->loadModel('TBLMArea');
        $this->loadModel('TBLMCustomer');
        $this->loadModel('TBLTTimeCard');
        $this->loadModel('TBLTReport');
        $this->loadModel('TBLTImageReport');
        $this->loadModel('TBLTFolder');
        $this->loadModel('TBLTFaceImage');
        $this->loadModel('TBLMLanguage');
        $this->loadModel('TBLMType');
        $this->loadModel('TBLMCheck');
        $this->loadModel('TBLMCategory');
        $this->loadModel('TBLTCheckResult');
        $this->loadModel('TBLTDistance');
        $this->loadModel('TBLTAreaStaff');
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
    }

    public function index(){
        $this->viewBuilder()->setLayout('mypage');
        $areas = $this->TBLTAreaStaff->find()->where(['StaffID' => $this->Auth->user('StaffID')])->toArray();
        // // sort by AreaName
        // $area = array();
        // foreach ($areas as $key => $row)
        // {
        //     $area[$key] = $row['Area'];
        // }
        // array_multisort($area, SORT_ASC, $areas);
        // // end sort
        $conditions = [];
        $arr_areas = [];
        foreach($areas as $area){
            array_push($arr_areas, $area->AreaID);
        }
        if(!empty($arr_areas)){
            $conditions['AreaID IN'] = $arr_areas ;
        }
        $listArea = $this->TBLMArea->find()->order('Name','ASC')->where($conditions)->toArray();
        $this->set('listArea', $listArea);
        $area = $this->TBLMArea->find()->order('Name','ASC')->where($conditions)->first();
        $staffid = $this->Auth->user('StaffID');
        $today = date('Y-m-d');
        $timecard = $this->TBLTTimeCard->find()
            ->where(['StaffID' => $staffid, 'Date' => $today])
            ->order(['TimeCardID' => 'DESC'])
            ->first();
        //********************************** GET CHECK IN/OUT ************* */
        if($timecard && !$timecard->TimeOut){
            $customer = $this->TBLMCustomer->find()->where(['CustomerID' => $timecard->CustomerID])->first();
            $area = $this->TBLMArea->find()->where(['AreaID' => $customer->AreaID])->first();
            $list_customer = $this->TBLMCustomer->find()->where(['AreaID' => $customer->AreaID])->toArray();
            $this->set('AreaID', $area->AreaID);
            $this->set('CustomerID', $customer->CustomerID);
            $this->set('listCustomer', $list_customer);
            // text time checkin
            $timeIn = $timecard->TimeIn;
            $timeIn = $timeIn->format('H:i:s');
            $timecard->TimeIn = date('H:i:s', strtotime($timeIn));
            // check checked-out
            if($timecard->TimeOut){
                $this->set('CheckedOut', 1);
                // text
                $timeOut = $timecard->TimeOut;
                $timeOut = $timeOut->format('H:i:s');
                $timecard->TimeOut = date('H:i:s', strtotime($timeOut));
            } else {
                $this->set('CheckedOut', 0);
            }
            $this->set('customerName', $customer->Name);
            $this->set('timecard', $timecard);
        }
        // get language
        $language = $this->request->session()->read('Config.language');
        $language_list = ['en_US' => 'EN', 'jp_JP' => 'JP', 'vn_VN' => 'VN'];

        $data_lang = $this->TBLMLanguage->find()
            ->select(['KeyString', $language_list[$language] . 'Language'])
            ->toArray();
        $data_language = [];
        foreach ($data_lang as $value) {
            //if($value['KeyString'] == 'top_paidleave'){
            $data_language[$value['KeyString']] = $value[$language_list[$language] . 'Language'];
            //}
        }
        $this->set('lang', $language_list[$language]);
        $this->set('data_language', $data_language);

        // typecode report
        $col_type = 'Type' . $language_list[$language];
        $types = $this->TBLMType->find()
            ->select([
                'TypeCode' => 'TypeCode',
                'Type' => $col_type
            ]);
        $this->set('types', $types);
    }

    public function location($lat, $long){
        $this->viewBuilder()->setLayout('mypage');
        // $this->viewBuilder()->setLayout('location');
        $this->set('lat', $lat);
        $this->set('long', $long);
    }

    public function staffs(){
        $this->viewBuilder()->setLayout('staffs');
        $staffs = $this->TBLMStaff->find()
            ->where([
                'FlagDelete' => 0,
                'Position LIKE' => '%Leader%'
            ]);
        // get language
        $language = $this->request->session()->read('Config.language');
        $language_list = ['en_US' => 'EN', 'jp_JP' => 'JP', 'vn_VN' => 'VN'];

        $data_lang = $this->TBLMLanguage->find()
            ->select(['KeyString', $language_list[$language] . 'Language'])
            ->toArray();
        $data_language = [];
        foreach ($data_lang as $value) {
            $data_language[$value['KeyString']] = $value[$language_list[$language] . 'Language'];
        }
        $this->set('lang', $language_list[$language]);
        $this->set('data_language', $data_language);
        $this->set('staffs', $staffs);
    }

    public function getType(){
        $data = $this->TBLMType->find();
        $result['data'] = $data;
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function getCustomer(){
        $areaid = $this->getRequest()->getData('AreaID');
        $customer = $this->TBLMCustomer->find()->where(['AreaID' => $areaid, 'FlagDelete' => 0])->order(['CustomerID' => 'ASC']);
        return $this->response->withType('application/json')->withStringBody(json_encode($customer));
    }

    public static function savePhoto($img,$staffid,$type,$timecardid){
        $result = [];
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        // make path
        $path = WWW_ROOT . 'FaceImage/' . $staffid;
        if(!file_exists($path)) {
            mkdir($path);
        }
        // filename
		$filename = $staffid . date('YmdHi') . $type . '.jpg'; #TODO: validate check in/out
		$file = $path. "/" .$filename;
        $success = file_put_contents($file, $data);
        if($success){
            $result['success'] = 1;
            $result['srcfile'] = 'FaceImage/' . $staffid . "/" . $filename;
            $result['filename'] = $filename;

            // insert DB Folder
            // check if not have then insert
            $folderid = 0;
            $existFolder = TableRegistry::getTableLocator()->get('TBLTFolder')->find()
                ->where([
                    'Name' => $staffid,
                    'Type' => 'Staff',
                    'ParentFolder IS NULL'
                ])
                ->first();
            if($existFolder){
                $folderid = $existFolder->ID;
            } else {
                $folderTable = TableRegistry::getTableLocator()->get('TBLTFolder');
                $folder = $folderTable->newEntity();
                $folder->Name = $staffid;
                $folder->Type = "Staff";
                $folder->Created_at = date('Y-m-d H:i:s');
                $savedFolder = $folderTable->save($folder);

                $folderid = $savedFolder->ID;

            }
            // save to DB FaceImage
            $faceImageTable = TableRegistry::getTableLocator()->get('TBLTFaceImage');
            $faceImage = $faceImageTable->newEntity();
            $faceImage->Name = $filename;
            $faceImage->Source = 'FaceImage/' . $staffid . "/";
            $faceImage->FolderID = $folderid;
            $faceImage->TimeCardID = $timecardid;
            $faceImage->Created_at = date('Y-m-d H:i:s');
            $faceImageTable->save($faceImage);
        }
        return $result;
    }

    public function validateCheckin(){
        $result = [];
        $customerID = $this->getRequest()->getData('customerID');
        $timecard = $this->TBLTTimeCard->find()
            ->where([
                'StaffID' => $this->Auth->user('StaffID'),
                'Date' => date('Y-m-d'),
            ])
            ->order(['TimeCardID' => 'DESC'])
            ->first();
        if($timecard){
            if($timecard->TimeOut){
                $result['valid'] = 1;
            } else {
                if($timecard->CustomerID == $customerID){
                    $result['same_area'] = 1;
                    $timeIn = $timecard->TimeIn;
                    $timeIn = $timeIn->format('H:i:s');
                    $result['timeCheckin'] = date('H:i:s', strtotime($timeIn));
                } else {
                    $result['same_area'] = 0;
                    // customer
                    $customer = $this->TBLMCustomer->find()->where(['CustomerID' => $timecard->CustomerID])->first();
                    $result['customerID'] = $customer->CustomerID;
                    $result['customerName'] = $customer->Name;
                    // get area
                    $area = $this->TBLMArea->find()->where(['AreaID' => $customer->AreaID])->first();
                    $result['areaID'] = $area->AreaID;
                    $result['areaName'] = $area->Name;
                    // time checked in
                    $timeIn = $timecard->TimeIn;
                    $timeIn = $timeIn->format('H:i:s');
                    $result['timeCheckin'] = date('H:i:s', strtotime($timeIn));
                }
            }
        } else {
            $result['valid'] = 1;
        }

        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function insertCheckin(){
        $img = $this->getRequest()->getData('img');
        $staffid = $this->Auth->user('StaffID');

        $result['success'] = 0;

        $timecard = $this->TBLTTimeCard->newEntity();
        $timecard->CustomerID = $this->getRequest()->getData('customerID');
        $timecard->CheckinLocation = $this->getRequest()->getData('coord');
        $timecard->Date = date('Y-m-d');
        $timecard->TimeIn = date('H:i:s');
        $timecard->StaffID = $this->Auth->user('StaffID');
        $timecard->Created_at = date('Y-m-d H:i:s');
        $saved = $this->TBLTTimeCard->save($timecard);
        if($saved){
            $result['success'] = 1;
            $result['timeChecked'] = $saved->TimeIn;
            $capture = MypageController::savePhoto($img,$staffid,'IN',$saved->TimeCardID);
            if(isset($capture['success'])){
                // src file
                $result['srcfile'] = $capture['srcfile'];
            }

            // get data staffs
            $result['dataStaff'] = $this->TBLTTimeCard->find()
                ->where([
                    "StaffID" => $staffid,
                    "CheckinLocation != 'none'",
                    'Date' => date('Y-m-d')
                ])
                ->toArray();
        }
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function putDistance(){
        $distance = $this->getRequest()->getData('distance');
        $staffid = $this->Auth->user('StaffID');
        $date = date('Y-m-d');
        $result['success'] = 0;

        $eDistance = $this->TBLTDistance->find()
            ->where([
                'StaffID' => $staffid,
                'Date' => $date
            ])
            ->first();

        $eDistance = ($eDistance) ? $eDistance : $this->TBLTDistance->newEntity();

        $eDistance->StaffID = $staffid;
        $eDistance->Date = $date;
        $eDistance->Distance = $distance;
        $eDistance->DateUpdated = date('Y-m-d H:i:s');
        if($this->TBLTDistance->save($eDistance)){
            $result['success'] = 1;
        }

        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function validateReport(){
        // vars
        $customerID = $this->getRequest()->getData('customerID');
        $staffid = $this->Auth->user('StaffID');
        $today = date('y/m/d');
        // data

        $report = $this->TBLTReport->find()
            ->where([
                'StaffID' => $staffid,
                "DATE_FORMAT(DateTime, '%y/%m/%d') =" => $today,
                'CustomerID' => $customerID
            ])
            ->order(['ID' => 'DESC'])
            ->first();

        $timecard = $this->TBLTTimeCard->find()
            ->where([
                'StaffID' => $staffid,
                'Date' => $today,
                'CustomerID' => $customerID
            ])
            ->order(['TimeCardID' => 'DESC'])
            ->first();

        $result['TypeSubmit'] = '';

        // if checkout
        if($report){
            // get language
            // $language = $this->request->session()->read('Config.language');
            // $language_list = ['en_US' => 'EN', 'jp_JP' => 'JP', 'vn_VN' => 'VN'];

            // $report_lang = "Report" . $language_list[$language];
            // compare Report->TimecardID <=> Timecard->ID
            if($report->TimeCardID == $timecard->TimeCardID){
                $result['TypeSubmit'] = 'update';
                $result['IDReport'] = $report->ID;
                $result['IDTimeCard'] = $timecard->TimeCardID;
                $result['TypeCode'] = $report->TypeCode;
                $result['Content'] = $report->Report; //$report[$report_lang]
                $result['Check'] = $this->TBLTCheckResult->find()->where(['TimeCardID' => $report->TimeCardID]);

                // images
                $result['images'] = $this->TBLTImageReport->find()->where(['ReportID' => $report->ID]);
            } else {
                $result['TypeSubmit'] = 'new';
                // $result['Content'] = $report->Report;
            }
        } else {
            if($timecard){
                $result['TypeSubmit'] = 'new';
            } else {
                $result['NullCheckin'] = 1;
            }
        }

        if($result['TypeSubmit'] == 'new'){
            $result['IDReport'] = $timecard->TimeCardID;
            $result['IDTimeCard'] = $timecard->TimeCardID;

            $checkresult = $this->TBLTCheckResult->find()
                ->where(['TimeCardID' => $timecard->TimeCardID])
                ->first();
            if($checkresult){
                $result['TypeCode'] = $checkresult->TypeCode;
                $result['Check'] = $this->TBLTCheckResult->find()->where(['TimeCardID' => $timecard->TimeCardID]);
                $result['Content'] = "null";
            }
        }

        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function insertReport(){
        set_time_limit(0);
		ini_set('memory_limit', -1);
        $result['success'] = 0;
        // vars
        $typeSubmit = $this->getRequest()->getData('typeSubmit');
        $typeReport = $this->getRequest()->getData('typeReport');
        $customerID = $this->getRequest()->getData('customerID');
        $content = $this->getRequest()->getData('content');
        $ID = $this->getRequest()->getData('ID');
        $haveCheck = $this->getRequest()->getData('haveCheck');
        $valuesChecked = explode(",",$this->getRequest()->getData()['valuesChecked']);
        $note = $this->getRequest()->getData('note');

        $timeCardID = $this->getRequest()->getData('TimeCardID');
        if($timeCardID == ''){
            $staffid = $this->Auth->user('StaffID');
            $today = date('y/m/d');
            $timecard = $this->TBLTTimeCard->find()
                ->where([
                    'StaffID' => $staffid,
                    'Date' => $today,
                    'CustomerID' => $customerID
                ])
                ->order(['TimeCardID' => 'DESC'])
                ->first();
            $timeCardID = $timecard->TimeCardID;
        }

        // translate
        $tr_jp = new TranslateClient(null, 'ja');
        $tr_vn = new TranslateClient(null, 'vi');
        $tr_en = new TranslateClient(null, 'en');
        $tr_jp->setUrlBase('http://translate.google.cn/translate_a/single');
        $tr_vn->setUrlBase('http://translate.google.cn/translate_a/single');
        $tr_en->setUrlBase('http://translate.google.cn/translate_a/single');

        // new
        if($typeSubmit == 'new'){
            $report = $this->TBLTReport->newEntity();
            $report->StaffID = $this->Auth->user('StaffID');
            $report->DateTime = date('Y-m-d H:i:s');
            $report->CustomerID = $customerID;
            $report->TypeCode = $typeReport;
            $report->TimeCardID = $ID;
            $report->Created_at = date('Y-m-d H:i:s');

            // cases
            if($haveCheck == 1){
                $textarea = $note;
                $checkresults = $this->TBLMCheck->find()->where(['TypeCode' => $typeReport]);
                foreach($checkresults as $item){
                    $check = $this->TBLTCheckResult->newEntity();
                    $check->TimeCardID = $timeCardID;
                    $check->TypeCode = $item->TypeCode;
                    $check->CheckID = $item->CheckID;
                    $check->Result = (in_array($item->CheckID, $valuesChecked)) ? 1 : 0;
                    $this->TBLTCheckResult->save($check);
                }
            } else {
                $textarea = $content;
            }

            $report->Report = $textarea;
            $report->ReportVN = $tr_vn->translate($textarea);
            $report->ReportJP = $tr_jp->translate($textarea);
            $report->ReportEN = $tr_en->translate($textarea);

            $saved_report = $this->TBLTReport->save($report);
            if($saved_report){
                $id_report_access = $saved_report->ID;
            }
        }
        // update
        else {
            $report = $this->TBLTReport->find()->where(['ID' => $ID])->first();

            //update image
            $images_uploaded = $this->TBLTImageReport->find()->where(['ReportID' => $ID]);
            $arr_uploaded = explode(",", $this->getRequest()->getData('imagesUploaded'));
            foreach($images_uploaded as $img){
                if($arr_uploaded != []){
                    if(!in_array($img->ID, $arr_uploaded)){
                        $this->TBLTImageReport->delete($img);
                    }
                } else {
                    $this->TBLTImageReport->delete($img);
                }

            }

            $id_report_access = $ID;
            $report->TypeCode = $typeReport;
             // cases
             if($haveCheck == 1){
                $textarea = $note;
                    $checks = $this->TBLTCheckResult->find()->where(['TimeCardID' => $timeCardID]);
                    foreach($checks as $item){
                        $check = $this->TBLTCheckResult->find()->where(['ID' => $item->ID])->first();
                        $check->Result = (in_array($check->CheckID, $valuesChecked)) ? 1 : 0;
                        $this->TBLTCheckResult->save($check);
                    }
            } else {
                $textarea = $content;
            }
            $report->Report = $textarea;
            $report->ReportVN = $tr_vn->translate($textarea);
            $report->ReportJP = $tr_jp->translate($textarea);
            $report->ReportEN = $tr_en->translate($textarea);

            $this->TBLTReport->save($report);
        }

        if ($id_report_access && $this->getRequest()->getData()['files'] != 'null') {
            $result['uploaded'] = 0;
            $files = $this->getRequest()->getData()['files'];
            $target_dir = WWW_ROOT . "ImageReport/ID_" . $id_report_access ;

            if (!file_exists($target_dir)) {
                mkdir($target_dir);
            }
            foreach ($files as $index => $item) {
                $ext = substr(strtolower(strrchr($item['name'], '.')), 1); //get the extension
                $arr_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'bmp']; //set allowed extensions
                $setNewFileName = basename($this->Auth->user('StaffID') . "_" . str_replace('.' . $ext, '', $item["name"])) . "_" . rand(000000, 999999) . "." . $ext;
                $setNewFileName = str_replace(' ', '', $setNewFileName);
                if (in_array($ext, $arr_ext)) {
                    if(move_uploaded_file($item['tmp_name'], $target_dir . "/" . $setNewFileName)){
                        $image = $this->TBLTImageReport->newEntity();
                        $image->ReportID = $id_report_access;
                        $image->ImageName = $setNewFileName;
                        $image->DateCreated = date('Y-m-d H:i:s');
                        // set background as avatar album
                        // if($background && $index == $background){
                        // 	$image->Background = $albumID;
                        // }
                        $this->TBLTImageReport->save($image);
                    }
                }

            }
            // $result['success'] = 1;
            // $result['path'] = "/files/album/" . $folder . "/";
            // $result['images'] = $this->TBLTAlbumImageUser->find()
            //     ->where(['AlbumID' => $albumID])
            //     ->order(['DateCreate' => 'desc']);
            $result['uploaded'] = 1;
        }

        $result['success'] = 1;

        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function validateCheckout(){
        $result = [];
        $customerID = $this->getRequest()->getData('customerID');
        $timecard = $this->TBLTTimeCard->find()
            ->where([
                'StaffID' => $this->Auth->user('StaffID'),
                'Date' => date('Y-m-d'),
            ])
            ->order(['TimeCardID' => 'DESC'])
            ->first();
        if($timecard){
            // TODO: AFTER FINISH TEST WILL ENABLE
            // $timeIn = strtotime($timecard->TimeIn);
            // $now = strtotime(date('H:i:s'));
            // if($now < $timeIn + 120){
            //     $result['valid'] = -1;
            //     $result['info'] = 'Sorry, please try again later!';
            //     return $this->response->withType('application/json')->withStringBody(json_encode($result));
            // }

            if($timecard->TimeOut){
                if($customerID == $timecard->CustomerID){
                    // alert you checked out at ...
                    $result['same_area'] = 1;
                    $timeOut = $timecard->TimeOut;
                    $timeOut = $timeOut->format('H:i:s');
                    $result['timeCheckout'] = date('H:i:s', strtotime($timeOut));
                } else {
                    // alert you not checked in at here
                    $result['same_area'] = 0;
                }
            } else {
                // same area
                if($customerID == $timecard->CustomerID){
                    // check have report or not
                    $report = $this->TBLTReport->find()->where(['TimeCardID' => $timecard->TimeCardID])->first();
                    if($report){
                        $result['valid'] = 1;
                        $result['timecardIDCheckout'] = $timecard->TimeCardID;
                    } else {
                        $result['not_reported'] = 1;
                    }
                } else { //difference area
                    $result['same_area'] = 0;
                    $result['not_timeout'] = 1;
                }
            }
        } else {
            $result['same_area'] = 0;
        }

        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function insertCheckout(){
        $img = $this->getRequest()->getData('img');
        $staffid = $this->Auth->user('StaffID');

        $result['success'] = 0;

        // insert DB
        $timecard_id = $this->getRequest()->getData('timecardID');
        $timecard = $this->TBLTTimeCard->find()->where(['TimeCardID' => $timecard_id])->first();
        $timecard->TimeOut = date('H:i:s');
        $total_time = abs(strtotime(Time::now()) - strtotime($timecard->TimeIn));
        $total_time = number_format($total_time / 3600, 2);
        $timecard->TotalTime = $total_time;
        $timecard->CheckoutLocation = $this->getRequest()->getData('coord');
        $timecard->Updated_at = date('Y-m-d H:i:s');
        if($this->TBLTTimeCard->save($timecard)){
            $result['success'] = 1;
            $result['timeChecked'] = $timecard->TimeOut;

            $capture = MypageController::savePhoto($img,$staffid,'OUT',$timecard_id);

            if(isset($capture['success'])){
                // src file
                $result['srcfile'] = $capture['srcfile'];
            }
        }
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    /**
     * Check exist timecard with that customer
     */
    public function checkTimecardOfCustomer(){
        $result = [];
        $staffid = $this->Auth->user('StaffID');
        $id = $this->getRequest()->getData('customerID');
        $timecard = $this->TBLTTimeCard->find()
            ->where(['CustomerID' => $id, 'StaffID' => $staffid, 'Date' =>date('Y-m-d')])
            ->order(['TimeCardID' => 'DESC'])
            ->first();
        if($timecard){
            // declare
            $result['timeCheckin'] = '';
            $result['timeCheckout'] = '';
            $result['contentReport'] = '';

            $result['timecard'] = $timecard->toArray();
            // timein
            $timeIn = $timecard->TimeIn;
            $timeIn = $timeIn->format('H:i:s');
            $result['timeCheckin'] = date('H:i:s', strtotime($timeIn));
            // timeout
            if($timecard->TimeOut){
                $timeOut = $timecard->TimeOut;
                $timeOut = $timeOut->format('H:i:s');
                $result['timeCheckout'] = date('H:i:s', strtotime($timeOut));
            }
            // content report
            $report = $this->TBLTReport->find()->where(['TimeCardID' => $timecard->TimeCardID])->first();
            if($report){
                $result['contentReport'] = $report->Report;
            }
        }
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function getArea(){
        $result['areas'] = $this->TBLMArea->find()->order('AreaID','ASC')->toArray();
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function getFormReport(){
        $typeCheck = 0;
        $sortedData = array();
        $id = 0;
        $submited = null;

        if($this->getRequest()->getData('report_id')){
            $id_report = $this->getRequest()->getData('report_id');
            $id_timecard = $this->getRequest()->getData('id_timecard');
            $report = $this->TBLTReport->findById($id_report)->first();
            $submited['Content'] = $report->Report;
            $submited['Check'] = $this->TBLTCheckResult->find()->where(['TimeCardID' => $id_timecard]);
            $id = $report->TypeCode;
        } else {
            $id = $this->getRequest()->getData('id_type');
        }
        // get language
        $language = $this->request->session()->read('Config.language');
        $language_list = ['en_US' => 'JP', 'jp_JP' => 'JP', 'vn_VN' => 'VN'];

        $checks = $this->TBLMCheck->find()
            ->contain('TBLMCategory')
            ->select([
                'CheckPoint' => 'TBLMCheck.CheckPoint'.$language_list[$language],
                'CheckID' => 'TBLMCheck.CheckID',
                'TypeCode' => 'TBLMCheck.TypeCode',
                'CheckCode' => 'TBLMCheck.CheckCode',
                'Category' => 'TBLMCategory.Category'.$language_list[$language]
            ])
            ->where(['TypeCode' => $id])
            ->toArray();
        if(!empty($checks)){
            $typeCheck = 1;
            foreach($checks as $item){
                $category = $item['Category'];
                if(!isSet($sortedData[$category])){
                    $sortedData[$category] = array($item);
                } else {
                    $sortedData[$category][] = $item;
                }
            }

        }

        $response = [
            'typeCheck' => $typeCheck,
            'data' => $sortedData,
            'submited' => $submited,
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($response));
    }

    /**
     * Save value checked in form report
     */
    public function queryCheck(){
        $result['success'] = 0;
        $timecard = $this->getRequest()->getData('timecard');
        $type = $this->getRequest()->getData('type');
        $id = $this->getRequest()->getData('id');
        $checked = $this->getRequest()->getData('checked');

        $check = $this->TBLTCheckResult->find()
            ->where([
                'TimeCardID' => $timecard,
                'CheckID' => $id
            ])
            ->first();
        if($check){
            $check->Result = ($checked == 'true') ? 1 : 0;
        } else {
            $check = $this->TBLTCheckResult->newEntity();
            $check->TimeCardID = $timecard;
            $check->TypeCode = $type;
            $check->CheckID = $id;
            $check->Result = ($checked == 'true') ? 1 : 0;
        }
        $this->TBLTCheckResult->save($check);

        $result['success'] = 1;
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function getImageReport(){
        $success = 0;
        $id = $this->getRequest()->getData('ReportID');
        $images = $this->TBLTImageReport->find()->where(['ReportID' => $id]);
        $success = 1;
        $response = [
            'success' => 1,
            'images' => $images
        ];
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($response));
    }
}
