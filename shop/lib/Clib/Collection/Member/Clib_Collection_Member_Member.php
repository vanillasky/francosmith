<?php
class Clib_Collection_Member_Member extends Clib_Collection_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'member';


	protected function construct()
	{
		$this->setOrder('m_no asc');
	}




}
