<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
Wind::import('LIBRARY:cache.strategy.CacheStrategy');

/**
 * 
 * 列表cache
 *
 * the last known user to change this file in the repository  <$LastChangedBy: wangsc $>
 * @author wangsc <igglonely@gmail.com>
 * @version $Id$
 * @package
 */
class ListCacheStrategy extends CacheStrategy {
	
	/**
	 * 读取列表缓存
	 * 
	 */
	public function getResult($isCook = true) {
		$key = $this->_getListCacheKey($isCook);
		$listIds = $this->cache->get($key);
		if (!$listIds) {
			$list = call_user_func_array(array($this->getDao(), $this->functionName), $this->arguments);
			$listIds = array();
			$primaryKey = $this->primaryKey;
			foreach ($list as $value) {
				$listIds[] = $value[$primaryKey];
			}
			$this->cache->set($key, $listIds);
		}
		return isset($list) ? $list : $this->_getResultByIds($listIds);
	}
	
	/**
	 * 更新列表cache
	 * 
	 */
	public function update($id = NULL) {
		$this->cache->increment($this->_getVersionKey());
	}
	
	/**
	 * 总条数
	 */
	public function getCount($methodName) {
		$key = $this->_cookHashKey($this->_getPerKey() . 'count:') . $this->_getCacheVersion(true, $methodName);
		if (($count = $this->cache->get($key)) === false) {
			$count = call_user_func_array(array($this->getDao(), $this->functionName), array($this->config['hash']));
			$count && $this->cache->set($key, $count);
		}
		return (int) $count;
	}
	
	/**
	 * 
	 * 列表cache读取
	 * @param unknown_type $ids
	 */
	private function _getResultByIds($ids) {
		
		$itemKeys = $cacheResultIds = $cacheResults = $dbResults = $noCachedIds = array();
		/*根据id取出cache key*/
		foreach ($ids as $value) {
			$itemKeys[] = $this->_getItemCacheKey($value);
		}
		
		/*从cache批量取出*/
		if (($tempResults = $this->cache->get($itemKeys))) {
			foreach ($tempResults as $value) {
				$cacheResultIds [] = $value[$this->primaryKey];
				$cacheResults [] = $value;
			}
		}
		/*比较出未缓存或缓存失效的id*/
		$noCachedIds = array_diff ($ids, $cacheResultIds);
		
		/*从db取出未缓存的id，进入此处几率较低*/
		if ($noCachedIds && ($dbResults = $this->getDao()->getMultiByField($this->primaryKey, $noCachedIds))) {
			foreach ($dbResults as $value) {
				$this->cache->set($this->_getItemCacheKey($value[$this->primaryKey]), $value);
			}
			/*重组数组，逻辑有点小乱*/
			$temp = array();
			foreach ($ids as $value) {
				$fromArray = in_array($value, $noCachedIds) ? $dbResults : $cacheResults;
				$temp[] = $this->_getIdFromArray($value,$fromArray);
			}
			return $temp;
		}
		return $cacheResults;
	}
	
	/**
	 * 
	 * 根据pk取出值
	 * @param unknown_type $id
	 * @param unknown_type $array
	 */
	private function _getIdFromArray($id, &$array) {
		foreach ($array as $key => $value) {
			if ($id == $value[$this->primaryKey]) {
				unset($array[$key]);
				return $value;
			}
		}
		return array();
	}
	
	/**
	 * 
	 * 取得单数据cacke key
	 * @param unknown_type $id
	 */
	private function _getItemCacheKey($id) {
		return $this->_getPerKey() . $this->primaryKey . ':' . $id;
	}
	
	/**
	 * 取得列表数据所有cache key
	 * Enter description here ...
	 */
	private function _getListCacheKey($isCook = true) {
		$limit = isset($this->config['limit']) ? $this->config['limit'] : 0;
		$offset = isset($this->config['offset']) ? $this->config['offset'] : 0;
		$result = $this->_cookHashKey($this->_getPerKey()) . $this->functionName . ':limit:' . $limit . ':offset:' . $offset . ':';
		return $this->mdKey($result . $this->_getCacheVersion($isCook));
	}
		
	/**
	 * 取得列表cache版本key
	 * Enter description here ...
	 */
	private function _getCacheVersion($isCook = true, $methodName = NULL) {
		$key = $this->_getVersionKey($isCook, $methodName);
		$versionId = $this->cache->get($key);
		if (!$versionId){
			$versionId = 1;
			$this->cache->set($key, $versionId);
		}
		return $versionId;
	}
	
	/**
	 * 
	 * 版本key
	 */
	private function _getVersionKey($isCook = true, $methodName = NULL) {
		$result = $this->_getPerKey();
		$temp = $methodName ? $methodName : $this->functionName;
		$result = $isCook ? $this->_cookHashKey($result) . $temp . ':version:' : $result . $temp . ':version:';
		return $this->mdKey($result);
	}
	
	/**
	 * 
	 * 组合key
	 * @param unknown_type $key
	 */
	private function _cookHashKey($key) {
		if (isset($this->config['hash']) && $this->config['hash']) {
			$this->config['hash'] = (array) $this->config['hash'];
			foreach ($this->config['hash']  as $k => $value) {
				$key .= 'hash:'. $k . ':' . $value . ':';
			}
		}
		return $key;
	}
	
	/**
	 * 
	 * md5值key
	 * @param string $key
	 */
	private function mdKey($key) {
		return substr(md5($key), 8, 16);
	}
}