<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * 缓存策略工厂
 *
 * the last known user to change this file in the repository  <$LastChangedBy: wangsc $>
 * @author wangsc <igglonely@gmail.com>
 * @version $Id$
 * @package
 */
class CacheStrategyFactory {
	
	/**
	 * 
	 * 创建缓存策略对象
	 * @param unknown_type $type
	 */
	static public function createCacheStrategy($type) {
		switch ($type) {
			case 'item':
				Wind::import('LIBRARY:cache.strategy.ItemCacheStrategy');
				return new ItemCacheStrategy();
			case 'list':
				Wind::import('LIBRARY:cache.strategy.ListCacheStrategy');
				return new ListCacheStrategy();
		}
	}
}