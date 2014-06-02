<?php namespace BX\News;

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
	 * Добавление привязки
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
	 * Удаление привязки
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
}