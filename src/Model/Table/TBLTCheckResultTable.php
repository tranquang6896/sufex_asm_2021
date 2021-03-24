<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class TBLTCheckResultTable extends Table {
	public function initialize(array $config) {
		parent::initialize($config);
		$this->setTable('tblTCheckResult');
		$this->setPrimaryKey('ID');

		$this->belongsTo('TBLTTimeCard', [
			'className' => 'TBLTTimeCard',
			'foreignKey' => false,
			'conditions' => ['TBLTTimeCard.TimeCardID = TBLTCheckResult.TimeCardID'],
			'propertyName' => 'TBLTTimeCard',
        ]);
        $this->belongsTo('TBLMType', [
			'className' => 'TBLMType',
			'foreignKey' => false,
			'conditions' => ['TBLMType.TypeCode = TBLTCheckResult.TypeCode'],
			'propertyName' => 'TBLMType',
        ]);
        $this->hasOne('TBLMCheck', [
			'className' => 'TBLMCheck',
			'foreignKey' => false,
			'conditions' => ['TBLTCheckResult.CheckID = TBLMCheck.CheckID'],
			'propertyName' => 'TBLMCheck',
        ]);
	}
}
