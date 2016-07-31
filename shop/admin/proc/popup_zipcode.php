<?
include "../lib.php";

$dong = $_GET['dong'];
$form = $_GET['form'];

if (!$form) $form = "opener.document.forms[0]";

if ($dong){

	$_param = array(
		'keyword' => $dong,
		'where' => 'dong',
		'page' => isset($_GET['page']) ? $_GET['page'] : 1,
		'page_size' => 15,
	);

	$result = Core::loader('Zipcode')->get($_param);
	$pg	    = $result->page;

}

?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>++ GODOMALL ++</title>
<script id=dynamic></script>
<script src="../common.js"></script>
<link rel="styleSheet" href="../style.css">

<script>

function zipcode(zipcode,address)
{
	var form = <?=$form?>;
	var r_zipcode = zipcode.split("-");
	form['zipcode[]'][0].value = r_zipcode[0];
	form['zipcode[]'][1].value = r_zipcode[1];
	form.address.value = address;
	if (form.address_sub){
		form.address_sub.value = "";
		form.address_sub.focus();
	} else {
		form.address.focus();
		form.address.value += " ";
	}
	window.close();
}

</script>

</head>

<body topmargin=5 margintop=5 leftmargin=10 rightmargin=10 marginwidth=10 marginheight=5>

<table border=0 cellspacing=0 cellpadding=0 width=100%>
<tr><td><img src="../img/title_address.gif" border=0></td></tr>
<tr><td height=4 background="../img/bg_ex.gif"></td></tr>
<tr><td height=15></td></tr>
</table>

<div align="center" style="padding-bottom:8px;"><span class="extext">찾고자 하는 주소의 동(읍/면) 이름을 입력하세요<br>[예]삼성동,수서동,역삼동</span></div>

<form onsubmit="return chkForm(this)">
<input type=hidden name=form value="<?=$form?>">

<table border=0 cellspacing=0 cellpadding=0 align=center>
<tr><td><input type=text name=dong value="<?=$dong?>" required label="지역명"></td>
<td width=5></td>
<td><input type=image src=../img/btn_search_s.gif class=null></td></tr>
</table>

</form><p>

<table width=100% cellpadding=0 cellspacing=0>
<col width=80 align=center>
<tr><td colspan=2 bgcolor="#cccccc" height=2></td></tr>
<tr height=25>
	<th>우편번호</th>
	<th>주소</th>
</tr>
<tr><td colspan=2 bgcolor="#cccccc" height=2></td></tr>
<? if (isset($result)){ foreach ($result as $data) { ?>
<tr <? if ($data['_no']%2){ ?>bgcolor="#f7f7f7"<? } ?> height=25>
	<td><font class=ver81 color=545454><?=$data[zipcode]?></td>
	<td><a href="javascript:zipcode('<?=$data[zipcode]?>','<?=$data[sido]." ".$data[gugun]." ".$data[dong]?>')"><font color=353535><?=$data[sido]." ".$data[gugun]." ".$data[dong]." ".$data[bunji]?></a></td>
</tr>
<? }} ?>
</table>

<div align=center>
<table border=0 cellspacing=0 cellpadding=0 align=center width=100%>
<tr><td colspan=2 height=10></td></tr>
<tr><td colspan=2 bgcolor="#cccccc" height=2></td></tr>
<tr><td colspan=2 height=5></td></tr>
<tr><td align=center><font class=ver8><?=isset($result) ? $pg->page['navi'] : ''?></td></tr>
<tr><td colspan=2 height=15></td></tr>
</table>
</div>

</body>
</html>
