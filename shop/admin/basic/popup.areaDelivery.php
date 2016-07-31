<?
include "../lib.php";

$area = $_GET['area'];
$idx = $_GET['idx'];

if (!$form) $form = "opener.document.forms[0]";

if ($area){

	$_param = array(
		'keyword' => $area,
		'where' => 'sido|gugun',
		'page' => isset($_GET['page']) ? $_GET['page'] : 1,
		'page_size' => 15,
		'group' => 'sido, gugun',
	);

	$result = Core::loader('Zipcode')->get($_param);
	$pg		= $result->page;

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

	window.close();
}

</script>

</head>

<body topmargin=5 margintop=5 leftmargin=10 rightmargin=10 marginwidth=10 marginheight=5>

<table border=0 cellspacing=0 cellpadding=0 width=100%>
<tr><td class="title title_top">지역추가하기</font></tr>
<tr><td height=15></td></tr>
</table>

<div align=center><font class=extext>시, 군, 구까지 검색이 가능합니다</div>
<div align=center style="padding:2px 0px 10px 0px">(예. 제주시, 강남구,  울릉군 등)</font></div>

<form onsubmit="return chkForm(this)">
<input type="hidden" name="idx" value="<?=$idx?>">

<table border=0 cellspacing=0 cellpadding=0 align=center>
<tr><td><input type=text name="area" value="<?=$area?>" required label="지역명"></td>
<td width=5></td>
<td><input type="image" src="../img/btn_search_s.gif" class="null"></td></tr>
</table>

</form><p>

<table width=100% cellpadding=0 cellspacing=0>
<col align=center width=30><col width=30 align=center><col>
<tr><td colspan="3" bgcolor="#cccccc" height=2></td></tr>
<tr height=25>
<th>선택</th>
<th>번호</th>
<th>지역명</th>
</tr>
<tr><td colspan="3" bgcolor="#cccccc" height=2></td></tr>
<? if (isset($result)) { $lidx=0; foreach ($result as $data) { ?>
<tr <? if ($lidx++%2){ ?>bgcolor="#f7f7f7"<? } ?> height=25>
	<td><input type="checkbox" name="chkarea" value="<?=$data[sido]." ".$data[gugun]?>"></td>
	<td><font class=ver81 color=545454><?=$lidx?></td>
	<td style="padding-left:5"><font color=353535><?=$data[sido]." ".$data[gugun]?></td>
</tr>
<? }} ?>
</table>
<div align=center>
<table border=0 cellspacing=0 cellpadding=0 align=center width=100%>
<tr><td colspan="3" height=10></td></tr>
<tr><td colspan="3" bgcolor="#cccccc" height=2></td></tr>
<tr><td colspan="3" height=10></td></tr>
<tr><td align=center><font class=ver8><?=$pg->page['navi']?></td></tr>
<tr><td style="padding-top:10px"  colspan="3" align=center><input type="image" src="../img/btn_deli_area_add.gif" onclick="putArea()"></td></tr>
<tr><td colspan="3" height=15></td></tr>
</table>
</div>

</body>
</html>
<script language="javascript">
function putArea(){
	var obj = document.getElementsByName('chkarea');
	var idx = document.getElementsByName('idx')[0].value;
	var ozn = opener.document.getElementsByName('overZipcodeName[]');
	var val = '';
	for(var i=0;i<obj.length;i++){
		if(obj[i].checked){
			for(var j=0;j<ozn.length;j++) {
				if(ozn[j].value.indexOf(obj[i].value) != -1) {
					alert("'" + obj[i].value + "'은(는) 이미 등록된 지역입니다.");
					return false;
				}
			}
			val += ',' + obj[i].value;
		}
	}
	if(ozn[idx].value == '')val = val.substring(1);
	ozn[idx].value += val;
	self.close();
}
</script>
