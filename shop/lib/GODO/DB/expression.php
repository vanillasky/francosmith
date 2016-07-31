<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */

/**
 * GODO_DB_expression
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_expression {

	/**
	 * quote 처리 하지 않을 문자열
	 * @var string
	 */
	private $expression = '';

	/**
     * string 캐스팅시 설정된 문자열을 리턴
	 * @return string
	 */
	public function __toString() {
		return $this->getExpression();
	}

	/**
	 * 문자열을 설정
     * @param string $str
	 * @return void
	 */
	public function setExpression($str) {
		$this->expression = $str;
	}

	/**
	 * 설정된 문자열을 리턴
	 * return string
	 */
	public function getExpression() {
		return (string) $this->expression;
	}

}
?>