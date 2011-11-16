<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * 缓存策略基类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: wangsc $>
 * @author wangsc <igglonely@gmail.com>
 * @version $Id$
 * @package
 */
abstract class CacheStrategy{
	/**
	 * 
	 * 缓存对象
	 *
	 * @var unknown_type
	 */
	protected $cache;
	
	/**
	 * 
	 * dao信息
	 *
	 * @var string
	 */
	protected $daoInfo;
	
	/**
	 * 
	 * 缓存key
	 *
	 * @var unknown_type
	 */
	protected $config;
	
	/**
	 * 
	 * 代理模式调用dao方法名
	 *
	 * @var unknown_type
	 */
	protected $functionName;
	
	/**
	 * 
	 * 参数
	 *
	 * @var unknown_type
	 */
	protected $arguments;
	
	/**
	 * 
	 * 主键名
	 *
	 * @var unknown_type
	 */
	protected $primaryKey;
	
	/**
	 * 
	 * 初始化策略
	 * @param unknown_type $daoName
	 * @param unknown_type $methodName
	 * @param unknown_type $arguments
	 */
	public function init($daoInfo, $methodName, $arguments = array()) {
		
		$this->cache = Common::getCacheHandle();
		$this->daoInfo = $daoInfo;
		$this->primaryKey = $daoInfo[2];
		$this->functionName = $methodName;
		$this->arguments = $arguments;
	}
	
	/**
	 * 
	 * 设置key
	 * @param unknown_type $config
	 */
	public function setConfig($config) {
		$this->config = $config;
	}
	
	/**
	 * 
	 * 获取dao对象
	 */
	protected function getDao() {
		return Common::getDao($this->daoInfo[0], $this->daoInfo[1]);
	}
	
	/**
	 * 
	 * 组合key
	 */
	protected function _getPerKey() {
		$result = strtolower ($this->daoInfo[0] .':');
		return $result;
	}
	
	abstract public function getResult($isCook = true);
	abstract public function update($id = NULL);
}
