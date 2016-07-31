<?php
/**
 * Clib_Controller_Abstract
 * @author extacy @ godosoft development team.
 */
abstract class Clib_Controller_Abstract
{
	/**
	 * Construct
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Controller 처리 결과를 화면에 출력한다.
	 * @return void
	 */
	public function display()
	{
		Clib_Application::template_()->make();
		Clib_Application::template_()->display();
	}

	/**
	 * Clib_Application 클래스 에서 Controller 생성후,
	 * 지정된 동작이 없을때 자동으로 실행된다.
	 * @return mixed
	 */
	abstract public function main();

	private function _back($msg = null)
	{
		// back 처리?

	}

	protected function redirect()
	{
		// redirect

	}

}
