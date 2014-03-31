<?php namespace BX\User\Entity;
use BX\DB\ActiveRecord;
use BX\Registry;

/**
 * @property-read string $id
 * @property string $login
 * @property-read string $password
 * @property sting $email
 * @property string $code
 * @property-read string $create_date
 * @property-read string $timestamp_x
 * @property string $url
 * @property string $registered
 * @property string $activation_key
 * @property string $active
 * @property string $display_name
 */
class UserEntity extends ActiveRecord
{
	use \BX\Date\DateTrait;
	const C_ID = 'ID';
	const C_LOGIN = 'LOGIN';
	const C_PASSWORD = 'PASSWORD';
	const C_EMAIL = 'EMAIL';
	const C_CODE = 'CODE';
	const C_CREATE_DATE = 'CREATE_DATE';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_URL = 'URL';
	const C_REGISTERED = 'REGISTERED';
	const C_ACTIVATION_KEY = 'ACTIVATION_KEY';
	const C_ACTIVE = 'ACTIVE';
	const C_DISPLAY_NAME = 'DISPLAY_NAME';
	/**
	 * Get self
	 * @param string|boolean $entity
	 * @param array $params
	 * @return UserEntity
	 */
	static public function getEntity($entity = false,$params = [])
	{
		$instance = static::autoload($entity,'entities',$params);
		$instance->init();
		return $instance;
	}
	/**
	 * Get settings
	 * @return array
	 */
	protected function settings()
	{
		return [
			self::DB_TABLE	 => 'tbl_user',
			self::CACHE_TAG	 => 'user',
			self::EVENT		 => 'user',
			#const UF_ENTITY = 'uf_entity';
			#const PERMISSION_BINDING = 'permission_binding';
			#const PERMISSION_TABLE = 'permission_table';
		];
	}
	protected function labels()
	{
		return [
			self::C_ID				 => $this->trans('user.entity.user.id'),
			self::C_LOGIN			 => $this->trans('user.entity.user.login'),
			self::C_PASSWORD		 => $this->trans('user.entity.user.password'),
			self::C_EMAIL			 => $this->trans('user.entity.user.email'),
			self::C_CODE			 => $this->trans('user.entity.user.code'),
			self::C_CREATE_DATE		 => $this->trans('user.entity.user.create_date'),
			self::C_TIMESTAMP_X		 => $this->trans('user.entity.user.timestamp_x'),
			self::C_URL				 => $this->trans('user.entity.user.url'),
			self::C_REGISTERED		 => $this->trans('user.entity.user.registered'),
			self::C_ACTIVATION_KEY	 => $this->trans('user.entity.user.activation_key'),
			self::C_ACTIVE			 => $this->trans('user.entity.user.active'),
			self::C_DISPLAY_NAME	 => $this->trans('user.entity.user.display_name'),
		];
	}
	protected function rules()
	{
		return [
			[
				[self::C_LOGIN,self::C_EMAIL],
				$this->rule()->string()->notEmpty()->setMax(50)
			],
			[
				[self::C_PASSWORD],
				$this->rule()->custom([$this,'validatePassword'])->notEmpty()
			],
			[
				[self::C_CODE],
				$this->rule()->setter()->setFunction([$this,'filterCode'])->setValidator(
					$this->rule()->string()->notEmpty()->setMax(50)
				)
			],
			[
				[self::C_CREATE_DATE],
				$this->rule()->setter()->setValue($this->date()->convertTimeStamp())->onAdd()
			],
			[
				[self::C_TIMESTAMP_X],
				$this->rule()->setter()->setValue($this->date()->convertTimeStamp())
			],
			[
				[self::C_DISPLAY_NAME],
				$this->rule()->string()->setMax(100)
			],
			[
				[self::C_URL],
				$this->rule()->string()->setMax(255)
			],
			[
				[self::C_REGISTERED,self::C_ACTIVE],
				$this->rule()->boolean()
			],
		];
	}
	protected function columns()
	{
		return [
			self::C_ID				 => $this->column()->int('T.ID'),
			self::C_LOGIN			 => $this->column()->string('T.Login'),
			self::C_PASSWORD		 => $this->column()->string('T.PASSWORD'),
			self::C_EMAIL			 => $this->column()->string('T.EMAIL'),
			self::C_CODE			 => $this->column()->string('T.CODE'),
			self::C_CREATE_DATE		 => $this->column()->datetime('T.CREATE_DATE'),
			self::C_TIMESTAMP_X		 => $this->column()->datetime('T.TIMESTAMP_X'),
			self::C_DISPLAY_NAME	 => $this->column()->string('T.DISPLAY_NAME'),
			self::C_URL				 => $this->column()->string('T.URL'),
			self::C_REGISTERED		 => $this->column()->bool('T.REGISTERED'),
			self::C_ACTIVATION_KEY	 => $this->column()->string('T.ACTIVATION_KEY'),
			self::C_ACTIVE			 => $this->column()->bool('T.ACTIVE'),
		];
	}
	protected function getMinLengthPassword()
	{
		if (Registry::exists('user','password_min_length')){
			return Registry::get('user','password_min_length');
		}
		return 6;
	}
	public function validatePassword(&$value)
	{
		if ($this->string()->length($value) === 0){
			return $this->trans('user.entity.user.error_password_empty');
		}
		$min = $this->getMinLengthPassword();
		if ($this->string()->length($value) < $min){
			return $this->trans('user.entity.user.error_password_min',['#MIN#' => $min]);
		}
		$value = password_hash($value,PASSWORD_BCRYPT);
	}
	public function filterCode()
	{
		return $this->string()->substr($this->string()->getSlug($this->getValue(self::C_LOGIN)),0,50);
	}
}