<?php namespace BX\Form\Field;
use BX\Form\Field\BaseField;
use BX\Validator\Collection\DateTimeValidator;

class DateField extends BaseField
{
	use \BX\Date\DateTrait;
	/**
	 * @var float
	 */
	protected $min = null;
	/**
	 * @var float
	 */
	protected $max = null;
	/**
	 * @var string
	 */
	protected $format = 'full';
	/**
	 * @var string
	 */
	protected $format_rules = 'short';
	/**
	 * Init
	 */
	public function init()
	{
		$this->validator = new DateTimeValidator();
	}
	/**
	 * Constructor
	 *
	 * @param string $label
	 * @param string $required
	 * @param string $format
	 */
	public function __construct($label,$required = false,$format = 'full')
	{
		parent::__construct($label,$required);
		$this->format = $format;
	}
	/**
	 * Return format
	 *
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}
	/**
	 * Set format
	 *
	 * @param string $format
	 * @return DateField
	 */
	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}
	/**
	 * Return format of rules
	 *
	 * @return string
	 */
	public function getFormatRules()
	{
		return $this->format_rules;
	}
	/**
	 * Set format of rules
	 *
	 * @param string $format_rules
	 * @return DateField
	 */
	public function setFormatRules($format_rules)
	{
		$this->format_rules = $format_rules;
		return $this;
	}
	/**
	 * Set max data
	 *
	 * @param string $max
	 * @return DateField
	 */
	public function setMax($max)
	{
		$this->max = $max;
		return $this;
	}
	/**
	 * Return max date
	 *
	 * @return string
	 */
	public function getMax()
	{
		return $this->max;
	}
	/**
	 * Set min date
	 *
	 * @param string $min
	 * @return DateField
	 */
	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}
	/**
	 * Return min date
	 */
	public function getMin()
	{
		return $this->min;
	}
	/**
	 * Validate value
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function validate(array &$data)
	{
		if ($this->min !== null){
			$this->validator->setMin($this->min);
		}
		if ($this->max !== null){
			$this->validator->setMax($this->max);
		}
		$this->validator->setFormat($this->getFormat());
		$this->validator->setFormatRules($this->getFormatRules());
		return parent::validate($data);
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
		.' name="'.$this->string()->escape($this->getFullName()).'"'
		.' id="'.$this->getId().'"'
		.' tabindex='.$this->tabindex
		.' placeholder="'.$placeholder.'"'
		.' value="'.$this->string()->escape($this->value).'"'
		.' />';
		echo '</div>';
	}
	/**
	 * Print html code of field
	 *
	 * @return string
	 */
	public function renderMulty()
	{
		throw new \RuntimeException('Function in development');
	}
}