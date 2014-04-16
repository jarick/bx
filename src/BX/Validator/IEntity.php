<?php namespace BX\Validator;

interface IEntity
{
	public function getLabels();
	public function getLabel($key);
	public function printLabel($key);
	public function getRules();
	public function exists($key);
	public function printValue($key);
	public function getValue($key);
	public function setValue($key,$value);
	public function getOldData();
	public function getData();
	public function setData(array $values,$old = false);
	public function prepareFiles($files);
	public function checkFields(&$data = null,$new = true);
	public function hasErrors();
	/**
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getErrors();
	public function addError($key,$error);
}