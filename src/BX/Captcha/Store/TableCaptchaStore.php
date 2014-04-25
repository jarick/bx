<?php namespace BX\Captcha\Store;
use BX\Captcha\Entity\CaptchaEntity;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;

class TableCaptchaStore implements ITable, \BX\Captcha\Store\ICaptchaStore
{
	use \BX\DB\TableTrait,
	 \BX\Date\DateTrait,
	 \BX\Logger\LoggerTrait,
	 \BX\String\StringTrait;
	/**
	 * Settings
	 * @return array
	 */
	protected function settings()
	{
		return [
			'db_table' => 'tbl_captcha',
		];
	}
	/**
	 * Columns
	 * @return array
	 */
	protected function columns()
	{
		return[
			CaptchaEntity::C_ID			 => $this->column()->int('T.ID'),
			CaptchaEntity::C_GUID		 => $this->column()->string('T.GUID',32),
			CaptchaEntity::C_CODE		 => $this->column()->string('T.CODE',32),
			CaptchaEntity::C_TIMESTAMP_X => $this->column()->datetime('T.TIMESTAMP_X'),
		];
	}
	/**
	 * Get captcha
	 * @param string $guid
	 * @param string $code
	 * @return type
	 */
	public function get($guid,$code)
	{
		$filter = [
			CaptchaEntity::C_GUID	 => $guid,
			CaptchaEntity::C_CODE	 => $code,
		];
		return self::finder(CaptchaEntity::getClass())->filter($filter)->get();
	}
	/**
	 * Reload captcha
	 * @param integer $id
	 */
	public function reload($id)
	{
		$repo = new Repository('captcha');
		$captcha = self::finder(CaptchaEntity::getClass())
			->filter([CaptchaEntity::C_ID => $id])
			->get();
		if ($captcha === false){
			throw new \RuntimeException('Captcha not found');
		}
		$repo->update($this,$captcha);
		if (!$repo->commit()){
			throw new \RuntimeException('Error delete captcha');
		}
		return $captcha;
	}
	/**
	 * Delete captcha
	 * @param integer $id
	 * @throws \RuntimeException
	 */
	public function clear($id)
	{
		$repo = new Repository('captcha');
		$captcha = self::finder(CaptchaEntity::getClass())
			->filter([CaptchaEntity::C_ID => $id])
			->get();
		if ($captcha === false){
			throw new \RuntimeException('Captcha not found');
		}
		$repo->delete($this,$captcha);
		if (!$repo->commit()){
			throw new \RuntimeException('Error delete captcha');
		}
		return true;
	}
	/**
	 * Clear old captcha
	 * @param integer $day
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function clearOld($day)
	{
		$repository = new Repository('captcha');
		$time = $this->date()->convertTimeStamp(time() - $day * 3600 * 24);
		$captches = static::finder(CaptchaEntity::getClass())
			->filter(['<'.CaptchaEntity::C_TIMESTAMP_X => $time])
			->all();
		foreach($captches as $captcha){
			$repository->delete($this,$captcha);
		}
		if (!$repository->commit()){
			$mess = print_r($repository->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException('Error clear old captches. Error:'.$mess);
		}
		return true;
	}
	/**
	 * Create captcha
	 * @return \BX\Captcha\Entity\CaptchaEntity
	 * @throws \RuntimeException
	 */
	public function create()
	{
		$entity = new CaptchaEntity();
		$repo = new \BX\DB\UnitOfWork\Repository('captcha');
		$repo->add($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error create captcha. Error: {$mess}.");
		}
		return $entity;
	}
}