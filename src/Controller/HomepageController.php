<?php

namespace App\Controller;

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
class HomepageController extends AppController
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
        $this->viewBuilder()->setLayout('homepage');
    }

    public function index(){
        
        $staffs = $this->TBLMStaff->find()
            ->where([
                'FlagDelete' => 0,
                'Position LIKE' => '%Leader%'
            ]);
        $this->set('staffs', $staffs);
    }

    public function getPassword(){
        $staffid = $this->getRequest()->getData('staffID');
        $staff = $this->TBLMStaff->find()->where(['StaffID' => $staffid])->first();
        $result['password'] = $staff->Password;
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
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

    /**
     * @return array valid ? result[valid] : result[] 
     */
    public function validateCheckin(){
        $params = $this->getRequest()->getData();
        $result = [];

        $timecard = $this->TBLTTimeCard->find()
            ->where([
                'StaffID' => $params['staffid'],
                'Date' => date('Y-m-d'),
            ])
            ->first();

        if($timecard){
            if($timecard->TimeOut){
                $timeOut = $timecard->TimeOut;
                $timeOut = $timeOut->format('H:i:s');
                $timeOut = date('H:i:s', strtotime($timeOut));
                $result['unvalid'] = "You have checked out at " . $timeOut;
            } else {
                $timeIn = $timecard->TimeIn;
                $timeIn = $timeIn->format('H:i:s');
                $timeIn = date('H:i:s', strtotime($timeIn));
                $result['unvalid'] = "You have checked in at " . $timeIn;
            }
        }

        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function validateCheckout(){
        $params = $this->getRequest()->getData();
        $result = [];

        $timecard = $this->TBLTTimeCard->find()
            ->where([
                'StaffID' => $params['staffid'],
                'Date' => date('Y-m-d'),
            ])
            ->first();

        if($timecard){
            if($timecard->TimeOut){
                $timeOut = $timecard->TimeOut;
                $timeOut = $timeOut->format('H:i:s');
                $timeOut = date('H:i:s', strtotime($timeOut));
                $result['unvalid'] = "You have checked out at " . $timeOut;
            } 
        } else { $result['unvalid'] = "You not have checked in yet. Please check in first !"; }

        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function insertCheckin(){
        $params = $this->getRequest()->getData();
        $result['success'] = 0;

        $timecard = $this->TBLTTimeCard->newEntity();
        $timecard->CheckinLocation = $params['coord'];
        $timecard->Date = date('Y-m-d');
        $timecard->TimeIn = date('H:i:s');
        $timecard->StaffID = $params['staffid'];
        $timecard->Created_at = date('Y-m-d H:i:s');
        $saved = $this->TBLTTimeCard->save($timecard);
        if($saved){
            $result['timeChecked'] = $saved->TimeIn;
            $capture = MypageController::savePhoto($params['img'],$params['staffid'],'IN',$saved->TimeCardID);
            if(isset($capture['success'])){
                // src file
                $result['srcfile'] = $capture['srcfile'];
                $result['success'] = 1;
            }
        }
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function insertCheckout(){
        $params = $this->getRequest()->getData();
        $result['success'] = 0;

        // get timecard checkin
        $timecard = $this->TBLTTimeCard->find()->where([
            'StaffID' => $params['staffid'],
            'Date' => date('Y-m-d')
        ])
        ->first();

        if($timecard){
            // insert DB
            $timecard->TimeOut = date('H:i:s');
            $total_time = abs(strtotime(Time::now()) - strtotime($timecard->TimeIn));
            $total_time = number_format($total_time / 3600, 2);
            $timecard->TotalTime = $total_time;
            $timecard->CheckoutLocation = $this->getRequest()->getData('coord');
            $timecard->Updated_at = date('Y-m-d H:i:s');
            if($this->TBLTTimeCard->save($timecard)){
                
                $result['timeChecked'] = $timecard->TimeOut;

                $capture = MypageController::savePhoto($params['img'],$params['staffid'],'OUT',$timecard->TimeCardID);

                if(isset($capture['success'])){
                    // src file
                    $result['srcfile'] = $capture['srcfile'];
                    $result['success'] = 1;
                }
            }
        }

        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }
}