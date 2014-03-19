<?php namespace BX;

abstract class Object extends Base
{
	use \BX\String\StringTrait;
	/**
	 * Get class
	 * @param string $key
	 * @param string $object
	 * @return array
	 * @throws \LogicException
	 */
	private static function getClassInstence($key,&$object)
	{
		if ($object === false){
			$match = [];
			if (preg_match(static::getRegexClass(),static::getClassName(),$match)){
				$object = $match[1].':'.$match[2].':'.$match[3];
			} else{
				throw new \LogicException('Class name `'.static::getClassName().'` not match `'.static::getRegexClass().'`');
			}
		} else{
			$count = substr_count($object,':');
			if ($count === 1){
				$object = static::getPackage().':'.$object;
			}
			if ($count === 0){
				$object = static::getPackage().':'.static::getService().':'.$object;
			}
		}
		if (Registry::exists($key,$object,'class')){
			$class_name = Registry::get($key,$object,'class');
		} else{
			list($package,$service,$class) = explode(':',$object);
			$class_name = static::getClass(ucwords($package),ucwords($service),ucwords($class));
		}
		return new $class_name();
	}
	/**
	 * Set params
	 * @param \BX\Object $instance
	 * @param string $key
	 * @param string $object
	 * @param array $params
	 * @return \BX\Object
	 */
	private function setParams($instance,$key,$object,array $params)
	{
		if (Registry::exists($key,$object,'params')){
			$params = array_merge($params,Registry::get($key,$object,'params'));
		}
		if (!empty($params)){
			foreach ($params as $param => $value){
				$func = 'set';
				foreach (explode('_',$param) as $item){
					$func .= $this->string()->ucwords($item);
				}
				$instance->$func($value);
			}
		}
		return $this;
	}
	/**
	 * Set events
	 * @param \BX\Object $instance
	 * @param string $key
	 * @param string $object
	 * @return \BX\Object
	 */
	private function setEvents($instance,$key,$object)
	{
		if (Registry::exists($key,$object,'on')){
			foreach (Registry::get($key,$object,'on') as $name => $function){
				$instance->on($name,$function);
			}
		}
		return $this;
	}
	/**
	 * Autoload class
	 * @param string $object
	 * @param string $key
	 * @param array $params
	 * @return self
	 */
	protected static function autoload($object,$key,array $params = [])
	{
		$instance = self::getClassInstence($key,$object);
		return $instance->setParams($instance,$key,$object,$params)->setEvents($instance,$key,$object);
	}
}