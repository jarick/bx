<?php namespace BX\News;
use BX\Error\Error;
use BX\News\NewsCategoryManager;
use BX\Config\DICService;

/**
 * Раздел новостей
 *
 * Класс для работы с категориями новостей
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class NewsCategory
{
	/**
	 * @var string
	 */
	private static $manager = 'news';
	/**
	 * Return news link manager
	 *
	 * @return NewsCategoryManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new NewsCategoryManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Добавление категории новостей
	 *
	 * @param array $category Массив значения полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 */
	public static function add(array $category)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($category);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Изменении категории новостей
	 *
	 * @param integer $id ID категории
	 * @param array $category Массив значения полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 */
	public static function update($id,array $category)
	{
		Error::reset();
		try{
			$return = self::getManager()->update($id,$category);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удалении категории новостей
	 *
	 * @param integer $id ID категории
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 */
	public static function delete($id)
	{
		Error::reset();
		try{
			$return = self::getManager()->delete($id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Поиск категории
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public static function finder()
	{
		return self::getManager()->finder();
	}
}