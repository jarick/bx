<?php namespace BX\Mutex\Entity;

/**
 * @property integer $max_acquire
 * @property string $name
 * @property-read string $key
 * @property string $permission
 */
class MutexEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Translate\TranslateTrait;
	const C_MAX_ACQUIRE = 'MAX_ACQUIRE';
	const C_NAME = 'NAME';
	const C_KEY = 'KEY';
	const C_PERMISSION = 'PERMISSION';
	/**
	 * @var array|null
	 */
	private $meta = null;
	/**
	 * Set meta data
	 * @param array $meta
	 * @return MutexEntity
	 */
	public function setMeta(array $meta)
	{
		$this->meta = $meta;
		return $this;
	}
	/**
	 * Get meta data
	 * @return array|null
	 */
	public function getMeta()
	{
		return $this->meta;
	}
	/**
	 * Get labels
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_MAX_ACQUIRE	 => $this->trans('mutes.entity.mutes.max_acquire'),
			self::C_NAME		 => $this->trans('mutes.entity.mutes.name'),
			self::C_KEY			 => $this->trans('mutes.entity.mutes.key'),
			self::C_PERMISSION	 => $this->trans('mutes.entity.mutes.permission'),
		];
	}
	/**
	 * Get rules
	 * @return array
	 */
	protected function rules()
	{
		return[
			[self::C_MAX_ACQUIRE],
			$this->rule()->number()->integer()->notEmpty()->setMin(1)->setDefault(1),
			[self::C_NAME],
			$this->rule()->string()->notEmpty(),
			[self::C_KEY],
			$this->rule()->setter()->setFunction(function(){
				$name = $this->getValue(self::C_NAME);
				if ($this->string()->length($name) > 0){
					return abs(crc32($name));
				}
			}),
			[self::C_PERMISSION],
			$this->rule()->custom(function($value){
				if (!preg_match('/^\d\d\d\d$/',sprintf('%04d',$value))){
					return 'Permision has invalide formate';
				}
			})->notEmpty()->setDefault(0666),
		];
	}
	/**
	 * Generate semaphor
	 * @param string $key
	 * @param integer $max_acquire
	 * @return \BX\Mutex\Entity\MutexEntity
	 * @throws \RuntimeException
	 */
	public function generate($key,$max_acquire = 1)
	{
		$this->name = $key;
		$this->max_acquire = $max_acquire;
		$data = null;
		if ($this->checkFields($data)){
			$this->setData($data);
		}else{
			$error = 'Error generate mutex: ('.implode(',',$this->getErrors()->all()).')';
			throw new \RuntimeException($error);
		}
		return $this;
	}
}