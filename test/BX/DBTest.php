<?php namespace BX;

class DBTest extends \PHPUnit_Extensions_Database_TestCase
{
	use \BX\DB\DBTrait;
	/**
	 * Return db schema
	 * @return PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet
	 */
	protected function getDataSet()
	{
		return $this->createFlatXMLDataSet(dirname(__DIR__).'/data/schema.xml');
	}
	/**
	 * On connection to db
	 * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 */
	protected function getConnection()
	{
		$db = dirname(__DIR__).'/data/db.db';
		$pdo = new \PDO("sqlite:{$db}");
		$pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
		DI::set('pdo',$pdo);
		return $this->createDefaultDBConnection($pdo);
	}
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
}