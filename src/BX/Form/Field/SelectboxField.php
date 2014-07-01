<?php namespace BX\Form\Field;
use BX\Form\Field\BaseField;
use BX\Validator\Collection\SafeValidator;

class SelectboxField extends BaseField
{
	/**
	 * @var array
	 */
	private $enums = [];
	/**
	 * Constructor
	 *
	 * @param string $label
	 * @param boolean $required
	 * @param array $enums
	 */
	public function __construct($label = null,$required = false,array $enums = [])
	{
		parent::__construct($label,$required);
		$this->enums = $enums;
	}
	/**
	 * Init
	 */
	public function init()
	{
		$this->validator = new SafeValidator();
	}
	/**
	 * Set enums
	 *
	 * @param array $enums
	 * @return \BX\Form\Field\TextField
	 */
	public function setEnums(array $enums)
	{
		$this->enums = $enums;
		return $this;
	}
	/**
	 * Return array of enums
	 *
	 * @return arrray
	 */
	public function getEnums()
	{
		return $this->enums;
	}
	/**
	 * Print html code of field
	 *
	 * @return string
	 */
	public function renderSingle($css_class = '',$placeholder = '')
	{
		echo '<div class="form-group '.$css_class;
		if ($this->hasErrors()){
			echo ' has-error';
		}
		echo '">';
		echo '<label class="control-label" for="'.$this->getId().'">'
		.$this->string()->escape($this->label);
		if ($this->required){
			echo '<span class="text-red">*</span>';
		}
		echo '</label>'
		.'<input type="datetime" class="form-control"'
		.' name="'.$this->string()->escape($this->name).'"'
		.' id="'.$this->getId().'"'
		.' tabindex='.$this->tabindex
		.' placeholder="'.$placeholder.'"'
		.' value="'.$this->value.'"'
		.' />';
		echo '</div>';
	}
	/**
	 * Print html code of multy field
	 *
	 * @return string
	 */
	public function renderMulty()
	{

	}
	/**
	 * Validate value
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function validate(&$data)
	{
		if ($this->isEmpty($this->value)){
			if ($this->required){
				$error = $this->trans('form.field.select.is_empty',[
					'#LABEL#' => $this->label,
				]);
				$this->addError($error);
				return false;
			}else{
				return true;
			}
		}
		if (!array_key_exists($this->value,$this->enums)){
			$error = $this->trans('form.field.select.invalid',[
				'#LABEL#' => $this->label,
			]);
			$this->addError($error);
			return false;
		}
		return true;
	}
}