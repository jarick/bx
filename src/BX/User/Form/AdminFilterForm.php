<?php namespace BX\User\Form;
use BX\Form\IForm;
use BX\Form\Field\TextField;
use BX\Form\Field\DateField;
use BX\String\Str;

class AdminFilterForm implements IForm
{
	use \BX\Form\FormTrait;
	const C_ID = 'ID';
	const C_GUID = 'GUID';
	const C_LOGIN = 'LOGIN';
	const C_EMAIL = 'EMAIL';
	const C_CODE = 'CODE';
	const C_CREATE_DATE_FROM = 'CREATE_DATE_TO';
	const C_CREATE_DATE_TO = 'CREATE_DATE_FROM';
	const C_TIMESTAMP_X_FROM = 'TIMESTAMP_X_FROM';
	const C_TIMESTAMP_X_TO = 'TIMESTAMP_X_TO';
	const C_REGISTERED = 'REGISTERED';
	const C_ACTIVE = 'ACTIVE';
	const C_DISPLAY_NAME = 'DISPLAY_NAME';
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->setMethod('get');
	}
	/**
	 * Return form name
	 *
	 * @return string
	 */
	public function getFormName()
	{
		return 'FILTER';
	}
	/**
	 * Return array of labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID				 => 'Номер',
			self::C_GUID			 => 'Внешний код',
			self::C_LOGIN			 => 'Логин',
			self::C_EMAIL			 => 'E-Mail',
			self::C_CODE			 => 'Символьный код',
			self::C_CREATE_DATE_FROM => 'Дата создания FROM',
			self::C_CREATE_DATE_TO	 => 'Дата создания TO',
			self::C_TIMESTAMP_X_FROM => 'Дата измененя FROM',
			self::C_TIMESTAMP_X_TO	 => 'Дата измененя TO',
			self::C_REGISTERED		 => 'Регистрация',
			self::C_ACTIVE			 => 'Активность',
			self::C_DISPLAY_NAME	 => 'Имя на сайте',
		];
	}
	/**
	 * Return fields
	 *
	 * @return array
	 */
	protected function fields()
	{
		return[
			self::C_ID				 => $this->field()->number(),
			self::C_GUID			 => $this->field()->text(),
			self::C_LOGIN			 => $this->field()->text(),
			self::C_EMAIL			 => $this->field()->text(),
			self::C_CODE			 => $this->field()->text(),
			self::C_CREATE_DATE_FROM => $this->field()->data(),
			self::C_CREATE_DATE_TO	 => $this->field()->data(),
			self::C_TIMESTAMP_X_FROM => $this->field()->data(),
			self::C_TIMESTAMP_X_TO	 => $this->field()->data(),
			self::C_REGISTERED		 => $this->field()->checkbox(),
			self::C_ACTIVE			 => $this->field()->checkbox(),
			self::C_DISPLAY_NAME	 => $this->field()->text(),
		];
	}
	/**
	 * Return array for filter users
	 *
	 * @return array
	 */
	public function getFilter()
	{
		$return = [];
		if ($this->isValid()){
			foreach($this->fields as $field){
				if ($field instanceof TextField){
					$return['%'.$field->getName()] = $field->getValue();
				}elseif ($field instanceof DateField){
					if (Str::endsWith($field->getName(),'_FROM')){
						$return['>='.$field->getName()] = $field->getValue();
					}elseif (Str::endsWith($field->getName(),'_TO')){
						$return['<='.$field->getName()] = $field->getValue();
					}else{
						$return[$field->getName()] = $field->getValue();
					}
				}
			}
		}
		return $return;
	}
}