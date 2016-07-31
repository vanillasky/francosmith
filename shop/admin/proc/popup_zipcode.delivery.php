<?
include "../lib.php";

$area = $_GET['area'];
$idx = $_GET['idx'];

if (!$form) $form = "opener.document.forms[0]";

if ($area){

	$_param = array(
		'keyword' => $area,
		'where' => 'sido|gugun|dong',
	);

	$result = Core::loader('Zipcode')->get($_param);

}
?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>++ GODOMALL ++</title>
<script id=dynamic></script>
<script src="../common.js"></script>
<link rel="styleSheet" href="../style.css">

<script language="javascript">
function putArea(mod){
	var sel = document.getElementsByName('retsel')[0];
	var val = sel[sel.selectedIndex].value;
	var area1 = document.getElementsByName('zipcode[]')[0].value;
	var area2 = document.getElementsByName('zipcode[]')[1].value;

	if(val){
		document.getElementsByName('zipcode[]')[mod].value=val;
	}
}
function putZipcode(){
	var idx = document.getElementsByName('idx')[0].value;
	if(document.getElementsByName('zipcode[]')[0].value >= document.getElementsByName('zipcode[]')[1].value){
		var zip2 = document.getElementsByName('zipcode[]')[0].value;
		var zip1 = document.getElementsByName('zipcode[]')[1].value;
	}else{
		var zip1 = document.getElementsByName('zipcode[]')[0].value;
		var zip2 = document.getElementsByName('zipcode[]')[1].value;
	}
	if(!zip1 || !zip2){
		alert('우편번호를 입력해주세요!');
		return false;
	}
	opener.document.getElementsByName('areaZip1[]')[idx].value = zip1;
	opener.document.getElementsByName('areaZip2[]')[idx].value = zip2;
	self.close();
}
</script>

</head>

<body topmargin="5" margintop="5" leftmargin="10" rightmargin="10" marginwidth="10" marginheight="5">

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td class="title title_top">우편번호 검색</font></tr>
<tr><td height="10"></td></tr>
<tr><td bgcolor="#cccccc" height="1"></td></tr>
<tr><td height="15"></td></tr>
</table>

<div align="center" style="padding-bottom:8px;"><font class="extext">주소지의 동(읍/면)으로 검색하신 후 검색결과에서 주소를 선택하세요.<br>선택 후 검색주소입력 버튼으로 시작주소와 끝주소를 각각 넣어주세요.</font></div>

<form onsubmit="return chkForm(this)" method='get'>
<input type="hidden" name="idx" value="<?=$idx?>">

<table border="0" cellspacing="0" cellpadding="0" align="center">
<tr><td><input type=text name="area" value="<?=$area?>" required label="지역명"></td>
<td width="5"></td>
<td><input type="image" src="../img/btn_search_s.gif" class="null"></td></tr>
</table>

<p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px #dddddd solid;">
<tr>
	<td style="padding:10;border:1px #ffffff solid;letter-spacing:-1;" align="center" bgcolor="#efefef">
	<strong>검색결과</strong>&nbsp;&nbsp;
	<select name="retsel" style="width:340px">
		<? if (isset($result)){ foreach ($result as $data) { ?>
		<option value='<?=str_replace('-','',$data[zipcode])?>'><?=$data[sido]?> <?=$data[gugun]?> <?=$data[dong]?> <?=$data[bunji]?></option>
		<?
		}
		}else{
		?>
		<option value=''>검색결과가 없습니다.</option>
		<?}?>
	</select>
	</td>
</tr>
</table>
<div align="center">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
<tr><td height="10"></td></tr>
<tr><td align="center">
	<table border="0" cellspacing="0" cellpadding="4">
	<tr>
	<td></td>
	<td align="center"><a href='javascript:var a = putArea(0)'><img src="../img/btn_addinput.gif" border="0" align="absmiddle"></a></td>
	<td></td>
	<td align="center"><a href='javascript:var a = putArea(1)'><img src="../img/btn_addinput.gif" border="0" align="absmiddle"></a></td>
	<td></td>
	</tr>
	<tr>
	<td>우편번호 : </td>
	<td><input type="text" size='7' name="zipcode[]" style="background:#f3f3f3;text-align:center;" value="<?=$_GET['zipcode'][0]?>" readonly /></td>
	<td>부터</td>
	<td><input type="text" style="background:#f3f3f3;text-align:center;" size='7' name="zipcode[]" value="<?=$_GET['zipcode'][1]?>" readonly /></td>
	<td>까지</td>
	</tr>
	</table>
</td></tr>
<tr><td height="15"></td></tr>
<tr><td bgcolor="#cccccc" height="1"></td></tr>
<tr><td height="10"></td></tr>
<tr><td style="padding-top:10px"  colspan="3" align=center class="noline"><input type="image" src="../img/btn_deli_area_add.gif" onclick="putZipcode()"></td></tr>
</table>
</div>
</form>
</body>
</html>
