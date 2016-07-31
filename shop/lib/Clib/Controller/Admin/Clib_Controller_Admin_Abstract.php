<?php
class Clib_Controller_Admin_Abstract extends Clib_Controller_Abstract
{
	public function main()
	{

	}

	public function __construct()
	{
		if ( ! $this->_checkPermission()) {
			$this->_back('권한 없음.');
		}

	}

	private function _checkPermission()
	{
		return true;
	}

}
