<?

$str_mode	= array(
			''			=> '방문자 분석',
			'referer'	=> '방문 경로분석',
			'client'	=> '방문자 환경분석',
			'iplist'	=> '방문자 IP확인',
			);

if (isset($_POST)) extract($_POST);
if (isset($_GET)) extract($_GET);

$mode = isset($mode) ? $mode : '';
$location = "접속관리 > $str_mode[$mode]";
$domain = $_SERVER['HTTP_HOST'];

// 날짜 기간 계산
if (!isset($year)) list ($year,$month,$day) = array(substr($_COOKIE['logDate'],0,4),substr($_COOKIE['logDate'],4,2),substr($_COOKIE['logDate'],6,2));
if (isset($_GET['today'])) $year = 0;

$day	+= isset($direc) ? $direc : 0;
$time	= ($year)  ? mktime(0,0,0,$month,$day,$year)    : time();
$date	= date("Ymd",$time);

setCookie('logDate',$date);

include "../_header.php";
include "../../lib/graph.class.php";

$btn_guide	= array(
			''			=> ' <a href="javascript:manual(\''.$guideUrl.'board/view.php?id=data&no=8\')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>',
			'referer'	=> ' <a href="javascript:manual(\''.$guideUrl.'board/view.php?id=data&no=9\')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>',
			'client'	=> ' <a href="javascript:manual(\''.$guideUrl.'board/view.php?id=data&no=10\')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>',
			'iplist'	=> ' <a href="javascript:manual(\''.$guideUrl.'board/view.php?id=data&no=11\')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>',
			);

$query = "select max(idx_date) from ".MINI_IP." where idx_date < '".date('Ymd')."'";
list($max_date) = $db->fetch($query);
?>
<!--
<html>
<head>
<title>MirrhLog plus+ v1.0.0</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
-->
<link rel="styleSheet" href="style.log.css">

<script>
var obj_pre;
function detail(obj){
	var obj = document.getElementById(obj);
	if (obj_pre) obj_pre.style.display = "none";
	obj.style.display = "inline";
	obj_pre = obj;
}
function move(x)
{
	var form = document.forms[0];
	form.direc.value = x;
	form.submit();
}
function chg_mode(x)
{
	var form = document.forms[0];
	form.mode.value = x;
	form.submit();
}
function utf8_han(str) {
    str = decodeURIComponent(str);
    document.write(str);
}
</script>

<div class="title title_top"><?=$str_mode[$mode]?><?=$btn_guide[$mode]?></div>

<?if($cfg['counterYN'] == '2'){ // 방문분석 사용여부 표시?>
<div style="border:solid 2px #5D644A; background:#F5FF9F; padding:5px 10px; margin:10px 0 20px 0;"><img src="../img/ico_warning.gif" align="absmiddle" style="margin-right:10px;">방문분석은 미사용 중입니다.<a href="../basic/default.php" class="extext"  style="font-weight:bold;padding-left:5;">[쇼핑몰기본관리>기본정보 설정]</a>에서 사용여부를 변경하실 수 있습니다.</div>
<?}?>

<table width=800 cellpadding=0 cellspacing=0>
<tr>
	<td>

	<form method=post action="index.php">
	<input type=hidden name=direc>
	<input type=hidden name=mode value="<?=$mode?>">

	<table width=100%>
	<tr>
		<td>

		<table>
		<tr>
			<td><input type=button onclick="move(-1)" value="◀" class=btn_white onfocus=blur()></td>
			<td style="padding:3"><b><?=date("Y/m/d",$time)?></b></td>
			<td><input type=button onclick="move(1)" value="▶" class=btn_white onfocus=blur()></td>
			<td>
		</tr>
		</table>

		</td>
		<td align=right>

		<table>
		<tr>
			<td>
			<input type=text name=year	size=4 value="<?=date('Y',$time)?>" class=line style="text-align:right">년
			<input type=text name=month size=2 value="<?=date('m',$time)?>" class=line style="text-align:right">월
			<input type=text name=day	size=2 value="<?=date('d',$time)?>" class=line style="text-align:right">일
			<input type=submit value=" Go > " class=btn>
			<input type=button value="Today" onClick="location.href='<?=$_SERVER[PHP_SELF]?>?mode=<?=$mode?>&today=1'" class=btn>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	</table>

	</form>

	</td>
</tr>
<tr>
	<td><br>

<?

function detectEncoding($str, $encodingSet)
{
	foreach ($encodingSet as $v) {
		$tmp = iconv($v, $v, $str);
		if (md5($tmp) == md5($str)) return $v;
	}
	return false;
}

