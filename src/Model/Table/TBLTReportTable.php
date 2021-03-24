<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class TBLTReportTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setTable('tblTReport');
        $this->setPrimaryKey('ID');

        $this->belongsTo('TBLTTimeCard', [
            'className' => 'TBLTTimeCard',
            'foreignKey' => 'CustomerID',
            'propertyName' => 'TBLTTimeCard',
        ]);

        $this->belongsTo('TBLMCustomer', [
            'className' => 'TBLMCustomer',
            'foreignKey' => false,
            'conditions' => ['TBLTReport.CustomerID = TBLMCustomer.CustomerID'],
            'propertyName' => 'TBLMCustomer',
	   ]);

		$this->belongsTo('TBLMStaff', [
            'className' => 'TBLMStaff',
            'foreignKey' => false,
            'conditions' => ['TBLTReport.StaffID = TBLMStaff.StaffID'],
            'propertyName' => 'Tblmstaff',
       ]);

       $this->belongsTo('TBLMType', [
        'className' => 'TBLMType',
        'foreignKey' => false,
        'conditions' => ['TBLTReport.TypeCode = TBLMType.TypeCode'],
        'propertyName' => 'TBLMType',
   ]);

    }

    /**
     * @param $staff
     * @param $month
     * @return \Cake\ORM\Query
     */
    public function getReports($staff, $month)
    {
        return $this->find()->where(['StaffID' => $staff, 'Date LIKE' => $month . "%"]);
    }

    /**
     * @return array|\Cake\ORM\Query
     */
	public function getVisits($conditions = [], $order = [], $having = []) {
        $query = $this->query();
        return $this->find()
            ->contain(['TBLMCustomer', 'TBLMStaff'])
            ->select([
                'ReportID' => 'TBLTReport.ID',
                'IDArea' => '(select AreaID from tblArea where tblArea.AreaID = TBLMCustomer.AreaID and TBLMCustomer.CustomerID = TBLTReport.customerID)',
                'AreaName' => '(select Name from tblArea where tblArea.AreaID = IDArea)',
                'IDCustomer' => 'TBLTReport.CustomerID',
                'CustomerName' => 'TBLMCustomer.Name',
                'NumberVisit' => '(SELECT COUNT(ID) FROM tblTReport WHERE tblTReport.CustomerID = TBLMCustomer.CustomerID
                )',
                'IDStaff' => '(SELECT  tblTReport.StaffID FROM ( SELECT CustomerID,StaffID, MAX(ID) AS ID FROM tblTReport GROUP BY CustomerID ) as t1 INNER JOIN  tblTReport ON t1.ID = tblTReport.ID where t1.CustomerID = IDCustomer)',
                'StaffName' => '(SELECT tblMStaff.Name FROM tblMStaff where tblMStaff.StaffID = IDStaff )',
                'LastVisit' => $query->func()->max('TBLTReport.DateTime'),
            ])
            ->where($conditions)
            ->group(['IDCustomer'])
            ->having($having)
            ->order($order);
    }

    /**
     * @return array|\Cake\ORM\Query
     */
	public function getHistoris($conditions, $orders = []) {
        return $this->find()
            ->contain(['TBLMStaff', 'TBLMCustomer'])
            ->select()
            ->where($conditions)->order($orders);
    }


    /**
     * @return \Cake\ORM\Query
     */
    public function getTimeVistis()
    {
        return $this->find('all')->order(['DateTime' => 'desc']);
    }

    public function getReport($id){
        return $this->find()
            ->contain('TBLMType')
            ->where(['ID' => $id])
            ->select([
                'ftime' => "(select CONCAT(DATE_FORMAT(TimeIn, '%H:%i:%s'),'', CASE WHEN TimeOut IS NOT NULL THEN CONCAT(' 〜 ', DATE_FORMAT(TimeOut, '%H:%i:%s')) ELSE '' END) From tblTTimeCard where tblTTimeCard.TimeCardID = TBLTReport.TimeCardID)",
                'Report' => "TBLTReport.Report",
                'ReportEN' => "TBLTReport.ReportEN",
                'ReportVN' => "TBLTReport.ReportVN",
                'ReportJP' => "TBLTReport.ReportJP",
                'TypeCode' => 'TBLTReport.TypeCode',
                'TypeVN' => 'TBLMType.TypeVN',
                'TypeEN' => 'TBLMType.TypeEN',
                'TypeJP' => 'TBLMType.TypeJP',
                'CheckID' => 'TBLTReport.CheckID',
                'TimeCardID' => 'TBLTReport.TimeCardID'
            ])
            ->first();
    }
}
