<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class TBLTAreaStaffTable extends Table {
	public function initialize(array $config) {
		parent::initialize($config);
		$this->setTable('tblTAreaStaff');
		$this->setPrimaryKey('ID');

		$this->belongsTo('TBLMStaff', [
			'className' => 'TBLMStaff',
			'foreignKey' => false,
			'conditions' => ['TBLMStaff.StaffID = TBLTAreaStaff.StaffID'],
			'propertyName' => 'TBLMStaff',
        ]);

        $this->belongsTo('TBLMArea', [
			'className' => 'TBLMArea',
			'foreignKey' => false,
			'conditions' => ['TBLMArea.AreaID = TBLTAreaStaff.AreaID'],
			'propertyName' => 'TBLMArea',
		]);
    }
}
