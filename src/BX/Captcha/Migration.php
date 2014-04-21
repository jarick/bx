<?php namespace BX\Captcha;
use BX\Captcha\Entity\CaptchaEntity;
use BX\DB\Helper\TableColumn;

class Migration
{
	use \BX\DB\DBTrait;
	const CAPTCHA_TABLE = 'tbl_captcha';
	/**
	 * @root
	 */
	public function upCaptchaTable($up)
	{
		if ($up){
			$this->db()->createTable(self::CAPTCHA_TABLE,[
				TableColumn::getPK(CaptchaEntity::C_ID),
				TableColumn::getString(CaptchaEntity::C_SID,50),
				TableColumn::getString(CaptchaEntity::C_CODE,50),
				TableColumn::getString(CaptchaEntity::C_UNIQUE_ID,50),
				TableColumn::getTimestamp(CaptchaEntity::C_TIMESTAMP_X),
			]);
		}else{
			$this->db()->dropTable(self::CAPTCHA_TABLE);
		}
	}
}