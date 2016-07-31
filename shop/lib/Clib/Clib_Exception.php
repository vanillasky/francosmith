<?php
/**
 * Clib_Exception
 * @author extacy @ godosoft development team.
 */
class Clib_Exception extends Exception
{
	/**
	 * 오류 상수
	 */
	const ERROR_COMMON = 0;
	const ERROR_CLASS_UNDEFINED = 1;
	const ERROR_DATA_UNLOADED = 2;
	const ERROR_PERMISSION_DENIED = 3;

	/**
	 * 예외 처리
	 * @param Clib_Exception $e
	 * @return void
	 */
	public static function displayMessage(Clib_Exception $e)
	{
		$code = $e->getCode();
		$msg = $e->getMessage();
		$trace = $e->getTraceAsString();

		// @todo : exception 발생시 처리 로직 추가
		debug($code);
		debug($msg);
		debug($trace);

	}

}
