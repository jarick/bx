<?php namespace BX\ZendSearch\Manager;
use BX\Manager;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive;
use ZendSearch\Lucene\Analysis\TokenFilter\StopWords;
use BX\Registry;
use BX\ZendSearch\Filter\Morphy;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Search\QueryParser;

class ZendSearchManager extends Manager
{
	use \BX\FileSystem\FileSystemTrait,
	 \BX\Http\HttpTrait;
	protected $limit = 100;
	protected $spec_symbol = ['\\','+','-','&&','||','!','(',')','{','}','[',']','^','"','~','*','?',':'];
	private static $index;
	/**
	 * Set select limit
	 * @param integer $limit
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
	}
	/**
	 * Set special symbol
	 * @param array $spec_symbol
	 */
	public function setSpecSymbol(array $spec_symbol)
	{
		$this->spec_symbol = $spec_symbol;
	}
	/**
	 * Get real path for file
	 * @param string $path
	 */
	public function getRealPath($path)
	{
		return realpath(str_replace('~',$this->request()->server()->get('DOCUMENT_ROOT'),$path));
	}
	/**
	 * Get index
	 * @return \ZendSearch\Lucene\Index
	 */
	private function index()
	{
		if (!isset(self::$index)){
			$analyzer = new CaseInsensitive();
			if (Registry::exists('zend_search','stop_words')){
				$stop_word_filter = new StopWords();
				$words = $this->getRealPath(Registry::get('zend_search','stop_words'));
				if ($words !== false){
					$stop_word_filter->loadFromFile($words);
				} else{
					throw new \InvalidArgumentException('Path not found');
				}
				$analyzer->addFilter($stop_word_filter);
			}
			if (Registry::exists('zend_search','morphy_dicts')){
				$morphy_dicts = $this->getRealPath(Registry::get('zend_search','morphy_dicts'));
				if ($morphy_dicts !== false){
					$analyzer->addFilter(new Morphy($morphy_dicts,$this->getCharset()));
				} else{
					throw new \InvalidArgumentException('Path not found');
				}
			}
			Analyzer::setDefault($analyzer);
			Lucene::setResultSetLimit($this->limit);
			QueryParser::setDefaultEncoding($this->getCharset());
			$index = Registry::get('zend_search','index');
			$path = $this->getRealPath($index);
			self::$index = ($path) ? Lucene::open($path) : Lucene::create($index);
		}
		return self::$index;
	}
	/**
	 * Delete index
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$document = current($this->findByQuery("id: $id"));
		if (is_object($document)){
			$this->index()->delete($document->id);
			$this->index()->commit();
			return true;
		}
		return false;
	}
	/**
	 * Add index
	 * @param integer $id
	 * @param array $index
	 */
	public function add($id,array $index)
	{
		$document = new Document();
		$document->addField(Field::keyword('id',$id));
		foreach ($index as $field){
			$document->addField($field);
		}
		$this->index()->addDocument($document);
		$this->index()->commit();
	}
	/**
	 * Find escape query
	 * @param string $query
	 * @return array|\ZendSearch\Lucene\Search\QueryHit
	 */
	public function find($query)
	{
		foreach ($this->spec_symbol as $symbol){
			$query = str_replace($symbol,'\\'.$symbol,$query);
		}
		return $this->findByQuery($query);
	}
	/**
	 * Find by query
	 * @param string $query
	 * @return array|\ZendSearch\Lucene\Search\QueryHit
	 */
	public function findByQuery($query)
	{
		return $this->index()->find($query);
	}
	/**
	 * Flush all index
	 */
	public function flush()
	{
		$folder = Registry::get('zend_search','index');
		if (is_dir(($folder))){
			$this->filesystem()->removePathDir($folder);
		}
	}
}