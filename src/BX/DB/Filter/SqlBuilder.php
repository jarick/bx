<?php namespace BX\DB\Filter;
use BX\DB\Adaptor\IAdaptor;
use BX\DB\Manager\DBResult;
use BX\Base\Collection;

class SqlBuilder
{
	use \BX\String\StringTrait,
	 \BX\DB\DBTrait,
	 \BX\Cache\CacheTrait{
		cache as protected cacheManager;
	}
	/**
	 * @var array
	 */
	private $fields = null;
	/**
	 * @var array
	 */
	private $relation = null;
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
	 * @var \BX\DB\ITable
	 */
	private $entity;
	/**
	 * @var string
	 */
	private $entity_class;
	const CACHE_KEY_TIME = 'time';
	const CACHE_KEY_NS = 'namespace';
	const CACHE_PREFIX = '__SQL_BUILDER__';
	/**
	 * Constructor
	 * @param \BX\DB\ITable $entity
	 * @param string $entity_class
	 */
	public function __construct(\BX\DB\ITable $entity,$entity_class)
	{
		$this->entity = $entity;
		$fields = [];
		foreach($entity->getColumns() as $key => $column){
			$key = $this->string()->toUpper($key);
			$fields[$key] = $column->getColumn();
		}
		$this->fields = $fields;
		$relations = $this->entity->getRelations();
		for($i = 0; $i < count($relations); $i+=2){
			$this->relation[] = [$relations[$i],$relations[$i + 1]];
		}
		$this->entity_class = $entity_class;
	}
	/**
	 * Prepare array from db
	 * @param array $fields
	 * @return array
	 */
	private function prepareArrayFromDb(array $fields)
	{
		$columns = $this->entity->getColumns();
		foreach($fields as $field => &$value){
			if (array_key_exists($field,$columns)){
				$value = $columns[$field]->convertFromDB($value);
			}
		}
		unset($value);
		return $fields;
	}
	/**
	 * Convert values from db
	 * @param array $values
	 * @return \BX\Base\Collection
	 */
	public function convertFromArray(array $values)
	{
		$collection = new Collection($this->entity_class);
		foreach($values as $value){
			$entity = new $this->entity_class();
			$entity->setData($this->prepareArrayFromDb($value),true);
			$collection->add($entity);
		}
		return $collection;
	}
	/**
	 * Get database adaptor
	 * @return IAdaptor
	 * */
	public function adaptor()
	{
		return $this->db()->adaptor();
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
		$fields = $this->fields;
		if (array_key_exists($key,$fields)){
			if (!in_array($key,$this->columns)){
				$this->columns[] = $key;
			}
			return $fields[$key];
		}else{
			throw new \InvalidArgumentException("Field `$key` is not found");
		}
	}
	/**
	 * Set data for GROUP BY operator
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function group()
	{
		$groups = func_get_args();
		if (count($groups) === 1 && isset($groups[0]) && is_array($groups[0])){
			$groups = $groups[0];
		}
		if ($groups !== false && !empty($groups)){
			foreach($groups as $key){
				$key = $this->string()->toUpper($key);
				$this->group_sql[] = $this->getColumn($key);
			}
		}
		$this->group_sql = array_unique($this->group_sql);
		return $this;
	}
	/**
	 * Set sorting
	 * @param array $sort
	 * @return \BX\DB\Filter\SqlBuilder
	 * @throws \InvalidArgumentException
	 */
	public function sort(array $sort = [])
	{
		foreach($sort as $key => $value){
			$key = $this->string()->toUpper($key);
			$value = $this->string()->toUpper($value);
			if (in_array($value,['ASC','DESC'],true)){
				$this->sort_sql[] = $this->getColumn($key).' '.$value;
			}else{
				throw new \InvalidArgumentException('Sorting must be asc or desc');
			}
		}
		$this->sort_sql = array_unique($this->sort_sql);
		return $this;
	}
	/**
	 * Set filter
	 * @param array $filter
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function where($sql,array $params = [])
	{
		$this->filter_sql[] = $sql;
		foreach($params as $key => $value){
			$this->vars[$key] = $value;
		}
		return $this;
	}
	/**
	 * Set filter
	 * @param array $filter
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function filter(array $filter = [])
	{
		$filter_rule = [];
		foreach($this->entity->getColumns() as $key => $column){
			$filter_rule[$key] = $column->getFilterRule();
		}
		$block = new LogicBlock($this,$filter_rule);
		$this->filter_sql[] = $block->toSql($filter);
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
		$fields = $this->fields;
		$select = func_get_args();
		if (count($select) === 1 && isset($select[0]) && is_array($select[0])){
			$select = $select[0];
		}
		$all = false;
		if (in_array('*',$select)){
			$all = true;
			foreach($fields as $key => $value){
				unset($select[$key]);
				$key = $this->string()->toUpper($key);
				$this->select_sql[] = $this->getColumn($key).' as '.$this->db()->esc($key);
			}
		}
		foreach($select as $key => $value){
			if ($value === '*'){
				continue;
			}
			if (is_numeric($key)){
				if ($all === false){
					$this->select_sql[] = $this->getColumn($value).' as '.$this->db()->esc($value);
				}
			}else{
				$value = $this->string()->toUpper($value);
				$function = $this->getGroupByFunctionArray();
				if (in_array($value,$function)){
					$this->select_sql[] = $value.'('.$this->getColumn($key).') as '.$this->db()->esc($value.'_'.$key);
				}else{
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
		foreach($relation_array as $column => $relation){
			if (is_string($relation)){
				$columns = explode(',',$column);
				$relation = $relation;
			}else{
				$columns = (array)$relation[0];
				$relation = $relation[1];
			}
			foreach($columns as $key){
				$keys[$key] = $i;
			}
			$relations[$i] = $relation;
			$i++;
		}
		foreach($this->columns as $column){
			if (isset($keys[$column]) && isset($relations[$keys[$column]])){
				$result[] = $relations[$keys[$column]];
				unset($relations[$keys[$column]]);
			}
		}
		return $result;
	}
	/**
	 * Get all
	 * @return \BX\Base\Collection
	 */
	public function all()
	{
		return $this->convertFromArray($this->find()->getData());
	}
	/**
	 * Find all rows
	 * @return DBResult
	 */
	public function find()
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
			}else{
				$result = new DBResult($cache_result);
			}
		}else{
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
		$all = $this->limit(1)->all();
		$all->rewind();
		return $all->current();
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
		if ($this->entity->getDbTable() === null){
			$error = 'Table name is not set class: `'.get_class($this->entity).'`';
			throw new \RuntimeException($error);
		}
		$sql .= ' FROM '.$this->entity->getDbTable().' T';
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
			$sql .= ' ORDER BY '.implode(',',$this->sort_sql);
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
		$result = $this->query($sql)->count();
		return $result[0]['CNT'];
	}
	/**
	 * Set tags cache
	 * @param array $tags
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function setTags($tags)
	{
		$this->cache_tags = (array)$tags;
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