<?
include "../_header.popup.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

include "./main.state.array.php";
@include "../../conf/admin_main_state.php";

	if($_POST['mode'] == "mainConf"){
		$qfile->open("../../conf/admin_main_state.php");
		$qfile->write("<?\n" );
		foreach ( (array)$_POST['main'] as $mKey => $mVal ){
			$qfile->write("\$adminMainState['" . $mKey . "']['chk'] = \"".$mVal."\"; \n" );
		}
		$qfile->write("?>" );
		$qfile->close();
		@chMod( "../../conf/admin_main_state.php", 0757 );
		echo "
		<script>
		parent.NowMainDisplay.inData();
		parent.closeLayer();
		</script>";
	}

?>

<div class="title title_top">주요현황 출력항목 설정 <font class=extext>관리자 메인화면에 보여지는 주요현황 항목을 설정하세요</div>


<div style="padding-top:10px"></div>

<form method="post" action="" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="mainConf" />
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="ebebeb"><tr><td bgcolor="e8e8e8">
<table width="100%" cellpadding="2" cellspacing="1" border="0" bgcolor="e8e8e8">
<?
	$i = 0;
	foreach($adminMainState AS $mKey => $mVal){
		if($i == 0 || $i % 2 == 0) echo "<tr>";
?>
	<td bgcolor="f6f6f6" width="30%" align="left" style="padding:3px 0px 0px 10px"><font class=small1 color=666666><?=$mVal['title']?></font></td>
	<td bgcolor="ffffff" width="20%" class="noline"><input type="checkbox" name="main[<?=$mKey?>]" value="on" <? if($mVal['chk'] == "on") echo" checked"; ?> /> <font class=small1 color=666666>메인에 출력</font></td>
<?
		$i++;
		if($i % 2 == 0) echo "</tr>";
	}
?>
</table>
</td></tr></table>

<div style="padding-top:10px"></div>

<div style="margin-bottom:10px;padding-top:10px;" class=noline align="center">
<input type="image" src="../img/btn_confirm_s.gif">
</form>

<div style="padding-top:10px;text-align:left;">
<font class="extext">* 매출액은 입금확인일 기준으로 보여지며, 입금확인,배송준비중,배송중,배송완료 건에 대해 표시합니다.</font><br />
<font class="extext">* 주문건수는 주문일을 기준으로 보여지며, 모든 주문건수를 표시를 합니다.</font><br />
<font class="extext">* 입금확인은 입금확인일 기준으로 보여지며, 입금확인 건에 대해서만 표시합니다.</font><br />
<font class="extext">* 배송완료는 배송완료일 기준으로 보여지며, 배송완료 건에 대해서만 표시합니다.</font><br />
<font class="extext">* 취소/환불/반품은 입금확인일 기준으로 보여지며, 취소/환불/반품 건에 대해서만 표시합니다.</font><br />
<font class="red">* 주문건이 많은 경우 매출액 항목을 출력하게 되면 화면로딩이 느려질 수 있습니다.</font><br />
</div>