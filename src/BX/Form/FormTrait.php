<?php namespace BX\Form;
use BX\Http\Request;
use BX\Form\FieldsDictionary;
use BX\String\Str;
use Illuminate\Support\MessageBag;
use BX\Form\Helper\FieldsHelper;
use BX\Config\DICService;

trait FormTrait
{
	use \BX\Http\HttpTrait;
	/**
	 * @var \BX\Http\Request
	 */
	protected $request = null;
	/**
	 * @var array
	 */
	protected $data;
	/**
	 * @var string
	 */
	protected $method = 'post';
	/**
	 * @var array
	 */
	protected $default = [];
	/**
	 * @var string
	 */
	private $session_token_key = 'SESSID';
	/**
	 * @var FieldsDictionary
	 */
	public $fields = null;
	/**
	 * @var MessageBag
	 */
	protected $error;
	/**
	 * Return form name
	 *
	 * @return string
	 */
	public function getFormName()
	{
		return 'FORM';
	}
	/**
	 * Set method
	 *
	 * @param string $method
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function setMethod($method)
	{
		$method = Str::toLower($method);
		if (!in_array($method,['post','get'])){
			throw new \InvalidArgumentException("Set bad method");
		}
		$this->method = $method;
		return $this;
	}
	/**
	 * Set errror in errors bag
	 *
	 * @return IForm
	 */
	public function addError($key,$message = null)
	{
		if ($message === null){
			$key = 'UNKNOW';
			$message = $key;
		}
		$this->error->add($key,$message);
		if ($this->fields->has($key)){
			$this->fields->get($key)->addError($message);
		}
		return $this;
	}
	/**
	 * Return errror bag
	 *
	 * @return MessageBag
	 */
	public function getErrors()
	{
		return $this->error;
	}
	/**
	 * Return is has errror
	 *
	 * @return boolean
	 */
	public function hasErrors()
	{
		return $this->error->has();
	}
	/**
	 * Set request
	 *
	 * @param \BX\Http\Request $request
	 * @return IForm
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
		return $this;
	}
	/**
	 * Return request
	 *
	 * @return Request
	 */
	protected function getRequest()
	{
		if ($this->request === null){
			$this->request = $this->request();
		}
		return $this->request;
	}
	/**
	 * Set session token key
	 *
	 * @param string $key
	 * @return IForm
	 */
	public function setSessionTokenKey($key)
	{
		$this->session_token_key = $key;
		return $this;
	}
	/**
	 * Return session token key
	 *
	 * @return string
	 */
	public function getSessionTokenKey()
	{
		return $this->session_token_key;
	}
	/**
	 * Set default value
	 *
	 * @param array $default
	 * @return IForm
	 */
	public function setDefault($default)
	{
		$this->default = $default;
		return $this;
	}
	/**
	 * Return array of default values
	 *
	 * @return array
	 */
	public function getDefault()
	{
		return $this->default;
	}
	/**
	 * Return value of field
	 *
	 * @param string $field
	 * @return string
	 */
	public function getValue($field)
	{
		return $this->fields->get($field)->getValue();
	}
	/**
	 * On validate event
	 *
	 * @return boolean
	 */
	protected function onValidate()
	{
		return true;
	}
	/**
	 * Return fields
	 *
	 * @return array
	 */
	protected function fields()
	{
		return [];
	}
	/**
	 * Return fields builder
	 *
	 * @return FieldsHelper
	 */
	public function field()
	{
		$key = 'fields';
		if (DICService::get($key) === null){
			$manager = function(){
				return new FieldsHelper($this->labels());
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Return array of labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [];
	}
	/**
	 * Return fields
	 *
	 * @return Dictionary
	 */
	public function getFields()
	{
		if ($this->method === 'post'){
			$fileds = [
				$this->session_token_key => $this->field()->sessid(),
			];
			return array_merge($fileds,$this->fields());
		}else{
			return $this->fields();
		}
	}
	/**
	 * Validate input data
	 *
	 * @param array $post
	 * @param boolean $new
	 * @return \Illuminate\Support\MessageBag
	 */
	private function checkInputData(array &$post,$new)
	{
		$return = new MessageBag();
		foreach($this->fields as $key => $field){
			if (array_key_exists($key,$post)){
				$field->setValue($post[$key]);
			}
			if (!$field->validate($post,$new)){
				foreach($field->getErrors() as $error){
					$return->add($field->getName(),$error);
				}
				$field->clearErrors();
			}
		}
		return $return;
	}
	/**
	 * Complite fields
	 */
	protected function completeFields()
	{
		$this->fields = new FieldsDictionary();
		$labels = $this->labels();
		foreach($this->getFields() as $key => $field){
			$field->setName($key);
			$field->setFormName($this->getFormName());
			if ($field->getLabel() === null){
				if (array_key_exists($key,$labels)){
					$field->setLabel($labels[$key]);
				}
			}
			$this->fields->add($key,$field);
		}
	}
	/**
	 * Return array data from request
	 *
	 * @return array
	 */
	private function getDataFromRequest()
	{
		if ($this->method === 'post'){
			$post = $this->getRequest()->post()->get($this->getFormName());
		}else{
			$post = $this->getRequest()->query()->get($this->getFormName());
		}
		return $post;
	}
	/**
	 * Return value
	 *
	 * @param string $key
	 * @return string
	 */
	public function __get($key)
	{
		$key = Str::toUpper($key);
		if (!$this->fields->has($key)){
			throw new \InvalidArgumentException("Field `$key` is not exists");
		}
		return $this->fields->get($key);
	}
	/**
	 * Return is exists field
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key)
	{
		$key = Str::toUpper($key);
		return $this->fields->has($key);
	}
	/**
	 * Return label string
	 *
	 * @param string $key
	 * @return string
	 */
	public function getLabel($key)
	{
		$key = Str::toUpper($key);
		if (!$this->fields->has($key)){
			throw new \InvalidArgumentException("Field `$key` is not exists");
		}
		return $this->fields->get($key)->getlabel();
	}
	/**
	 * Check is form data is valid
	 *
	 * @param boolean $new
	 * @return boolean
	 */
	public function isValid($new = true)
	{
		if ($this->fields === null){
			$this->completeFields();
		}
		$post = $this->getDataFromRequest();
		if (is_array($post)){
			$error = $this->checkInputData($post,$new);
			foreach($post as $key => &$value){
				if (!$error->has($key)){
					$func = 'validate';
					foreach(explode('_',$key) as $item){
						$func .= Str::ucwords(Str::toLower($item));
					}
					if (method_exists($this,$func)){
						$return = $this->$func($value);
						if (Str::length($return) > 0){
							$error->add($key,$return);
						}
					}
				}
			}
			unset($value);
			if (!$error->has()){
				if ($this->onValidate()){
					$this->data = $post;
					return true;
				}
			}
			$this->error = $error;
			foreach($error->toArray() as $key => $message){
				$this->fields->get($key)->error[] = $message;
			}
		}else{
			$this->error = new MessageBag();
			foreach($this->default as $key => $value){
				if ($this->fields->has($key)){
					$this->fields->get($key)->setValue($value);
				}
			}
		}
		return false;
	}
	/**
	 * Return access data
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}
}