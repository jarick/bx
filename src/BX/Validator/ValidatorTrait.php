<?php namespace BX\Validator;

trait ValidatorTrait
{
	/**
	 * Get validator
	 * @param array $rules
	 * @param array $labels
	 * @param array $new
	 * @return Validator
	 */
	protected function validator(array $rules,array $labels,$new = true)
	{
		return new Validator($labels,$rules,$new);
	}
}