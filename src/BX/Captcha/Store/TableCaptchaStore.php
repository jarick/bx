<?php namespace BX\Captcha\Store;
use BX\Captcha\Entity\CaptchaEntity;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;

class TableCaptchaStore implements ITable, \BX\Captcha\Store\ICaptchaStore
{
	use \BX\DB\TableTrait,
	 \BX\Date\DateTrait,
	 \BX\Logger\LoggerTrait;
	protected function settings()
	{
		return [
			'db_table' => 'tbl_captcha',
		];
	}
	protected function columns()
	{
		return[
			CaptchaEntity::C_ID			 => $this->column()->int('T.ID'),
			CaptchaEntity::C_SID		 => $this->column()->string('T.SID',32),
			CaptchaEntity::C_CODE		 => $this->column()->string('T.CODE',32),
			CaptchaEntity::C_TIMESTAMP_X => $this->column()->datetime('T.TIMESTAMP_X'),
			CaptchaEntity::C_UNIQUE_ID	 => $this->column()->string('T.UNIQUE_ID',32),
		];
	}
	public function clear($day = 30)
	{
		$repository = new Repository('captcha');
		$time = $this->date()->convertTimeStamp(time() - $day * 3600 * 24);
		$captches = static::finder(CaptchaEntity::getClass())
			->filter(['<TIMESTAMP_X' => $time])
			->all();
		foreach($captches as $captcha){
			$repository->delete($this,$captcha);
		}
		if (!$repository->commit()){
			throw new \RuntimeException('Error clear old captches');
		}
		return true;
	}
	public function getByUniqueId($unique_id)
	{
		$repository = new Repository('captcha');
		$captcha = self::finder(CaptchaEntity::getClass())
			->sort(['UNIQUE_ID' => 'desc'])
			->filter(['UNIQUE_ID' => $unique_id])
			->get();
		if ($captcha === false){
			$captcha = new CaptchaEntity();
			$captcha->unique_id = $unique_id;
			$captcha->code = md5(uniqid());
			$captcha->sid = md5(uniqid());
			$repository->add($this,$captcha);
		}
		if (!$repository->commit()){
			throw new \RuntimeException('Error add new captcha');
		}
		return $captcha;
	}
}