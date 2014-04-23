<?php namespace BX\Captcha;
use \BX\Counter\Entity\CounterEntity;
use \BX\DB\Helper\TableColumn;

class Migration
{
	use \BX\DB\DBTrait;
	const CAPTCHA_TABLE = 'tbl_counter';
	/**
	 * @root
	 */
	public function upCreateTable($up)
	{
		if ($up){
			$this->db()->createTable(self::CAPTCHA_TABLE,[
				TableColumn::getPK(CounterEntity::C_ID),
				TableColumn::getString(CounterEntity::C_ENTITY,100),
				TableColumn::getString(CounterEntity::C_ENTITY_ID,100),
				TableColumn::getInteger(CounterEntity::C_COUNTER),
				TableColumn::getTimestamp(CounterEntity::C_TIMESTAMP_X),
			]);
		}else{
			$this->db()->dropTable(self::CAPTCHA_TABLE);
		}
	}
}