<?php namespace BX\Form;
use BX\Base\Dictionary;
use BX\Form\Field\BaseField;
use Illuminate\Support\MessageBag;

class FieldsDictionary extends Dictionary
{
	public function __construct()
	{
		parent::__construct(BaseField::getClass());
	}
	/**
	 * Return is has errors
	 *
	 * @return boolean
	 */
	public function hasErrors()
	{
		foreach($this->array as $field){
			if ($field->hasErrors()){
				return true;
			}
		}
		return false;
	}
	/**
	 * Return errors
	 *
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getErrors()
	{
		$return = new MessageBag();
		foreach($this->array as $key => $field){
			foreach($field->getErrors() as $message){
				$return->add($key,$message);
			}
		}
		return $return;
	}
}