function changeCharset($str)
{
	if (is_string($str)==true) {
		$encodingSet = array('UTF-8','EUC-KR');

		if (($encoding = detectEncoding($str, $encodingSet))!='EUC-KR') {
			$str = ($encoding) ? iconv($encoding, 'EUC-KR', $str) : $str;
		}
	}
	return $str;
}

function mark($mark,$sub,$hit){
	global $logs, $break;
	$logs[$mark][$sub] += $hit;
	$break = true;
}

function utf8_han($str){
	$str = urlencode($str);
	return "<script>utf8_han('$str')</script>";
}

function utf16_han($str){
	return "<script>document.write(unescape(' $str '))</script>";
}

function is_utf8($string) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]       # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]       # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
   )*$%xs', $string);
}

$gp = new Graph;

switch ($mode){

case "iplist":

	include "../../lib/page.class.php";

	$pg = new Page($page,50);
	$pg->vars[page] = getVars('page',1);

	if($date >= date('Ymd')){

		$db_table = "".MINI_IP."";

		$where[] = "idx_date='$date'";

		$pg->setQuery($db_table,$where,"reg_date");
		$pg->exec();

		$result = $db->query($pg->query);

		echo "<div align=center style='padding:20'>{$pg->page[navi]}</div><table width=100%><tr><td height=1 bgcolor='#efefef' colspan=5></td></tr>";
		while ($data=$db->fetch($result)){
			$llink = (preg_match('/http:\/\//', $data[referer]))? "<a href='$data[referer]' target='_blank'>" : "";
			$rlink = (preg_match('/http:\/\//', $data[referer]))? "</a>" : '';
			echo "
			<tr>
				<td width=50 nowrap align=center class=eng><b>".($pg->idx--)."</b></td>
				<td width=110 nowrap bgcolor='#000000' style='color:#ffffff' class=engb align=center>$data[ip]</td>
				<td width=100% style='padding-left:10px'>$data[os], $data[browser]</td>
				<td width=100 nowrap align=right>".date("H시 i분 s초",$data[reg_date])."</td>
			</tr>
			<tr>
				<td></td>
				<td colspan=3 width=100% style='font:8pt tahoma'>".$llink.$data[referer].$rlink."</td>
			</tr>
			<tr><td height=1 bgcolor='#efefef' colspan=5></td></tr>
			";
		}
		echo "</table><div align=center style='padding:20'>{$pg->page[navi]}</div>";
	}else{

		$filename = "../../log/".MINI_COUNTER."/".$date.".log";
		if(file_exists($filename)){
			$fp = fopen ($filename, "r");
			$i=0;
			if(!$page)$page=1;
			while ($line = fgets($fp, 1024)){
				$arr = preg_split('/\t/',$line);
				if($i == 0){
					$total = $arr[1];
					$tot_page = ceil($total / 50);
					$pg->recode[total] =  $total;
					$pg->exec();
					echo "<div align=center style='padding:20'>{$pg->page[navi]}</div><table width=100%><tr><td height=1 bgcolor='#efefef' colspan=5></td></tr>";
				}

				if($page == $arr[0]){
					$llink = (preg_match('/http:\/\//',$arr[6]))? "<a href='$arr[6]' target='_blank'>" : "";
					$rlink = (preg_match('/http:\/\//', $arr[6]))? "</a>" : '';
					echo "
					<tr>
						<td width=50 nowrap align=center class=eng><b>".$arr[1]."</b></td>
						<td width=110 nowrap bgcolor='#000000' style='color:#ffffff' class=engb align=center>$arr[2]</td>
						<td width=100% style='padding-left:10px'>$arr[3], $arr[4]</td>
						<td width=100 nowrap align=right>".$arr[5]."</td>
					</tr>
					<tr>
						<td></td>
						<td colspan=3 width=100% style='font:8pt tahoma'><div style='text-overflow:ellipsis;overflow:hidden;width:550px' nowrap>".$llink.$arr[6].$rlink."</div></td>
					</tr>
					<tr><td height=1 bgcolor='#efefef' colspan=5></td></tr>
					";
				}

				$i++;
			}

			fclose($fp);
	}
		echo "</table><div align=center style='padding:20'>{$pg->page[navi]}</div>";
	}
	break;

case "client":
	if($date >= date('Ymd')){
		echo "
		<table width=100%>
		<col style='background-color:#cccccc;color:#000000;font:8pt tahoma;width:100'>
		<col style='padding-left:10'>
		<tr>
			<th>Operating<br>System</th>
			<td>
		";

		$query = "select os,count(*) as z from ".MINI_IP." where idx_date=$date group by os order by z desc";

		$gp->query	= $query;
		$gp->type = 1;
		$gp->barMax = 400;
		$gp->drawGraph();

		echo "
			</td>
		</tr>
		<tr bgcolor='#ffffff'><td height=30></td></tr>
		<tr>
			<th>Browser<br>UA</th>
			<td>
		";

		$query = "select browser,count(*) as z from ".MINI_IP." where idx_date='$date' group by browser order by z desc";

		$gp->reset();
		$gp->query	= $query;
		$gp->type = 1;
		$gp->drawGraph();

		echo "
			</td>
		</tr>
		</table>
		";
	}else{
		echo "
		<table width=100%>
		<col style='background-color:#cccccc;color:#000000;font:8pt tahoma;width:100'>
		<col style='padding-left:10'>
		<tr>
			<th>Operating<br>System</th>
			<td>
		";

		$query = "select os,cnt as z from ".MINI_IP_OS." where idx_date=$date order by z desc";

		$gp->query	= $query;
		$gp->type = 1;
		$gp->barMax = 400;
		$gp->drawGraph();

		echo "
			</td>
		</tr>
		<tr bgcolor='#ffffff'><td height=30></td></tr>
		<tr>
			<th>Browser<br>UA</th>
			<td>
		";

		$query = "select browser,cnt as z from ".MINI_IP_BROWSER." where idx_date='$date' order by z desc";

		$gp->reset();
		$gp->query	= $query;
		$gp->type = 1;
		$gp->drawGraph();

		echo "
			</td>
		</tr>
		</table>
		";
	}
	break;

case "referer":

	// 경로 분석

	$query = "select * from ".MINI_REFERER." where day=$date order by hit desc";
	$result = $db->query($query);

	$search = array(
		"naver"		=> "query",
		"yahoo"		=> "p",
		"daum"		=> "q",
		"empas"		=> "q",
		"google"	=> "q",
		"nate"		=> "q",
		"msn"		=> "q",
		);
	$seach_keys = array_keys($search);

	while ($data=$db->fetch($result)){

		$break		= false;
		$keyword	= "";
		$referer	= $data[referer];
		$parse		= parse_url($referer);

		$engins		= implode("|",array_keys($search));
		preg_match("/($engins)/i",$parse['host'],$engin);

		if ($engin[0] && ! preg_match('/^shopping\./',$parse['host'])) {
			$mark = $engin[0];
			switch ($mark){
				case "naver":
					if (strpos($parse['path'],"mailread")!==false) mark($mark,'이메일',$data[hit]);
					else if (strpos($parse['host'],"blog")!==false) mark($mark,'블로그',$data[hit]);
					else if (strpos($parse['host'],"kin")!==false) mark($mark,'지식인',$data[hit]);
					else if (strpos($parse['host'],"cafe")!==false) mark($mark,'카페',$data[hit]);
					else if (strpos($parse['host'],"shopping")!==false) mark($mark,'지식쇼핑',$data[hit]);
					break;
				case "daum":
					if (strpos($parse['path'],"hanmail")!==false) mark($mark,'한메일',$data[hit]);
					else if (strpos($parse['host'],"cafe")!==false) mark($mark,'다음카페',$data[hit]);
					break;
				case "empas":
					if (strpos($parse['host'],"blog")!==false) mark($mark,'블로그',$data[hit]);
					break;
			}
			if (!$break){
				$div = explode("&",$parse['query']);
				foreach($div as $q){
					list($k, $v) = explode("=", $q, 2);

					if ($k == $search[$mark]) {
						$keyword = $v;
						break;
					}

				}
				if ($keyword){
					$keyword = changeCharset( urldecode($keyword) );
					if (preg_match('/%u[0-9A-Z]{4}/',$keyword)) $keyword = utf16_han($keyword);
					$log[$mark][$keyword] += $data[hit];
					mark($mark,'검색엔진',$data[hit]);
				} else mark($mark,'기타경로',$data[hit]);
			}
		} else if ($referer=="Direct Contact (Typing or Bookmark)"){
			$mark = $domain; mark($mark,'Typing or Bookmark',$data[hit]);
		} else if (strpos($parse['host'],$domain)!==false){
			$mark = $domain; mark($mark,'내부링크',$data[hit]);
		}

		if (!$break){
			$mark = "*etc";
			mark($mark,'기타경로',$data[hit]);
		}

		$referer = htmlspecialchars($referer);
		if ($referer!="Direct Contact (Typing or Bookmark)") $referer = "<a href='$data[referer]' target=_blank>$referer </a>";

		$buffer[$mark][referer][] = $referer;
		$buffer[$mark][hit][]	  = $data[hit];
	}

	$logs_key = @array_keys($logs);
	for ($i=0;$i<count($logs);$i++){
		$log[engin][$logs_key[$i]] = array_sum($logs[$logs_key[$i]]);
	}

	@arsort($log[engin]);
	$gp->reset();
	$gp->type = 1;
	$gp->link = 1;
	$gp->barMax = 515;
	$gp->out = $log[engin];
	$gp->drawGraph();

	echo "<br></td></tr>";

	for ($i=0;$i<count($logs);$i++){
		echo "
		<tr id={$logs_key[$i]} style='display:none'><td>
		<div align=center style='font:bold 35px arial;padding-bottom:30'><u>{$logs_key[$i]}</u></div>
		";

		arsort($logs[$logs_key[$i]]);
		$gp->reset();
		$gp->type = 1;
		$gp->barMax = 515;
		$gp->out = $logs[$logs_key[$i]];
		$gp->drawGraph();

		echo "<br>";

		if ($log[$logs_key[$i]]){
			arsort($log[$logs_key[$i]]);
			$gp->reset();
			$gp->type = 1;
			$gp->barMax = 515;
			$gp->out = $log[$logs_key[$i]];
			$gp->drawGraph();
		}

		echo "<br>
		<table width=100% border=1 bordercolor='#cccccc' style='border-collapse:collapse;table-layout:fixed'>
		<col align=center bgcolor='#f7f7f7'><col style='padding-left:10'><col align=right style='padding-right:5' bgcolor='#f7f7f7'>
		";

		for ($j=0;$j<count($buffer[$logs_key[$i]][referer]);$j++){
			echo "
			<tr height=22>
				<td width=30 nowrap class=engb>".($j+1)."</td>
				<td style='line-height:18px'>{$buffer[$logs_key[$i]][referer][$j]} </td>
				<td width=50 nowrap>{$buffer[$logs_key[$i]][hit][$j]}</td>
			</tr>
			";
		}

		echo "</table></td></tr>";
	}
	break;

default :

	if(!$max_date){
	// 오늘 접속자 현황

	/*
	$query	= "select * from ".MINI_COUNTER." where day=$date";
	$data	= mysql_fetch_array(mysql_query($query));

	for ($i=0;$i<24;$i++) $out[] = $data[$i+3];

	$gp->barMax = 100;
	$gp->barSize = 12;
	$gp->out = $out;
	$gp->drawGraph();
	*/

	?>

<script src="common.js"></script>
<script>bar_flash('2Dbar.swf',800,160,'<?=$date?>',1);</script>

	<?

	echo "</td></tr><tr><td bgcolor=#f7f7f7><b>▲ ".date("Y-m-d",$time)." 접속자 현황</b></td></tr><tr><td><br>";

	// 이번달 접속자 현황

	/*
	$gp->reset();
	$gp->query	= "select right(day,2)+0,uniques from ".MINI_COUNTER." where day like '".substr($date,0,6)."%'";
	$gp->date = $date;
	$gp->calcuKey = "+1";
	$gp->ea = date("t",$time);
	$gp->drawGraph();
	*/
	?>

<script src="common.js"></script>
<script>bar_flash('2Dbar.swf',800,160,'<?=$date?>',2);</script>

	<?
	echo "</td></tr><tr><td bgcolor=#f7f7f7><b>▲ ".date("Y-m",$time)." 접속자 현황</b></td></tr><tr><td><br>";

	// 이번달 페이지뷰 현황
	/*
	$gp->reset();
	$gp->query	= "select right(day,2)+0,pageviews from ".MINI_COUNTER." where day like '".substr($date,0,6)."%'";
	$gp->date = $date;
	$gp->calcuKey = "+1";
	$gp->ea = date("t",$time);
	$gp->drawGraph();
	*/
	?>

<script src="common.js"></script>
<script>bar_flash('2Dbar.swf',800,160,'<?=$date?>',3);</script>

	<?
	echo "</td></tr><tr><td bgcolor=#f7f7f7><b>▲ ".date("Y-m",$time)." 페이지뷰 현황</b></td></tr><tr><td><br>";

	// 시간별 접속자 현황
	/*
	$out	= "";
	$query	= "select * from ".MINI_COUNTER." where day=0";
	$data	= mysql_fetch_array(mysql_query($query));

	for ($i=0;$i<24;$i++) $out[] = @round($data[$i+3]/$data[1]*100,1);

	$gp->reset();
	$gp->out = $out;
	$gp->drawGraph();
	*/
	?>

<script src="common.js"></script>
<script>bar_flash('2Dbar.swf',800,160,'<?=$date?>',4);</script>

	<?
	echo "</td></tr><tr><td bgcolor=#f7f7f7><b>▲ 시간별 접속자 현황</b></td></tr><tr><td style='padding:10'>";
	}
}

?>

	</td>
</tr>
</table>

<? include "../_footer.php"; ?>
<?
if($max_date){
	echo "<script>popupLayer('data.log.php',500,300);</script>";
	exit;
}
?>
