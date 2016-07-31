<?php
class LoadClass
{
	var $class;
	function class_load($obj_name, $class_name = null, $arg1 = null, $arg2 = null, $arg3 = null)
	{
		$this->class	= array();
		if(!array_key_exists($obj_name, $this->class)&&$class_name){
			//echo $obj_name;
			if($arg1===null) $this->class[$obj_name]=new $class_name();
			else if($arg2===null) $this->class[$obj_name]=new $class_name(&$arg1);
			else if($arg3===null) $this->class[$obj_name]=new $class_name(&$arg1,&$arg2);
			else $this->class[$obj_name]=new $class_name(&$arg1,&$arg2,&$arg3);
		}
	}
}
?>