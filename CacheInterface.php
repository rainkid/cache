<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * cache基础接口
 *
 * the last known user to change this file in the repository  <$LastChangedBy: wangsc $>
 * @author wangsc <igglonely@gmail.com>
 * @version $Id$
 * @package
 */
 interface CacheInterface {
 	public function get($key);
 	public function set($key, $value, $expire = 0);
 	public function delete($key);
 	public function flush();
 }