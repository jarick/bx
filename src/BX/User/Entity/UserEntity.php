<?php namespace BX\User\Entity;

/**
 * @property-read integer $id
 * @property-read string $guid
 * @property string $login
 * @property-read string $password
 * @property sting $email
 * @property string $code
 * @property-read string $create_date
 * @property-read string $timestamp_x
 * @property string $registered
 * @property string $active
 * @property string $display_name
 */
class UserEntity implements \BX\Validator\IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Date\DateTrait,
	 \BX\Translate\TranslateTrait,
	 \BX\Config\ConfigTrait;
	const C_ID = 'ID';
	const C_GUID = 'GUID';
	const C_LOGIN = 'LOGIN';
	const C_PASSWORD = 'PASSWORD';
	const C_EMAIL = 'EMAIL';
	const C_CODE = 'CODE';
	const C_CREATE_DATE = 'CREATE_DATE';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_REGISTERED = 'REGISTERED';
	const C_ACTIVE = 'ACTIVE';
	const C_DISPLAY_NAME = 'DISPLAY_NAME';
	/**
	 * Return labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('user.entity.user.id'),
			self::C_GUID		 => $this->trans('user.entity.user.guid'),
			self::C_LOGIN		 => $this->trans('user.entity.user.login'),
			self::C_PASSWORD	 => $this->trans('user.entity.user.password'),
			self::C_EMAIL		 => $this->trans('user.entity.user.email'),
			self::C_CODE		 => $this->trans('user.entity.user.code'),
			self::C_CREATE_DATE	 => $this->trans('user.entity.user.create_date'),
			self::C_TIMESTAMP_X	 => $this->trans('user.entity.user.timestamp_x'),
			self::C_REGISTERED	 => $this->trans('user.entity.user.registered'),
			self::C_ACTIVE		 => $this->trans('user.entity.user.active'),
			self::C_DISPLAY_NAME => $this->trans('user.entity.user.display_name'),
		];
	}
	/**
	 * Return rules
	 *
	 * @return array
	 */
	protected function rules()
	{
		return [
			[self::C_LOGIN,self::C_EMAIL],
			$this->rule()->string()->notEmpty()->setMax(50),
			[self::C_GUID],
			$this->rule()->setter()->setValue(uniqid('user'))->setValidators([
				$this->rule()->string()->notEmpty()->setMax(50),
			])->onAdd(),
			[self::C_PASSWORD],
			$this->rule()->custom([$this,'filterPassword'])->notEmpty(),
			[self::C_CODE],
			$this->rule()->setter()->setFunction([$this,'filterCode'])->setValidators([
				$this->rule()->string()->notEmpty()->setMax(50),
			]),
			[self::C_CREATE_DATE],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp())->onAdd(),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
			[self::C_DISPLAY_NAME],
			$this->rule()->string()->setMax(100),
			[self::C_REGISTERED,self::C_ACTIVE],
			$this->rule()->boolean(),
		];
	}
	/**
	 * Return min length password
	 *
	 * @return int
	 */
	protected function getMinLengthPassword()
	{
		if ($this->config()->exists('user','password_min_length')){
			return $this->config()->get('user','password_min_length');
		}
		return 6;
	}
	/**
	 * Filter password
	 *
	 * @param string $value
	 */
	public function filterPassword(&$value)
	{
		$value = $this->getValue(self::C_PASSWORD);
		if ($this->string()->length($value) === 0){
			return $this->trans('user.entity.user.error_password_empty');
		}
		$min = $this->getMinLengthPassword();
		if ($this->string()->length($value) < $min){
			return $this->trans('user.entity.user.error_password_min',['#MIN#' => $min]);
		}
		$value = password_hash($value,PASSWORD_BCRYPT);
	}
	/**
	 * Filter code
	 *
	 * @return string
	 */
	public function filterCode()
	{
		$value = $this->getValue(self::C_LOGIN);
		return $this->string()->substr($this->string()->getSlug($value),0,50);
	}
}