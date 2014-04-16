<?php namespace BX;

class Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * Call protected/private method of a class.
	 *
	 * @param object &$object    Instantiated object that we will run method on.
	 * @param string $methodName Method name to call
	 * @param array  $parameters Array of parameters to pass into method.
	 *
	 * @return mixed Method return.
	 */
	public function invokeMethod(&$object,$methodName,$parameters = array())
	{
		$reflection = new \ReflectionClass(get_class($object));
		$method = $reflection->getMethod($methodName);
		$method->setAccessible(true);
		$parameters = func_get_args();
		return $method->invokeArgs($object,array_slice($parameters,2));
	}
	/**
	 * Set value private property
	 * @param mixed $object
	 * @param string $propertyName
	 * @param mixed $value
	 */
	public function setPropertyValue(&$object,$propertyName,$value)
	{
		$refObject = new \ReflectionObject($object);
		$refProperty = $refObject->getProperty($propertyName);
		$refProperty->setAccessible(true);
		$refProperty->setValue(null,$value);
	}
}