<?php namespace BX\news;
use BX\News\NewsManager;
use BX\Config\DICService;

/**
 * Новости
 *
 * Класс для работы с новостями
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class News
{
	/**
	 * @var string
	 */
	private static $manager = 'news';
	/**
	 * Return news manager
	 *
	 * @return NewsManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new NewsManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Добавление новости
	 *
	 * @param array $news Массив значения полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 */
	public static function add(array $news)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($news);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Изменение новости
	 *
	 *
	 * @param integer $id ID новости
	 * @param array $news Массиы значения полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function update($id,array $news)
	{
		Error::reset();
		try{
			$return = self::getManager()->update($id,$news);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление новости
	 *
	 * @param integer $id ID новости
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
	 * Поиск новости
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public static function finder()
	{
		return self::getManager()->finder();
	}
}