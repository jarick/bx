<?php namespace BX\ZendSearch\Helper;
use BX\Base\Registry;
use BX\Validator\IEntity;
use ZendSearch\Lucene\Document\Field;

class SearchHelper
{
	/**
	 * @var IEntity
	 */
	private $entity;
	/**
	 * Constructor
	 * @param IEntity $entity
	 */
	public function __construct($entity)
	{
		$this->entity = $entity;
	}
	/**
	 * Get text search field
	 * @param string $name
	 * @param string $value
	 * @return Field
	 */
	public function text($name,$value = null)
	{
		if ($value === null){
			$value = $this->entity->getValue($name);
		}
		return Field::text($name,$value,Registry::getCharset());
	}
	/**
	 * Get keyword search field
	 * @param string $name
	 * @param string $value
	 * @return Field
	 */
	public function keyword($name,$value)
	{
		Field::keyword($name,$value,Registry::getCharset());
	}
	/**
	 * Get un stored search field
	 * @param string $name
	 * @param string $value
	 * @return Field
	 */
	public function unStored($name,$value)
	{
		return Field::unStored($name,$value,Registry::getCharset());
	}
	/**
	 * Get binary search field
	 * @param string $name
	 * @param string $value
	 * @return Field
	 */
	public function binary($name,$value)
	{
		return Field::binary($name,$value);
	}
}