<?php namespace BX\DB\Adaptor;
use BX\Config\Config;
use BX\Config\DICService;

class DbAdaptor
{
	use \BX\Config\ConfigTrait;
	/**
	 * Return pdo object
	 *
	 * @return \PDO
	 */
	public function pdo()
	{
		if (DICService::get('pdo') === null){
			$ptr = function(){
				$dsn = ($this->config()->exists('pdo','dsn')) ? $this->config()->get('pdo','dsn') : 'sqlite:memory';
				$username = ($this->config()->exists('pdo','username')) ? $this->config()->get('pdo','username') : '';
				$passwd = ($this->config()->exists('pdo','passwd')) ? $this->config()->get('pdo','passwd') : '';
				$options = ($this->config()->exists('pdo','options')) ? $this->config()->get('pdo','options') : [];
				return new \PDO($dsn,$username,$passwd,$options);
			};
			DICService::set('pdo',$ptr);
		}
		return DICService::get('pdo');
	}
	/**
	 * Execute sql
	 *
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
	 * Return last insert id
	 *
	 * @return integer
	 */
	public function getLastId()
	{
		return $this->pdo()->lastInsertId();
	}
	/**
	 * Query sql
	 *
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