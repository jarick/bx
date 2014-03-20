<?php namespace BX\DB\Filter;
use BX\Object;
use BX\DB\IDatabase;
use BX\DB\Adaptor\IAdaptor;
use BX\DB\Manager\DBResult;

class SqlBuilder extends Object
{
	use \BX\String\StringTrait,
	 \BX\Cache\CacheTrait{
		cache as protected cacheManager;
	}
	/**
	 * @var array
	 */
	private $vars = [];
	/**
	 * @var array
	 */
	private $columns = [];
	/**
	 * @var array
	 */
	private $filter_sql = [];
	/**
	 * @var array
	 */
	private $sort_sql = [];
	/**
	 * @var array
	 */
	private $group_sql = [];
	/**
	 * @var array
	 */
	private $select_sql = [];
	/**
	 * @var integer
	 */
	private $limit = null;
	/**
	 * @var integer
	 */
	private $offset = null;
	/**
	 * @var boolean
	 */
	private $distinct = false;
	/**
	 * @var array
	 */
	private $cache = null;
	/**
	 * @var array
	 */
	private $cache_tags = [];
	/**
	 * @var array
	 */
	private $filter_rule;
	/**
	 * @var array
	 */
	private $fields;
	/**
	 * @var array
	 */
	private $relation;
	/**
	 * @var \BX\DB\IDatabase $database
	 */
	private $database;
	/**
	 * @var string
	 */
	private $table;
	/**
	 * @var callable
	 */
	private $callback;
	const CACHE_KEY_TIME = 'time';
	const CACHE_KEY_NS = 'namespace';
	const CACHE_PREFIX = '__SQL_BUILDER__';
	/**
	 * Constructor
	 * @param \BX\DB\IDatabase $database
	 * @param string $table
	 * @param array $fields
	 * @param array $filter_rule
	 * @param array $relation
	 */
	public function __construct(IDatabase $database,$table,array $fields,array $filter_rule = [],array $relation = [],$callback = false)
	{
		$this->table = $table;
		$this->database = $database;
		$this->fields = $fields;
		$this->filter_rule = $filter_rule;
		$this->relation = $relation;
		$this->callback = $callback;
	}
	/**
	 * Get database adaptor
	 * @return IAdaptor
	 * */
	public function adaptor()
	{
		return $this->database->adaptor();
	}
	/**
	 * Get DB Manager
	 * @return IDatabase
	 */
	public function db()
	{
		return $this->database;
	}
	/**
	 * Bind param
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	public function bindParam($key,$value)
	{
		$key = $this->string()->toLower($key);
		$i = 0;
		while (array_key_exists($key.'_'.$i,$this->vars)){
			$i++;
		}
		$key = $key.'_'.$i;
		$this->vars[$key] = strval($value);
		return ':'.$key;
	}
	/**
	 * Set limit
	 * @param integer $limit
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}
	/**
	 * Set offset
	 * @param integer $offset
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function offset($offset)
	{
		$this->offset = $offset;
		return $this;
	}
	/**
	 * Distinct select
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function distinct()
	{
		$this->distinct = true;
		return $this;
	}
	/**
	 * Escape column for SQL and save column name for loader
	 * @param string $key
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function getColumn($key)
	{
		if (array_key_exists($key,$this->fields)){
			if (!in_array($key,$this->columns)){
				$this->columns[] = $key;
			}
			return $this->fields[$key];
		} else{
			throw new \InvalidArgumentException("Field `$key` is not found");
		}
	}
	/**
	 * Set data for GROUP BY operator
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function group()
	{
		$aGroup = func_get_args();
		if (count($aGroup) === 1 && isset($aGroup[0]) && is_array($aGroup[0])){
			$aGroup = $aGroup[0];
		}
		if ($aGroup !== false && !empty($aGroup)){
			foreach ($aGroup as $key){
				$this->group_sql[] = $this->getColumn($key);
			}
		}
		$this->group_sql = array_unique($this->group_sql);
		return $this;
	}
	/**
	 * Set sorting
	 * @param array $aSort
	 * @return \BX\DB\Filter\SqlBuilder
	 * @throws \InvalidArgumentException
	 */
	public function sort(array $aSort = [])
	{
		foreach ($aSort as $key => $value){
			$value = $this->string()->toLower($value);
			if (in_array($value,['asc','desc'],true)){
				$this->sort_sql[] = $this->getColumn($key).' '.$value;
			} else{
				throw new \InvalidArgumentException('Sorting must be asc or desc');
			}
		}
		$this->sort_sql = array_unique($this->sort_sql);
		return $this;
	}
	/**
	 * Set filter
	 * @param array $aFilter
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function filter(array $aFilter = [])
	{
		$oBlock = new LogicBlock($this,$this->filter_rule);
		$this->filter_sql[] = $oBlock->toSql($aFilter);
		return $this;
	}
	/**
	 * Get sql functions
	 * @return array
	 */
	private function getGroupByFunctionArray()
	{
		return ['COUNT','AVG','MIN','MAX','SUM'];
	}
	/**
	 * Select array
	 * @param $select array
	 * @return \BX\DB\Filter\SqlBuilder
	 * @throws \InvalidArgumentException
	 */
	public function select()
	{
		$select = func_get_args();
		if (count($select) === 1 && isset($select[0]) && is_array($select[0])){
			$select = $select[0];
		}
		$all = false;
		if (in_array('*',$select)){
			$all = true;
			foreach ($this->fields as $key => $value){
				$this->select_sql[] = $this->getColumn($key).' as '.$this->db()->esc($key);
			}
		}
		foreach ($select as $key => $value){
			if ($value === '*'){
				continue;
			}
			if (is_numeric($key)){
				if ($all === false){
					$this->select_sql[] = $this->getColumn($value).' as '.$this->db()->esc($value);
				}
			} else{
				$value = $this->string()->toUpper($value);
				$function = $this->getGroupByFunctionArray();
				if (in_array($value,$function)){
					$this->select_sql[] = $value.'('.$this->getColumn($key).') as '.$this->db()->esc($value.'_'.$key);
				} else{
					throw new \InvalidArgumentException("Agreegate function must be ".
					implode('|',$this->getGroupByFunctionArray())." set `$value`");
				}
			}
		}
		$this->select_sql = array_unique($this->select_sql);
		return $this;
	}
	/**
	 * Prepare relation array
	 * @param array $relation_array
	 * @return array
	 */
	private function getRelationArray(array $relation_array)
	{
		$result = [];
		$relations = [];
		$keys = [];
		$i = 0;
		foreach ($relation_array as $column => $relation){
			if (is_string($relation)){
				$columns = explode(',',$column);
				$relation = $relation;
			} else{
				$columns = (array) $relation[0];
				$relation = $relation[1];
			}
			foreach ($columns as $key){
				$keys[$key] = $i;
			}
			$relations[$i] = $relation;
			$i++;
		}
		foreach ($this->columns as $column){
			if (isset($keys[$column]) && isset($relations[$keys[$column]])){
				$result[] = $relations[$keys[$column]];
				unset($relations[$keys[$column]]);
			}
		}
		return $result;
	}
	public function all()
	{
		if ($this->callback !== false){
			return call_user_func_array($this->callback,[$result->getData()]);
		} else{
			return $result;
		}
	}
	/**
	 * Find all rows
	 * @return DBResult
	 */
	public function asArray()
	{
		$sql = $this->getSql();
		if (is_array($this->cache)){
			$cache_key = self::CACHE_PREFIX.md5(self::CACHE_PREFIX.$sql.serialize($this->vars));
			$time = $this->cache[self::CACHE_KEY_TIME];
			$ns = $this->cache[self::CACHE_KEY_NS];
			$cache_result = $this->cacheManager()->get($cache_key,$ns);
			if ($cache_result === null){
				$data = $this->query($sql);
				$cache = $this->cacheManager();
				$tags = $this->cache_tags;
				if (!empty($tags)){
					$tags = $cache->setTags($ns,$this->cache_tags);
				}
				$cache->set($cache_key,$data->getData(),$ns,$time,$tags);
				return $data;
			} else{
				$result = DBResult::getManager(false,['result' => $cache_result]);
			}
		} else{
			$result = $this->query($sql);
		}
		return $result;
	}
	/**
	 * Find first row
	 * @return DBResult
	 */
	public function get()
	{
		return $this->limit(1)->all();
	}
	/**
	 * Send query
	 * @param string $sql
	 * @return DBResult
	 */
	private function query($sql)
	{
		return $this->db()->query($sql,$this->vars);
	}
	/**
	 * Compile and send sql
	 * @param boolean $sMd5 key for cache
	 * @return \BX\DB\Manager\DBResult
	 */
	private function getSql()
	{
		$sql = 'SELECT';
		if ($this->distinct){
			$sql .= ' DISTINCT';
		}
		if (empty($this->select_sql)){
			$this->select(['*']);
		}
		$sql .= ' '.implode(',',$this->select_sql);
		$sql .= ' FROM '.$this->table.' T';
		if (!empty($this->relation)){
			$relation_array = $this->getRelationArray($this->relation);
			if (!empty($relation_array)){
				$sql .= ' '.implode(' ',$relation_array);
			}
		}
		if (!empty($this->filter_sql)){
			$sql .= ' WHERE '.implode(' AND ',$this->filter_sql);
		}
		if (!empty($this->group_sql)){
			$sql .= ' GROUP BY '.implode(',',$this->group_sql);
		}
		if (!empty($this->sort_sql)){
			$sql .= ' SORT BY '.implode(',',$this->sort_sql);
		}
		if ($this->limit > 0){
			$sql .= ' LIMIT '.$this->limit;
		}
		if ($this->offset > 0){
			$sql .= ' OFFSET '.$this->offset;
		}
		return $sql;
	}
	/**
	 * Get count
	 * @return integer
	 */
	public function count()
	{
		$sql = 'SELECT';
		if ($this->distinct){
			$sql .= ' DISTINCT';
		}
		$sql .= ' COUNT(*) as CNT';
		$sql .= ' FROM '.$this->table;
		if (!empty($this->relation)){
			$relation_array = $this->getRelationArray($this->relation);
			if (!empty($relation_array)){
				$sql .= ' '.implode(' ',$relation_array);
			}
		}
		if (!empty($this->filter_sql)){
			$sql .= ' WHERE '.implode(' AND ',$this->filter_sql);
		}
		if (!empty($this->group_sql)){
			$sql .= ' GROUP BY '.implode(',',$this->group_sql);
		}
		$result = $this->db()->query($sql,$this->vars)->count();
		return $result[0]['CNT'];
	}
	/**
	 * Set tags cache
	 * @param array $tags
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function setTags($tags)
	{
		$this->cache_tags = (array) $tags;
		return $this;
	}
	/**
	 * Get cache
	 * @param integer $time
	 * @param array|string $ns
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function cache($time = 3600,$ns = 'base')
	{
		if ($time > 0){
			$this->cache[self::CACHE_KEY_TIME] = $time;
			$this->cache[self::CACHE_KEY_NS] = $ns;
		}
		return $this;
	}
}