<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class TBLMItemTable extends Table {
	public function initialize(array $config) {
		parent::initialize($config);
		$this->setTable('tblMItem');
		$this->setPrimaryKey('ID');
	}
}
