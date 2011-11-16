<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
Wind::import('LIBRARY:cache.CacheInterface');
/**
 * 
 * memcache操作类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: wangsc $>
 * @author wangsc <igglonely@gmail.com>
 * @version $Id$
 * @package
 */
class Smemcache implements CacheInterface {
	
	protected $cache;
	
	/**
	 * 
	 * 构造函数
	 */
	public function __construct($cacheInfo = NULL) {
		$this->cache = new Memcache;
		if (is_array($cacheInfo)) $this->connectCache($cacheInfo);
		return $this;
	}
	
	/**
	 * 
	 * 连接memcache
	 * @param array $cacheInfo
	 */
	public function connectCache($cacheInfo) {
		$this->cache->addserver($cacheInfo['host'], $cacheInfo['port']);
	}
	
	/**
	 * 从内存中获取数据
	 * @param string $key
	 */
	public function get($key) {
		return $this->cache->get($key);
	}
	
	/**
	 * 设置一个key-value到内存中
	 * 
	 */
	public function set($key, $value, $expire = 0) {
		return $this->cache->set($key, $value, 0, $expire);
	}
	
	/**
	 * 
	 * 自增长
	 * @param string $key
	 * @param int $step
	 */
	public function increment($key, $step = 1) {
		return $this->cache->increment($key, $step);
	}
	/**
	 * 
	 * 递减
	 * @param string $key
	 * @param int $step
	 */
	public function decrement($key, $step = 1) {
		return $this->cache->decrement($key, $step);
	}
	
	/**
	 * 删除内存中键值为$key的数据
	 * 
	 */
	public function delete($key) {
		return $this->cache->delete($key);
	}
	
	/**
	 * 清空内存
	 * 
	 */
	public function flush() {
		return $this->cache->flush();
	}
	
}
