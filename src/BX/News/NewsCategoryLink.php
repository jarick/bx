<?php namespace BX\News;
use BX\Error\Error;
use BX\News\NewsCategoryLinkManager;
use BX\Config\DICService;

/**
 * Привязка новостей к разделям
 *
 * Класс для работы с привязкой новостей к разделам
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class NewsCategoryLink
{
	/**
	 * @var string
	 */
	private static $manager = 'news_category_link';
	/**
	 * Return news link manager
	 *
	 * @return NewsCategoryLinkManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new NewsCategoryLinkManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Добавление привязки новости к кагерории новостей
	 *
	 * @param integer $news_id
	 * @param integer $category_id
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function add($news_id,$category_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($news_id,$category_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление привязки новости к кагерории новостей
	 *
	 * @param integer $news_id
	 * @param integer $category_id
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function delete($news_id,$category_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->delete($news_id,$category_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление всех привязок у новости
	 *
	 * @param integer $news_id ID новости
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function deleteAllByNewsId($news_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->deleteAllByNewsId($news_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление всех привязок у категории
	 *
	 * @param integer $category_id ID новостной категории
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function deleteAllByCategoryId($category_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->deleteAllByCategoryId($category_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Поиск привязок новостей с категориями
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public static function finder()
	{
		return self::getManager()->finder();
	}
}