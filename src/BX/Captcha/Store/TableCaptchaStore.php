<?php namespace BX\Captcha\Store;
use BX\Captcha\Entity\CaptchaEntity;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;

class TableCaptchaStore implements ITable, \BX\Captcha\Store\ICaptchaStore
{
	use \BX\DB\TableTrait,
	 \BX\Date\DateTrait;
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
	public function check($guid,$code)
	{
		$filter = [
			'='.CaptchaEntity::C_GUID	 => $guid,
			'='.CaptchaEntity::C_CODE	 => $code,
		];
		return self::finder(CaptchaEntity::getClass())->filter($filter)->count() > 0;
	}
	/**
	 * Reload captcha
	 * @param string $guid
	 * @return CaptchaEntity[]
	 * @throws \RuntimeException
	 */
	public function reload($guid)
	{
		$repo = new Repository('captcha');
		$captches = $this->getByGuid($guid);
		if ($captches->count() === 0){
			throw new \RuntimeException('Captcha not found');
		}
		foreach($captches as $captcha){
			$repo->update($this,$captcha);
		}
		if (!$repo->commit()){
			throw new \RuntimeException('Error reload captcha');
		}
		$captches->rewind();
		return $captches;
	}
	/**
	 * Delete captcha by guid
	 * @param string $guid
	 * @return true
	 * @throws \RuntimeException
	 */
	public function clear($guid)
	{
		$repo = new Repository('captcha');
		$captches = $this->getByGuid($guid);
		if ($captches->count() === 0){
			throw new \RuntimeException('Captcha not found');
		}
		foreach($captches as $captcha){
			$repo->delete($this,$captcha);
		}
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
	 * Get captches by guid
	 *
	 * @param ctring $guid
	 * @return CaptchaEntity[]
	 */
	public function getByGuid($guid)
	{
		$filter = [
			'='.CaptchaEntity::C_GUID => $guid,
		];
		return self::finder(CaptchaEntity::getClass())->filter($filter)->all();
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