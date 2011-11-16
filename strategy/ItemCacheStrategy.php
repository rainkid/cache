<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
Wind::import('LIBRARY:cache.strategy.CacheStrategy');
/**
 * 
 * 单数据cache类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: wangsc $>
 * @author wangsc <igglonely@gmail.com>
 * @version $Id$
 * @package
 */
class ItemCacheStrategy extends CacheStrategy {
	
	/**
	 * 根据key先从cache取数据，为false则从db取出存至cache
	 * 
	 */
	public function getResult($isCook = true) {
		$key = $this->getItemCacheKey();
		$value = $this->cache->get($key);
		if ($value === false) {
			$value = call_user_func_array(array($this->getDao(), $this->functionName), $this->arguments);
			if ($value) $this->cache->set($key, $value);
		}
		return $value;
	}
	
	/**
	 * 更新cache
	 * 
	 */
	public function update($id = NULL) {
		$cKey = $this->_getPerKey() . $this->primaryKey . ':' . $id;
		$this->cache->delete($cKey);
	}
	
	/**
	 * 
	 * 获取cache key
	 * @param unknown_type $item
	 */
	private function getItemCacheKey() {
		if (!$this->arguments || !is_array($this->arguments)) return '';
		$keyArr = array_keys($this->arguments);
		$argsKey = $keyArr[0];
		if ($argsKey == $this->primaryKey) 
			return $this->_getPerKey() . $this->primaryKey . ':' . $this->arguments[$this->primaryKey];
		$cKey = $this->_getPerKey() . $argsKey . ':' . $this->arguments[$argsKey];
		if (!$pkValue = $this->cache->get($cKey)) {
			$dbResult = call_user_func_array(array($this->getDao(), $this->functionName), $this->arguments);
			if (!$dbResult) return '';
			$pkValue = $dbResult[$this->primaryKey];
			$this->cache->set($cKey, $pkValue);
		}
		return $this->_getPerKey() . $this->primaryKey . ':' . $pkValue;
	}
}