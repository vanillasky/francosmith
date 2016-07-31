<?php
/**
 * Created on 2008. 01. 28
 *
 * Filename	: Basic.lib.php
 * Comment 	: class미구현 함수들 
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 **/
?>
<?
function arrayToString($array)
{
   $text.="array(";
   $count=count($array);

   foreach ($array as $key => $value)
   {
       $x++;

       if (is_array($value))
       {
           if(substr($text,-1,1)==')')    $text .= ',';
           $text.='"'.$key.'"'."=>".arraytostring($value);
           continue;
       }

       $text.="\"$key\"=>\"$value\"";

       if ($count!=$x) $text.=",";
   }

   $text.=")";

   if(substr($text, -4, 4)=='),),')$text.='))';

   return $text;
}

/** 프린트에러 메세지 출력 **/
function PrintError($n, $str = null) {
	global $confErrorMsg;
	echo '[Error' . $n . ']' . $confErrorMsg[$n];
	if ( !is_null($str) ) {
		print_r( "\n" . $str);
	}
	echo "\n";
	die();
}

// 디버그 함수
function debug2($data) {
	print "<xmp style=\"font:8pt 'Courier New';background:#000000;color:#00ff00;padding:10\">";
	print_r($data);
	print "</xmp>";
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

//기본용
function getPaging($pg, $jp, $total, $ls, $options){
	$totalPage = ceil($total / $ls);
	$pageGroup= ceil($pg / $jp);
	$totalpageGroup = ceil($totalPage / $jp);
	$prevJumper = $jp * ($pageGroup-1);
	$link = is_array($options) ? "&" . parseParameter($options) : $options;

	
	if($pageGroup>1) $r .=  "[<a href={$PHP_SELF}?pg={$prevJumper}{$link}>◀</a>]&nbsp;&nbsp;";
	
	for($i = 1 + $prevJumper ; $i <= ($jp * $pageGroup) && $i <= $totalPage ; $i++) {
		if($i != $pg) $r .=  "<a href={$PHP_SELF}?pg={$i}{$link}>[{$i}]</a>&nbsp;&nbsp;";
		else $r .=  "<strong>{$i}</strong>&nbsp;&nbsp;";
	}

	if($i <= $totalPage) $r .=  "[<a href={$PHP_SELF}?pg={$i}{$link}>▶</a>]";
	
		
	return $r ;
}

//코디에디터용 스크립트 페이징
function getScriptPaging($pg, $jp, $total, $ls){
	$totalPage = ceil($total / $ls);
	$pageGroup= ceil($pg / $jp);
	$totalpageGroup = ceil($totalPage / $jp);
	$prevJumper = $jp * ($pageGroup-1);
	$link = is_array($options) ? "&" . parseParameter($options) : $options;
	
	if($pageGroup>1) $r .=  "[<a href=\"javascript:\" onclick=\"searchGoods('1')\">◀</a>]&nbsp;&nbsp;";
	
	for($i = 1 + $prevJumper ; $i <= ($jp * $pageGroup) && $i <= $totalPage ; $i++) {
		if($i != $pg) $r .=  "<a href=\"javascript:\" onclick=\"searchGoods('".$i."')\">[{$i}]</a>&nbsp;&nbsp;";
		else $r .=  "<strong>{$i}</strong>&nbsp;&nbsp;";
	}

	if($i <= $totalPage) $r .=  "[<a href=\"javascript:\" onclick=\"searchGoods('".$i."')\">▶</a>]";
			
	return $r ;
}


/**
 * 배열을 url 파라미터로 변환한다.
 *
 */
function parseParameter($a) {
	if ( !is_array($a) || count($a) < 1 ) return '';

	while ( list($k, $v) = each($a) ) {
		$r .= "&" . $k . "=" . $v;
	}

	return $r;
}

function loadConfig($name,$config,$path=""){
	
	if($path == "")$path = dirname(__FILE__) . '/../../../setGoods/data/config/';
	include $path . $config;	
	
	return ${$name};
}


function pageError($srt){
	echo "<script>alert('".$srt."\\n\\n 관리자에게 문의 하세요.');</script>";
}
?>