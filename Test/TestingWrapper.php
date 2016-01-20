<?php
/**
 * Created by PhpStorm.
 * User: ryuji
 * Date: 15/06/18
 * Time: 18:43
 */

/**
 * Class TestingWrapper privateやprotectedメソッドをテストできるようにするラッパー
 */
class TestingWrapper {

/**
 * @var Instance ラップする元インスタンス
 */
	protected $_wrappedInstance;

/**
 * constructor
 *
 * @param Instance $wrappedInstance ラップする元インスタンス
 * @return TestingWrapper
 */
	public function __construct($wrappedInstance) {
		$this->_wrappedInstance = $wrappedInstance;
	}

/**
 * __call
 *
 * @param string $methodName メソッド名
 * @param array $params パラメータ
 * @return mixed
 */
	public function __call($methodName, $params) {
		if (substr($methodName, 0, 9) === '_testing_') {
			$callName = substr($methodName, 9);
			$method = new ReflectionMethod($this->_wrappedInstance, $callName);
			$method->setAccessible(true);
			$result = $method->invokeArgs($this->_wrappedInstance, $params);
			return $result;
		} else {
			return call_user_func_array(array($this->_wrappedInstance, $methodName), $params);
		}
	}
}
