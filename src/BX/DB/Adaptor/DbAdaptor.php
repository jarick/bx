<?php namespace BX\DB\Adaptor;
use BX\Base\Registry;
use BX\Base\DI;

class DbAdaptor
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
			return false;
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
			return false;
		}
		if ($query->execute($vars)){
			return $query->fetchAll(\PDO::FETCH_ASSOC);
		}else{
			return false;
		}
	}
}