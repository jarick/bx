<?php namespace BX\Form\Field;
use BX\Validator\Collection\MultyValidator;

abstract class BaseField
{
	use \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait;
	/**
	 * @var string
	 */
	protected $label = null;
	/**
	 * @var mixed
	 */
	protected $value = null;
	/**
	 * @var boolean
	 */
	protected $required;
	/**
	 * @var integer
	 */
	protected $tabindex;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var \BX\Validator\Collection\BaseValidator
	 */
	protected $validator;
	/**
	 * @var boolean
	 */
	protected $multy = false;
	/**
	 * Construct
	 *
	 * @param string $label
	 * @param string $required
	 */
	public function __construct($label = null,$required = false)
	{
		$this->label = $label;
		$this->required = $required;
		$this->init();
	}
	/**
	 * Init
	 */
	public function init()
	{

	}
	/**
	 * Set label
	 *
	 * @param string $label
	 * @return \BX\Form\Field\BaseField
	 */
	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}
	/**
	 * Return label
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}
	/**
	 * Print escape label
	 */
	public function printLabel()
	{
		echo $this->string()->escape($this->label);
	}
	/**
	 * Set value
	 *
	 * @param string $value
	 * @return \BX\Form\Field\BaseField
	 */
	public function setValue($value)
	{
		if (is_array($value)){
			foreach($value as &$item){
				$item = trim($item);
			}
			unset($item);
		}else{
			$value = trim($value);
		}
		$this->value = $value;
		return $this;
	}
	/**
	 * Return value
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
	/**
	 * Print value
	 */
	public function printValue()
	{
		echo $this->string()->escape($this->value);
	}
	/**
	 * Set name
	 *
	 * @param string $name
	 * @return \BX\Form\Field\BaseField
	 */
	public function setName($name)
	{
		if ($this->string()->length($name) === 0){
			throw new \InvalidArgumentException('Name is not set');
		}
		$this->name = $this->string()->toUpper($name);
		return $this;
	}
	/**
	 * Return name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	/**
	 * Print name
	 */
	public function printName()
	{
		echo $this->string()->escape($this->name);
	}
	/**
	 * Set tab index
	 *
	 * @param integer $tabindex
	 * @return \BX\Form\Field\BaseField
	 */
	public function setTabindex($tabindex)
	{
		$this->tabindex = intval($tabindex);
		return $this;
	}
	/**
	 * Return tab index
	 *
	 * @return integer
	 */
	public function getTabindex()
	{
		return $this->tabindex;
	}
	/**
	 * Print tab index
	 */
	public function printTabIndex()
	{
		echo $this->string()->escape($this->tabindex);
	}
	/**
	 * Return id for input
	 *
	 * @return string
	 */
	public function getId()
	{
		return 'field-'.$this->tabindex;
	}
	/**
	 * Not empty
	 *
	 * @param boolean $required
	 * @return \BX\Form\Field\BaseField
	 */
	public function required($required = true)
	{
		$this->required = $required;
		return $this;
	}
	/**
	 * Add error
	 *
	 * @param string $message
	 * @return BaseField
	 */
	public function addError($message)
	{
		$this->validator->addError($this->string()->toUpper($this->name),$message);
		return $this;
	}
	/**
	 * Clear errors
	 *
	 * @return BaseField
	 */
	public function clearErrors()
	{
		$this->validator->clearErrors();
		return $this;
	}
	/**
	 * Return errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->validator->getErrors()->all();
	}
	/**
	 * Return is exists errors
	 *
	 * @return boolean
	 */
	public function hasErrors()
	{
		return $this->validator->getErrors()->has();
	}
	/**
	 * Print html code of field
	 */
	public function __toString()
	{
		$this->render();
	}
	/**
	 * Set field is multy
	 *
	 * @param boolean $multy
	 * @return \BX\Form\Field\BaseField
	 */
	public function multy($multy = true)
	{
		$this->multy = $multy;
		return $this;
	}
	/**
	 * Is empty
	 * @param string $value
	 * @param boolean $trim
	 * @return bollean
	 */
	public function isEmpty($value,$trim = false)
	{
		return $value === null || $value === array() || $value === '' || $trim && is_scalar($value) && trim($value) === '';
	}
	/**
	 * Return class name
	 *
	 * @return string
	 */
	public static function getClass()
	{
		return get_called_class();
	}
	/**
	 * Validate input data
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function validate(array &$data)
	{
		$this->validator->notEmpty($this->required);
		if ($this->multy){
			$this->validator = MultyValidator::create($this->validator);
		}
		if (!$this->validator->validate($this->name,$this->value,$this->label,$data)){
			foreach($this->validator->getErrors()->all() as $message){
				$this->addError($message);
			}
			return false;
		}
		return true;
	}
	/**
	 * Set validator
	 *
	 * @param callable $callback
	 */
	public function setValidator($callback)
	{
		call_user_func($callback,$this->validator,$this);
	}
	/**
	 * Render field
	 *
	 * @return \BX\Form\Field\BaseField
	 */
	public function render()
	{
		if ($this->multy){
			$this->renderMulty();
		}else{
			$this->renderSingle();
		}
		return $this;
	}
	abstract public function renderSingle();
	abstract public function renderMulty();
}