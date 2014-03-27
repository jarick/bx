<?php namespace BX\User\Entity;
use BX\DB\ActiveRecord;
use BX\Validator\Manager\String;
use BX\Validator\Manager\Custom;
use BX\Validator\Manager\Setter;
use BX\Validator\Manager\Boolean;
use BX\DB\Column\StringColumn;
use BX\DB\Column\TimestampColumn;
use BX\DB\Column\BooleanColumn;
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
	use \BX\String\StringTrait,
	 \BX\Date\DateTrait;
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
			[[self::C_LOGIN,self::C_EMAIL],String::create()->notEmpty()->setMax(50)],
			[[self::C_PASSWORD],Custom::create([$this,'validatePassword'])->notEmpty()],
			[[self::C_CODE],Setter::create()->setFunction([$this,'filterCode'])->setValidator(
					String::create()->notEmpty()->setMax(50)
				)
			],
			[[self::C_CREATE_DATE],Setter::create()->setValue($this->date()->convertTimeStamp())->onAdd()],
			[[self::C_TIMESTAMP_X],Setter::create()->setValue($this->date()->convertTimeStamp())],
			[[self::C_DISPLAY_NAME],String::create()->setMax(100)],
			[[self::C_URL],String::create()->setMax(255)],
			[[self::C_REGISTERED,self::C_ACTIVE],Boolean::create()],
		];
	}
	protected function columns()
	{
		return [
			self::C_LOGIN			 => StringColumn::create('T.Login'),
			self::C_PASSWORD		 => StringColumn::create('T.PASSWORD'),
			self::C_EMAIL			 => StringColumn::create('T.EMAIL'),
			self::C_CODE			 => StringColumn::create('T.CODE'),
			self::C_CREATE_DATE		 => TimestampColumn::create('T.CREATE_DATE'),
			self::C_TIMESTAMP_X		 => TimestampColumn::create('T.TIMESTAMP_X'),
			self::C_DISPLAY_NAME	 => StringColumn::create('T.DISPLAY_NAME'),
			self::C_URL				 => StringColumn::create('T.URL'),
			self::C_REGISTERED		 => BooleanColumn::create('T.REGISTERED'),
			self::C_ACTIVATION_KEY	 => StringColumn::create('T.ACTIVATION_KEY'),
			self::C_ACTIVE			 => BooleanColumn::create('T.ACTIVE'),
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
	/**
	 *
	 * @param string|integer $id
	 */
	public static function findIdentity($id)
	{

	}
	/**
	 *
	 */
	public static function findIdentityByAccessToken($token)
	{

	}
	/**
	 */
	public function getId()
	{

	}
	/**
	 */
	public function getAuthKey()
	{

	}
	/**
	 */
	public function validateAuthKey($authKey)
	{

	}
}