<?
	include "../lib.php";

	$dong = $_GET['dong'];	// 검색 값
	$zcd1 = $_GET['zcd1'];	// opener의 우편번호 1 ID
	$zcd2 = $_GET['zcd2'];	// opener의 우편번호 2 ID
	$ad1 = $_GET['ad1'];	// opener의 주소 1 ID
	$ad2 = $_GET['ad2'];	// opener의 상세주소 ID

	if($dong) {

		$_param = array(
			'keyword' => $dong,
			'where' => 'dong',
			'page' => isset($_GET['page']) ? $_GET['page'] : 1,
			'page_size' => 15,
		);

		$result = Core::loader('Zipcode')->get($_param);
		$pg		= $result->page;

	}
?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>++ GODOMALL ++</title>
<script id="dynamic"></script>
<script src="../common.js"></script>
<link rel="styleSheet" href="../style.css">
<script>
	function zipcode(zipcode, address) {
		var zcd1 = document.getElementById('zcd1').value;
		var zcd2 = document.getElementById('zcd2').value;
		var ad1 = document.getElementById('ad1').value;
		var ad2 = document.getElementById('ad2').value;

		if(zcd1 && zcd2) {
			var r_zipcode = zipcode.split("-");
			opener.document.getElementById(zcd1).value = r_zipcode[0];
			opener.document.getElementById(zcd2).value = r_zipcode[1];
		}
		if(ad1) opener.document.getElementById(ad1).value = address;
		if(ad2) opener.document.getElementById(ad2).focus();

		window.close();
	}
</script>
</head>

<body topmargin="5" margintop="5" leftmargin="10" rightmargin="10" marginwidth="10" marginheight="5">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td><img src="../img/title_address.gif" border="0"></td></tr>
<tr><td height="4" background="../img/bg_ex.gif"></td></tr>
<tr><td height="15"></td></tr>
</table>

<form onsubmit="return chkForm(this)">
<input type="hidden" name="zcd1" id="zcd1" value="<?=$zcd1?>">
<input type="hidden" name="zcd2" id="zcd2" value="<?=$zcd2?>">
<input type="hidden" name="ad1" id="ad1" value="<?=$ad1?>">
<input type="hidden" name="ad2" id="ad2" value="<?=$ad2?>">

<table border="0" cellspacing="0" cellpadding="0" align="center">
<tr><td><input type="text" name="dong" value="<?=$dong?>" required label="지역명"></td>
<td width="5"></td>
<td><input type="image" src="../img/btn_search_s.gif" class="null"></td></tr>
</table>
</form>
<p>
<table width="100%" cellpadding="0" cellspacing="0">
<col width="80" align="center">
<tr><td colspan="2" bgcolor="#CCCCCC" height="2"></td></tr>
<tr height="25">
	<th>우편번호</th>
	<th>주소</th>
</tr>
<tr><td colspan="2" bgcolor="#CCCCCC" height="2"></td></tr>
<? if (isset($result)){ foreach ($result as $data) { ?>
<tr <? if($data['_no']%2) { ?>bgcolor="#F7F7F7"<? } ?> height="25">
	<td><font class="ver81" color="#545454"><?=$data['zipcode']?></td>
	<td><a href="javascript:zipcode('<?=$data['zipcode']?>','<?=$data['sido']." ".$data['gugun']." ".$data['dong']?>')"><font color="#353535"><?=$data['sido']." ".$data['gugun']." ".$data['dong']." ".$data['bunji']?></a></td>
</tr>
<? } } ?>
</table>

<div align="center">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
<tr><td colspan="2" height="10"></td></tr>
<tr><td colspan="2" bgcolor="#CCCCCC" height="2"></td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td align="center"><font class="ver8"><?=isset($result) ? $pg->page['navi'] : ''?></td></tr>
<tr><td colspan="2" height="15"></td></tr>
</table>
</div>
</body>
</html>
