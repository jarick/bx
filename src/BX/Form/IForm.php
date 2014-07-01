<?php namespace BX\Form;

interface IForm
{
	public function isValid($new = true);
	public function setRequest(\BX\Http\Request $request);
}