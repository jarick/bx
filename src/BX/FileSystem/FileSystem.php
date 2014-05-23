<?php namespace BX\FileSystem;
use BX\Config\DICService;
use BX\Error\Error;

/**
 * Файловая система
 *
 * Хелпер для работы с файловой системой
 *
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class FileSystem
{
	/**
	 * @var string
	 */
	private static $manager = 'filesystem';
	/**
	 * Get manager
	 *
	 * @return FileSystemManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new FileSystemManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Рекурсивное удаление папки
	 * @param string $path путь к папке
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки, саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function removePathDir($path)
	{
		Error::reset();
		try{
			$return = self::getManager()->removePathDir($path);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Рекурсивное создание папок
	 *
	 * Проверяется наличие папок по задоному пути и если на пути они не найдены, то функция их создает
	 * @param string $path путь к папке
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки, саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function checkPathDir($path)
	{
		Error::reset();
		try{
			$return = self::getManager()->checkPathDir($path);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
}