<?php

/* TEMPLATE PLUGIN FUNCTION EXAMPLE */

function scriptRotator()
{

	
	$rollbannerPath = "../conf/design.rollbaner.dat";
	if(!is_file($rollbannerPath))
	{
		return '';
	}
	$banner_info = unserialize(file_get_contents($rollbannerPath));
	
	$banner_image=array();
	for($i=1;$i<=9;$i++)
	{
		if(is_file('../data/scriptrotator/'.$i.'.jpg'))
		{
			$banner_image[]=$i;
		}
	}
	
	if(count($banner_image)==0)
	{
		return '';
	}



	$returnStr='';
	$returnStr="
	<script src='../lib/js/ierotator.js' type='text/javascript'></script>
	<div id='ie_banner' style='position:relative;display:none'>
	";
	foreach($banner_image as $v)
	{
		if($banner_info['link'][$v])
		{
			$returnStr.="\n<div>
			<a href='{$banner_info['link'][$v]}'>
			<img src='../data/scriptrotator/$v.jpg' width='{$banner_info['width']}' height='{$banner_info['height']}'>
			</a>
			</div>\n";
		}
		else
		{
			$returnStr.="\n<div><img src='../data/scriptrotator/$v.jpg'></div>\n";
		}
	}
	$returnStr.="</div>\n\n\n\n";
	
	if($banner_info['setting'])
	{
		$optstr = implode(',',$banner_info['setting']);
	}
	$optstr .=  ',Duration='.$banner_info['duration'];
	
	if($banner_info['numDisplay']=='yes')
	{
		$banner_info['numDisplay']='block';
	}
	else
	{
		$banner_info['numDisplay']='none';
	}

	$returnStr.="

	<script>
	var config = {
		'id':'ie_banner',
		'effect':'FILTER: progid:DXImageTransform.Microsoft.{$banner_info['effect']}($optstr)',
		'width':{$banner_info['width']},
		'height':{$banner_info['height']},
		'wait':{$banner_info['wait']},
		'numDisplay':'{$banner_info['numDisplay']}',
		'numimg':[
			['../lib/js/ierotator/01.gif','../lib/js/ierotator/01_over.gif'],
			['../lib/js/ierotator/02.gif','../lib/js/ierotator/02_over.gif'],
			['../lib/js/ierotator/03.gif','../lib/js/ierotator/03_over.gif'],
			['../lib/js/ierotator/04.gif','../lib/js/ierotator/04_over.gif'],
			['../lib/js/ierotator/05.gif','../lib/js/ierotator/05_over.gif'],
			['../lib/js/ierotator/06.gif','../lib/js/ierotator/06_over.gif'],
			['../lib/js/ierotator/07.gif','../lib/js/ierotator/07_over.gif'],
			['../lib/js/ierotator/08.gif','../lib/js/ierotator/08_over.gif'],
			['../lib/js/ierotator/09.gif','../lib/js/ierotator/09_over.gif']
		]
	}
	ier = new ierotator(config);
	</script>
	";
	return $returnStr;


}
?>