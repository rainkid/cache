<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * 缓存工厂
 *
 * the last known user to change this file in the repository  <$LastChangedBy: wangsc $>
 * @author wangsc <igglonely@gmail.com>
 * @version $Id$
 * @package
 */
class CacheFactory {
	
	static $instances;
	
	/**
	 * 
	 * 统一获取cache接口
	 * @param array $cacheInfo
	 * @param string $cacheType
	 */
	static public function getCache($cacheInfo, $cacheType = 'memcache') {
		if(!in_array($cacheType, array('memcache', 'redis'))) $cacheType = 'memcache';
		$cacheName = 'S' . $cacheType;
		$key = md5($cacheName);
		if(isset(self::$instances[$key]) && is_object(self::$instances[$key])) return self::$instances[$key];
		Wind::import('LIBRARY:cache.' . $cacheName);
		if (!class_exists($cacheName)) throw new Exception('empty class name');
		self::$instances[$key] = new $cacheName($cacheInfo);
		return self::$instances[$key];
	}
}