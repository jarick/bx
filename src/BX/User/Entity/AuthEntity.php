<?php namespace BX\User\Entity;
use BX\DB\ActiveRecord;

/**
 * @property-read string $id
 * @property string $unique_id
 * @property string $access_token
 * @property integer $user_id
 * @property-read string $create_date
 * @property-read string $timestamp_x
 * @property integer $expire
 * @property-read string $user_login
 * @property-read string $user_email
 */
class AuthEntity extends ActiveRecord
{
	use \BX\Date\DateTrait;
	const C_ID = 'ID';
	const C_UNIQUE_ID = 'UNIQUE_ID';
	const C_ACCESS_TOKEN = 'ACCESS_TOKEN';
	const C_USER_ID = 'USER_ID';
	const C_CREATE_DATE = 'CREATE_DATE';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_EXPIRE = 'EXPIRE';
	const C_USER_LOGIN = 'USER_LOGIN';
	const C_USER_EMAIL = 'USER_EMAIL';
	/**
	 * Get self
	 * @param string|boolean $entity
	 * @param array $params
	 * @return AuthEntity
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
			self::DB_TABLE => 'tbl_auth',
		];
	}
	/**
	 * Get labels
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('user.entity.auth.id'),
			self::C_UNIQUE_ID	 => $this->trans('user.entity.auth.unique_id'),
			self::C_ACCESS_TOKEN => $this->trans('user.entity.auth.access_token'),
			self::C_USER_ID		 => $this->trans('user.entity.auth.user_id'),
			self::C_TIMESTAMP_X	 => $this->trans('user.entity.auth.timestamp_x'),
			self::C_CREATE_DATE	 => $this->trans('user.entity.create_date'),
			self::C_EXPIRE		 => $this->trans('user.entity.expire'),
			self::C_USER_LOGIN	 => $this->trans('user.entity.auth.user_login'),
			self::C_USER_EMAIL	 => $this->trans('user.entity.auth.user_email'),
		];
	}
	/**
	 * Get rules
	 * @return array
	 */
	protected function rules()
	{
		return [
			[
				[self::C_UNIQUE_ID,self::C_ACCESS_TOKEN],
				$this->rule()->string()->notEmpty()->setMax(100)
			],
			[
				[self::C_USER_ID],
				$this->rule()->custom([$this,'validateUserId'])->notEmpty()
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
				[self::C_EXPIRE],
				$this->rule()->number()->setDefault(0)->setMin(0)
			],
		];
	}
	/**
	 * Get columns
	 * @return array
	 */
	protected function columns()
	{
		return [
			self::C_ID			 => $this->column()->int('T.ID'),
			self::C_UNIQUE_ID	 => $this->column()->string('T.UNIQUE_ID'),
			self::C_ACCESS_TOKEN => $this->column()->string('T.ACCESS_TOKEN'),
			self::C_USER_ID		 => $this->column()->int('T.USER_ID'),
			self::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
			self::C_CREATE_DATE	 => $this->column()->datetime('T.CREATE_DATE'),
			self::C_EXPIRE		 => $this->column()->int('T.EXPIRE'),
			self::C_USER_LOGIN	 => $this->column()->string('U.LOGIN'),
			self::C_USER_EMAIL	 => $this->column()->string('U.EMAIL'),
		];
	}
	/**
	 * Get relations
	 * @return array
	 */
	protected function relations()
	{
		return[
			[
				[self::C_USER_ID,self::C_USER_LOGIN,self::C_USER_EMAIL],
				$this->hasMany('U',UserEntity::getEntity())
			],
		];
	}
	/**
	 * Validate use id
	 * @param integer $value
	 * @return null|string
	 */
	public function validateUserId(&$value)
	{
		if (intval($value) <= 0){
			return $this->trans('user.entity.auth.user_is_not_set');
		}
		$user = UserEntity::filter()->select(['ID'])->filter(['ID' => $value,'ACTIVE' => 'Y'])->get();
		if ($user === false){
			return $this->trans('user.entity.auth.user_is_not_found');
		}
	}
}