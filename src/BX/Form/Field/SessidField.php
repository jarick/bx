<?php namespace BX\Form\Field;
use BX\Form\Field\BaseField;
use BX\Validator\Collection\StringValidator;

class SessidField extends BaseField
{
	use \BX\Http\HttpTrait,
	 \BX\Translate\TranslateTrait;
	/**
	 * Init
	 */
	public function init()
	{
		$this->validator = new StringValidator();
	}
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('',true);
	}
	/**
	 * Set multy value
	 *
	 * @param boolaen $multy
	 * @throws \LogicException
	 */
	public function multy($multy = true)
	{
		throw new \LogicException('Sessid field is not be multy');
	}
	/**
	 * Print html code of field
	 *
	 * @return string
	 */
	public function renderSingle()
	{
		echo '<input type="hidden"'
		.' name="'.$this->string()->escape($this->name).'"'
		.' value="'.$this->string()->escape($this->value).'"'
		.' />';
	}
	/**
	 * Print html code of multy field
	 *
	 * @return string
	 */
	public function renderMulty()
	{
		throw new \RuntimeException('Function in development');
	}
	/**
	 * Return session id
	 *
	 * @return string
	 */
	public function getSessionId()
	{
		return $this->session()->getId();
	}
	/**
	 * Validate value
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function validate(array &$data)
	{
		$sessid = intval($this->value);
		if ($sessid === 0 || $this->session()->getId() !== $sessid){
			$message = $this->trans('form.fields,sessid.error');
			$this->addError($message);
			return false;
		}
		unset($data[$this->name]);
		return true;
	}
}