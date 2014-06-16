<?php namespace BX\User\Entity;
use BX\Error\Error;
use BX\User\User;

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
	 * @var array
	 */
	protected $filters = [];
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
			$this->rule()->custom([$this,'filterPassword'])->notEmpty()->onAdd(),
			[self::C_CODE],
			$this->rule()->setter()->setFunction([$this,'filterCode'])->setValidators([
				$this->rule()->string()->notEmpty()->setMax(50),
			])->onAdd(),
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
	 * Return rules for filter
	 *
	 * @return array
	 */
	protected function filter()
	{
		return[
			[self::C_ID],
			$this->rule()->number()->integer()->setMin(1),
			[self::C_DISPLAY_NAME,self::C_LOGIN,self::C_EMAIL,self::C_LOGIN,self::C_CODE,self::C_GUID],
			$this->rule()->safe(),
			[self::C_CREATE_DATE,self::C_TIMESTAMP_X],
			$this->rule()->datetime_filter()->filter(function($filter){
				return [
					$filter->min()->withTime()->setFormat('full'),
					$filter->max()->withTime()->setFormat('full')
				];
			})->setMinKey('from')->setMaxKey('to'),
			[self::C_REGISTERED,self::C_ACTIVE],
			$this->rule()->boolean(),
		];
	}
	/**
	 * Filter password
	 *
	 * @param string $value
	 */
	public function filterPassword(&$value)
	{
		$value = User::getHashPassword($value);
		if ($value === false){
			return $this->trans('user.entity.user.error_password_min',['#MIN#' => 6]);
		}
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