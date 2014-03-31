<?php namespace BX\DB\Adaptor;
use BX\Registry;
use BX\Object;
use BX\DI;

class DbAdaptor extends Object
{
	/**
	 * Get pdo
	 * @return \PDO
	 */
	public function pdo()
	{
		if (DI::get('pdo') === null){
			$dsn = (Registry::exists('pdo','dsn')) ? Registry::get('pdo','dsn') : 'sqlite:memory';
			$username = (Registry::exists('pdo','username')) ? Registry::get('pdo','username') : '';
			$passwd = (Registry::exists('pdo','passwd')) ? Registry::get('pdo','passwd') : '';
			$options = (Registry::exists('pdo','options')) ? Registry::get('pdo','options') : [];
			$pdo = new \PDO($dsn,$username,$passwd,$options);
			$pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
			DI::set('pdo',$pdo);
		}
		return DI::get('pdo');
	}
	/**
	 * Execute sql
	 * @param string $sql
	 * @param array $vars
	 * @return boolean
	 */
	public function execute($sql,$vars = [])
	{
		$query = $this->pdo()->prepare($sql);
		if ($query === false){
			throw new \PDOException("Error sql query :`$sql`");
		}
		return $query->execute($vars);
	}
	/**
	 * Get last insert id
	 * @return integer
	 */
	public function getLastId()
	{
		return $this->pdo()->lastInsertId();
	}
	/**
	 * Query sql
	 * @param string $sql
	 * @param array $vars
	 * @return array
	 */
	public function query($sql,$vars = [])
	{
		$query = $this->pdo()->prepare($sql);
		if ($query === false){
			throw new \PDOException("Error sql query :`$sql`");
		}
		if ($query->execute($vars)){
			return $query->fetchAll();
		} else{
			throw new \PDOException("Error sql query :`$sql`");
		}
	}
	/**
	 * Drop table
	 * @param string $table
	 */
	public function dropTable($table)
	{
		return $this->execute('DROP TABLE '.$table);
	}
	/**
	 * Get sql start transaction
	 * @return string
	 */
	public function startTransaction()
	{
		return 'BEGIN TRANSACTION';
	}
	/**
	 * Get sql commit transaction
	 * @return string
	 */
	public function commitTransaction()
	{
		return 'COMMIT TRANSACTION';
	}
	/**
	 * Get sql rollback transaction
	 * @return string
	 */
	public function rollbackTransaction()
	{
		return 'ROLLBACK TRANSACTION';
	}	
}