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

namespace App\Controller\Admin;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */

use Cake\Event\Event;

class ScheduleController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->loadModel('TBLMArea');
        $this->loadModel('TBLMCustomer');
        $this->loadModel('TBLMCheck');
        $this->loadModel('TBLMCategory');
        $this->loadModel('TBLTCheckResult');
        $this->loadModel('TBLTImageReport');
        $this->loadModel('TBLTReport');
        $this->loadModel('TBLTTimeCard');
        $this->loadModel('TBLMItem');

        $this->viewBuilder()->setLayout('admin');
        $this->set('roll', 'admin');
    }

    public function beforeFilter(Event $event) {
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
        $customerIds = $this->TBLMCustomer->getAllCustomer();
        $this->set('customerIds', $customerIds);

        $staffIds = $this->TBLMStaff->getAllStaff();
        $this->set('staffIds', $staffIds);

        $staffId = $this->Auth->user('StaffID');
        $this->set('StaffID', $staffId);

        $params['datepicker'] = date('Y/m/d');
        $alert_time = $this->TBLMItem->find()->where(['Code' => 'alert_time'])->first();
        if($alert_time){
            $params['timepicker'] = $alert_time->Value;
        } else {
            $params['timepicker'] = "08:00"; //default
        }
        $mail_receipt_1 = $this->TBLMItem->find()->where(['Code' => 'mail_receipt_1'])->first();
        if($mail_receipt_1){
            $params['mail_receipt_1'] = $mail_receipt_1->Value;
        }
        $mail_receipt_2 = $this->TBLMItem->find()->where(['Code' => 'mail_receipt_2'])->first();
        if($mail_receipt_2){
            $params['mail_receipt_2'] = $mail_receipt_2->Value;
        }
        $this->set('params', $params);
    }

    public function getReport(){
        $id = $this->getRequest()->getData('id');
        $response = [];
        $response['report'] = $this->TBLTReport->getReport($id);
        $timecardID = $this->getRequest()->getData('timecardID');

        if($response['report'] === NULL){
            $response['timecard'] = $this->TBLTTimeCard->find()
                ->where(['TimeCardID' => $timecardID])
                ->select([
                    'ftime' => "CONCAT(DATE_FORMAT(TimeIn, '%H:%i:%s'),'', CASE WHEN TimeOut IS NOT NULL THEN CONCAT(' 〜 ', DATE_FORMAT(TimeOut, '%H:%i:%s')) ELSE '' END)"
                ])
                ->first();
        } else {
            $checks = $this->TBLMCheck->find()
                ->contain('TBLMCategory')
                ->select([
                    'CheckPointVN' => 'TBLMCheck.CheckPointVN',
                    'CheckPointJP' => 'TBLMCheck.CheckPointJP',
                    'CheckID' => 'TBLMCheck.CheckID',
                    'TypeCode' => 'TBLMCheck.TypeCode',
                    'CheckCode' => 'TBLMCheck.CheckCode',
                    'CategoryVN' => 'TBLMCategory.CategoryVN',
                    'CategoryJP' => 'TBLMCategory.CategoryJP'
                ])
                ->where(['TypeCode' => $response['report']->TypeCode])
                ->toArray();

            $response['formCheck'] = false;
            // if have checkbox -> get form
            if(!empty($checks)){
                $response['formCheck'] = true;
                foreach($checks as $item){
                    $category = $item['CategoryJP'];
                    if(!isSet($response['form'][$category])){
                        $response['form'][$category] = array($item);
                    } else {
                        $response['form'][$category][] = $item;
                    }
                }

                // get checked
                $response['report']['checked'] = $this->TBLTCheckResult->find()->where(['TimeCardID' => $response['report']->TimeCardID]);
            }

            // get images
            $response['images'] = $this->TBLTImageReport->find()->where(['ReportID' => $id]);
        }
        return $this->response->withType("application/json")->withStringBody(json_encode($response));
    }

    public function getStaff(){
        $result['success'] = 0;
        $staffID = $this->getRequest()->getData('staffID');
        $staff = $this->TBLMStaff->findByStaffID($staffID);
        $data = $staff->toArray();
        // $data['AreaName'] = "";
        // // get areas
        // if(strpos($data['Area'], ",") !== false){ //if is array
        //     $areas = explode(",",$data['Area']);
        //     $last = (key(array_slice($areas, -1, 1, true)));
        //     foreach($areas as $index=>$value){
        //         $area = $this->TBLMArea->findByAreaID(trim($value));
        //         if($index != $last){
        //             $data['AreaName'] .= $area->Name . ", ";
        //         } else {
        //             $data['AreaName'] .= $area->Name;
        //         }
        //     }
        // } else { //if only one string
        //     $area = $this->TBLMArea->findByAreaID($data['Area']);
        //     $data['AreaName'] = $area->Name;
        // }
        
        // get region South, North, Middle
        // $NAME_REGION = [
        //     "S" => "South",
        //     "N" => "North",
        //     "M" => "Middle"
        // ];
        // $data['RegionName'] = $NAME_REGION[$data['Region']]; 
        $result = [
            'success' => 1,
            'data' => $data
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function getCustomer(){
        $result['success'] = 0;
        $customerID = $this->getRequest()->getData('customerID');
        $customer = $this->TBLMCustomer->findByCustomerID($customerID);
        $result = [
            'success' => 1,
            'data' => $customer
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($result));
    }

    public function sessionSort(){
        $col = $this->getRequest()->getData('col');
        $dir = $this->getRequest()->getData('dir');
        $this->request->session()->write('Config.sort.col', $col);
        $this->request->session()->write('Config.sort.dir', $dir);
        $response = [
            'result' => 'success',
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($response));
    }

    public function setSendMail(){
        $response['success'] = 0;
        $param = $this->getRequest()->getData();
        // set Alert
        if(isset($param['alert']) && $param['alert'] != ""){
            // check for insert or update
            $item_alert = $this->TBLMItem->exists(['Code' => 'alert_time']);
            if($item_alert){
                $item = $this->TBLMItem->find()->where(['Code' => 'alert_time'])->first();
            } else {
                $item = $this->TBLMItem->newEntity();
            }

            $item->Code = "alert_time";
            $item->Name = "Alert time send pdf report checkin to mail";
            $item->Value = $param['alert'];
            $this->TBLMItem->save($item);
        }
        // set Mail receipt 1
        if(isset($param['mail_1'])){
            $item_mail_1 = $this->TBLMItem->exists(['Code' => 'mail_receipt_1']);
            if($item_mail_1){
                $item = $this->TBLMItem->find()->where(['Code' => 'mail_receipt_1'])->first();
            } else {
                $item = $this->TBLMItem->newEntity();
            }
            $item->Code = "mail_receipt_1";
            $item->Name = "Mail receipt pdf report checkin (1)";
            $item->Value = $param['mail_1'];
            $this->TBLMItem->save($item);
            
        }
        // set Mail receipt 2
        if(isset($param['mail_2'])){
            $item_mail_2 = $this->TBLMItem->exists(['Code' => 'mail_receipt_2']);
            if($item_mail_2){
                $item = $this->TBLMItem->find()->where(['Code' => 'mail_receipt_2'])->first();
            } else {
                $item = $this->TBLMItem->newEntity();
            }
            $item->Code = "mail_receipt_2";
            $item->Name = "Mail receipt pdf report checkin (2)";
            $item->Value = $param['mail_2'];
            $this->TBLMItem->save($item);
        }
        $response['success'] = 1;
        return $this->response->withType('application/json')->withStringBody(json_encode($response));
    }
}
