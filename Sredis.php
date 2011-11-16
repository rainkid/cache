<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
Wind::import('LIBRARY:cache.CacheInterface');

/**
 * 
 * Redis操作类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: wangsc $>
 * @author wangsc <igglonely@gmail.com>
 * @version $Id$
 * @package
 */
class Sredis implements  CacheInterface {
	
	protected $cache;
	
	/**
	 * 
	 * 构造函数
	 * 
	 */
	public function __construct($cacheInfo) {
		$this->cache = new Redis;
		if(is_array($cacheInfo)) $this->connectCache($cacheInfo);
		return $this;
	}
	
	/**
	 * 连接redis
	 * 
	 */
	public function connectCache($cacheInfo) {
		try {
			$this->cache->connect($cacheInfo['host'], $cacheInfo['port']);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * 从内存获取数据 
	 *
	 */
	public function get($key) {
		if (!is_array($key))
			return $this->_getSingleValue($key);
		$temp = $this->cache->mget($key);
		$result = array();
		foreach ($temp as $val) {
			if (!$val) continue;
			$result[] = json_decode($val, true);
		}
		return $result;
	}
	
	/**
	 * 
	 * 单个key
	 * @param unknown_type $key
	 */
	private function _getSingleValue($key) {
		$temp = $this->cache->get($key);
		if ($temp === false) return false;
		return json_decode($temp, true);
	}
	
	/**
	 * 设置值到内存中
	 */
	public function set($key, $value, $expire = 0) {
		$value = json_encode($value);
		$result = $this->cache->set($key, $value);
		if ($expire) $this->cache->expireAt($key, $expire);
		return $result;
	}
	
	/**
	 * 
	 * 自增长
	 * @param unknown_type $key
	 * @param unknown_type $step
	 */
	public function increment($key, $step = 1) {
		return $this->cache->incr($key, $step);
	}
	
	/**
	 * 
	 * 递减
	 * @param string $key
	 * @param int $step
	 */
	public function decrement($key, $step = 1) {
		return $this->cache->decr($key, $step);
	}
	
	/**
	 * 
	 * 删除内存$key
	 */
	public function delete($key) {
		return $this->cache->delete($key);
	}
	
	/**
	 * 
	 * 分页取列表
	 * @param unknown_type $key
	 * @param unknown_type $limit
	 * @param unknown_type $offset
	 */
	public function get_list_range($key, $limit, $offset) {
		return $this->cache->lGetRange($key, $limit, $limit + $offset - 1);
	}
	
	/**
	 * 清内存
	 * 
	 */
	public  function flush() {
		return $this->cache->flushDB();
	}
	
}
