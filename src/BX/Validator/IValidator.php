<?php namespace BX\Validator;

interface IValidator
{
	public function validateField($key,&$fields,$label);
	public function getErrors();
}