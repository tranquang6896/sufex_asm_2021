<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class TBLMAreaTable extends Table {
	public function initialize(array $config) {
		parent::initialize($config);
		$this->setTable('tblArea');
		$this->setPrimaryKey('AreaID');

		$this->hasMany('TBLTCustomer', [
			'className' => 'TBLTCustomer',
			'foreignKey' => false,
			'conditions' => ['TBLTCustomer.AreaID = TBLMArea.AreaID'],
			'propertyName' => 'TBLTCustomer',
        ]);

        $this->hasOne('TBLTAreaStaff', [
			'className' => 'TBLTAreaStaff',
			'foreignKey' => false,
			'conditions' => ['TBLMArea.AreaID = TBLTAreaStaff.AreaID'],
			'propertyName' => 'TBLTAreaStaff',
        ]);
    }

    public function getAreaIDs()
    {
        return $this->find('list', [
            'keyField' => 'AreaID',
            'valueField' => "Name"
        ])->order(['Name' => 'asc']);
    }

	public function findByAreaID($areaId){
		return $this->find()->where(["AreaID" => $areaId])->first();
	}
}
