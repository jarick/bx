<?php namespace BX\DB;
use BX\Base\Registry;
use Symfony\Component\Yaml\Yaml;

class Schema
{
	use DBTrait;
	/**
	 * Load schema from array
	 * @param array $schema
	 * @return boolean
	 */
	private function process(array $schema)
	{
		$this->transaction()->begin();
		foreach($schema as $table => $data){
			if (!$this->db()->delete($table,'1=1')){
				$this->transaction()->rollback();
				return false;
			}
			$this->db()->adaptor()->resetAI($table);
			if (!$this->db()->add($table,$data)){
				$this->transaction()->rollback();
				return false;
			}
		}
		$this->transaction()->commit();
		return true;
	}
	/**
	 * Load schema from array
	 * @param array $schema
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function load(array $schema)
	{
		if (!$this->process($schema)){
			$error = implode(' ',$this->db()->adaptor()->pdo()->errorInfo());
			throw new \RuntimeException('DB error: '.$error);
		}
		return true;
	}
	/**
	 * Load from yaml file
	 * @param string $file
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public static function loadFromYamlFile($file = null)
	{
		if ($file === null){
			$yaml = Registry::get('schema');
		}else{
			if (!file_exists($file)){
				throw new \RuntimeException("file `$file` is not found");
			}
			$yaml = Yaml::parse(file_get_contents($file));
		}
		if (!empty($yaml)){
			$self = new static();
			return $self->load($yaml);
		}
		return false;
	}
}