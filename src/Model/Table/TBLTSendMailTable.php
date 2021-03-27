<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class TBLTSendMailTable extends Table {
	public function initialize(array $config) {
		parent::initialize($config);
		$this->setTable('tblTSendMail');
		$this->setPrimaryKey('ID');
	}
}
