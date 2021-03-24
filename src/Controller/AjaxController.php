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

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Constants;

class AjaxController extends Controller
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);

        $this->loadModel('TBLTSchedule');
        $this->loadModel('TBLTReport');
        $this->loadModel('TBLMStaff');
        $this->loadModel('TBLTTimeCard');
        $this->loadModel('TBLTFaceImage');
        $this->loadModel('TBLTAreaStaff');
        $this->loadModel('TBLTImageReport');
        $this->loadComponent('Date');
        $this->loadModel('TBLTDistance');
    }


    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
    }

    public function getAllHoliday()
    {
        //stop render view
        $this->autoRender = false;
        $this->viewBuilder()->setLayout('ajax');

        //get paramater
        $params = $this->request->getData();

        //init conditions
        $staffIds = (isset($params['staffIds'])) ? $params['staffIds'] : [];
        $month = (isset($params['month'])) ? $params['month'] : '';
        $conditions = [];
        if ($staffIds) {
            $conditions['StaffID IN'] = $staffIds;
        }
        if ($month) {
            $conditions['Date LIKE'] = "{$month}%";
        }

        $roll = $params['roll'] ? 1 : 0;

        $title = "CONCAT(DATE_FORMAT(TimeIn, '%H') , (select tblMCustomer.CustomerID From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID))";
        if ($roll) {
            $title = "CONCAT(StaffID,' ', DATE_FORMAT(TimeIn, '%H:%i') , ' ', (select Name From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID))";
        }

        //get schedules
        $events = $this->TBLTTimeCard
            ->find()
            ->select([
                'id' => 'TBLTTimeCard.TimeCardID',
                'title' => $title,
                'ftitle' => "CONCAT(DATE_FORMAT(TimeIn, '%H:%i'),'', CASE WHEN TimeOut IS NOT NULL THEN CONCAT(' - ', DATE_FORMAT(TimeOut, '%H:%i')) ELSE '' END, ' ', (select Name From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID))",
                'start' => "CONCAT(DATE_FORMAT(Date, '%Y-%m-%d'), ' ', DATE_FORMAT(TimeIn, '%H:%i:%s'))",
                'end' => "CONCAT(DATE_FORMAT(Date, '%Y-%m-%d'), ' ', DATE_FORMAT(CASE WHEN TimeOut IS NOT NULL THEN TimeOut ELSE ADDTIME(TimeIn, '1') END, '%H:%i:%s'))",
                'starttime' => "DATE_FORMAT(TimeIn, '%H:%i')",
                'endtime' => "DATE_FORMAT(TimeOut, '%H:%i')",
                'long' => "(select Longitude From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID)",
                'lat' => "(select Latitude From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID)",
                'description' => "CONCAT(StaffID,' (', DATE_FORMAT(TimeIn, '%H:%i'), ' ~ ',  DATE_FORMAT(TimeOut, '%H:%i'),') ', (select Name From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID))",
                'StaffID' => 'StaffID',
                'StaffName' => "(select Name From tblMStaff where tblMStaff.StaffID = TBLTTimeCard.StaffID)",
                'CustomerID' => 'TBLTTimeCard.CustomerID',
                'CustomerName' => '(select Name From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID)',
                'Report' => '(select Report From tblTReport where tblTReport.TimeCardID = TBLTTimeCard.TimeCardID)',
                'Report_ID' => '(select tblTReport.ID From tblTReport where tblTReport.TimeCardID = TBLTTimeCard.TimeCardID)',
                'TypeCode' => '(select tblTReport.TypeCode From tblTReport where tblTReport.TimeCardID = TBLTTimeCard.TimeCardID)',
                'imgcheckin' => "(select CONCAT(tblTFaceImage.`Source`, Name) From tblTFaceImage where TBLTTimeCard.TimeCardID = tblTFaceImage.TimeCardID AND tblTFaceImage.NAME LIKE '%IN%')",
                'imgcheckout' => "(select CONCAT(tblTFaceImage.`Source`, Name) From tblTFaceImage where TBLTTimeCard.TimeCardID = tblTFaceImage.TimeCardID AND tblTFaceImage.NAME LIKE '%OUT%')",
                'color' => '(select
                    CASE WHEN TypeCode = 1 THEN "#EF2424"
                         WHEN TypeCode = 2 THEN "#EF5113"
                         WHEN TypeCode = 3 THEN "#ECC008"
                         WHEN TypeCode = 4 THEN "#0C56D3"
                         WHEN TypeCode = 5 THEN "#6F7172"
                         WHEN TypeCode = 6 THEN "#065D07"
                         WHEN TypeCode = 7 THEN "#6F3FC6"
                         ELSE ""
                    END
                    From tblTReport where tblTReport.TimeCardID = TBLTTimeCard.TimeCardID
                    )',
                'textColor' => '"white"',
            ])
            ->where($conditions)
            ->all();
        echo json_encode($events);die();
    }

    public function updateReport() {
        //stop render view
        $this->autoRender = false;
        $this->viewBuilder()->setLayout('ajax');

        //get paramater
        $params = $this->request->getData();

        $entity = $this->TBLTReport->findById($params['ID'])->first();
        $entity = $this->TBLTReport->patchEntity($entity, $params, ['validate' => false]);
        $this->TBLTReport->save($entity);
        echo json_encode([]);die();
    }

    public function getAllLongLatByDate()
    {
        //stop render view
        $this->autoRender = false;
        $this->viewBuilder()->setLayout('ajax');

        //get paramater
        $params = $this->request->getData();

        //init conditions
        $staffIds = (isset($params['staffIds'])) ? $params['staffIds'] : [];
        $date_from = $params['date_from'];
        $date_to = $params['date_to'];

        $conditions = [];

        if (empty($staffIds)) {
            $conditions['TBLTTimeCard.Date >='] = date('Y-m-d', strtotime($date_from));
            $conditions['TBLTTimeCard.Date <='] = date('Y-m-d', strtotime($date_to));
        }
        else {
            $conditions['TBLTTimeCard.StaffID IN'] = $staffIds;
            $conditions['TBLTTimeCard.Date >='] = date('Y-m-d', strtotime($date_from));
            $conditions['TBLTTimeCard.Date <='] = date('Y-m-d', strtotime($date_to));
        }
        $conditions['TBLMStaff.FlagDelete ='] = 0;

        // case area leader
        $user = $this->TBLMStaff->find()->where(['StaffID' => $params['auth']])->first();
        if($user->Position == 'Area Leader'){
            $areas = $this->TBLTAreaStaff->find()->where(['StaffID' => $user->StaffID])->toArray();
            $arr_areas = [];
            foreach($areas as $area){
                array_push($arr_areas, $area->AreaID);
            }
            if(!empty($arr_areas)){
                $conditions['TBLMCustomer.AreaID IN'] = $arr_areas ;
            }
        }

        $orders = [
            "",
            "TBLTTimeCard.StaffID",
            "StaffName",
            "checkin",
            "checkout",
            "CustomerName",
        ];
        $order = [
            $orders[$this->getRequest()->getData('order.0.column')] => $this->getRequest()->getData('order.0.dir')
        ];

        $query = $this->TBLTTimeCard
            ->find()
            ->contain(['TBLMStaff', 'TBLMCustomer'])
            ->select([
                'TimecardID' => 'TBLTTimeCard.TimeCardID',
                'id' => "(select tblTReport.ID From tblTReport where tblTReport.TimeCardID = TBLTTimeCard.TimeCardID)",
                'StaffID' => 'TBLTTimeCard.StaffID',
                'date' => " DATE_FORMAT(Date, '%Y/%m/%d')",
                'time' => "(select DATE_FORMAT(tblTReport.DateTime, '%H:%i:%s') From tblTReport where tblTReport.TimeCardID = TBLTTimeCard.TimeCardID)",
                'ftime' => "CONCAT(DATE_FORMAT(TimeIn, '%H:%i:%s'),'', CASE WHEN TimeOut IS NOT NULL THEN CONCAT(' 〜 ', DATE_FORMAT(TimeOut, '%H:%i:%s')) ELSE '' END)",
                'CustomerID' => 'TBLTTimeCard.CustomerID',
                'Report' => "(select Report From tblTReport where tblTReport.TimeCardID = TBLTTimeCard.TimeCardID)",
                'StaffName' => "(select Name From tblMStaff where tblMStaff.StaffID = TBLTTimeCard.StaffID)",
                'CustomerName' => "(select Name From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID)",
                // 'Area' => "(select AreaID From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID)",
                'long' => "(select Longitude From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID)",
                'lat' => "(select Latitude From tblMCustomer where tblMCustomer.CustomerID = TBLTTimeCard.CustomerID)",
                'slat' => "SUBSTRING_INDEX(CheckinLocation, ',', 1)",
                'slong' => "SUBSTRING_INDEX(CheckinLocation, ',', -1)",
                'checkin' => "CONCAT(DATE_FORMAT(Date, '%m.%d'), ' ', DATE_FORMAT(TimeIn, '%H:%i:%s'))",
                'checkout' => "CONCAT(DATE_FORMAT(Date, '%m.%d'), ' ', DATE_FORMAT(TimeOut, '%H:%i:%s'))",
                'imgcheckin' => "(select CONCAT(tblTFaceImage.`Source`, Name) From tblTFaceImage where TBLTTimeCard.TimeCardID = tblTFaceImage.TimeCardID AND tblTFaceImage.NAME LIKE '%IN%')",
                'imgcheckout' => "(select CONCAT(tblTFaceImage.`Source`, Name) From tblTFaceImage where TBLTTimeCard.TimeCardID = tblTFaceImage.TimeCardID AND tblTFaceImage.NAME LIKE '%OUT%')",
            ])
            ->where($conditions)
            ->order($order);

        $page = ($this->getRequest()->getData('start') / PAGE_LIMIT_SPECIFIC) + 1;
        $this->paginate = ['page' => $page, 'limit' => PAGE_LIMIT_SPECIFIC, 'maxLimit' => PAGE_MAX_LIMIT];
        $schedules = $this->paginate($query);

        $data = [];
        $date = (isset($params['date'])) ? $params['date'] : date('Y-m-d');
        foreach($schedules as $item){
            $distance = $this->TBLTDistance->find()->where(['Date' => $date, 'StaffID' => $item->StaffID])->first();
            $item['distance'] = '';
            if($distance){
                $item['distance'] = $distance->Distance;
            }
            array_push($data, $item);
        }

        $response = [
            'recordsTotal' => $query->count(),
            'recordsFiltered' => $query->count(),
            'data' => $data,
        ];
        return $this->response->withType("application/json")->withStringBody(json_encode($response));
    }

    public function getVisits() {
        //stop render view
        $this->viewBuilder()->setLayout('ajax');

        $customerID = $this->getRequest()->getData('customerId');
        if ($customerID != '') {
            $orders = [
                "TBLTReport.DateTime",
                "TBLMStaff.StaffID",
                "TBLMStaff.Name",
            ];
            $order = [
                $orders[$this->getRequest()->getData('order.0.column')] => $this->getRequest()->getData('order.0.dir')
            ];
            if($order == ['TBLTReport.DateTime' => 'desc']){
                $order = ['TBLTReport.ID' => 'desc'];
            }
            if($order == ['TBLTReport.DateTime' => 'asc']){
                $order = ['TBLTReport.ID' => 'asc'];
            }

            $conditions = ['TBLTReport.CustomerID' => $customerID];
            if ($this->getRequest()->getData('psStaffID')) {
                $conditions["TBLMStaff.StaffID LIKE"] = "%".$this->getRequest()->getData('psStaffID')."%";
            }
            if ($this->getRequest()->getData('psTimeVisit')) {
                $sTime = str_replace('/', '-', $this->getRequest()->getData('psTimeVisit'));
                $conditions["TBLTReport.DateTime LIKE"] = "%".$sTime."%";
            }

            $query = $this->TBLTReport->getHistoris($conditions, $order);
            $page = ($this->getRequest()->getData('start') / PAGE_LIMIT_FULL) + 1;
            $this->paginate = ['page' => $page, 'limit' => PAGE_LIMIT_FULL];
            $logs = $this->paginate($query);
            $data = $logs->toArray();

            foreach($data as $idx => $e) {
                $imgin = $this->TBLTFaceImage->getImage($e->TimeCardID, 'IN');
                $imgout = $this->TBLTFaceImage->getImage($e->TimeCardID, 'OUT');
                $data[$idx]['imgcheckin'] = $imgin ? $imgin->Source . $imgin->Name : '';
                $data[$idx]['imgcheckout'] = $imgout ? $imgout->Source . $imgout->Name : '';
            }

            $response = [
                'recordsTotal' => $query->count(),
                'recordsFiltered' => $query->count(),
                'data' => $data
            ];
            return $this->response->withType("application/json")->withStringBody(json_encode($response));
        }
        //render selectbox
        $staffIds = $this->TBLMStaff->getStaffIDs();
        $this->set('staffIds', $staffIds);
        $rst = $this->TBLTTimeCard->getTimeVistis();
        $timeVisits = [];
        foreach ($rst as $each) {
            $timeVisits[$this->Date->makeFormat($each->Date, 'Y/m/d')] = $this->Date->makeFormat($each->Date, 'Y/m/d');
        }
        $this->set('timeVisits', $timeVisits);
    }
}
