<?
$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";
?>

<form name="fm" method="post" action="../design/indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="intro_save" />
<input type=hidden name=tplSkinWork value="<?=$cfg['tplSkinWork']?>">

<div class="title title_top">인트로(회원) 페이지 디자인<span>회원전용 인트로 페이지의 디자인을 수정합니다.</span></div>

<?=$workSkinStr?>


<!--<div style="margin:10px 0 10px 0;"><font class=extext>공사중 페이지를 보려면 '<a href="/shop/main/intro.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://도메인명</u></b></font></a>' 을 클릭하세요.</div>
<div style="margin:10px 0 10px 0;"><font class=extext>메인페이지를 보려면 '<a href="/shop/main/index.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://도메인명/shop/main/index.php</u></b></font></a>' 를 클릭하세요.</div>-->

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], 'main/intro_member.htm'); ?>

<?
{ // 디자인코디툴

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '99%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="HTML 소스"';
	$tmp['tplFile']		= "/main/intro_member.htm";

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>
<input type="hidden" name="skin_file" value="<?=$tmp['tplFile']?>"/>
<div style="padding:20px" align="center">
<input type="image" src="../img/btn_register.gif" class="null" />
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class=small_ex>
<tr><td>성인인증 인트로 페이지는 유형에 따라 2종류로 제공됩니다.</td></tr>
<tr><td>① 메인 페이지 접속이 성인 또는 회원만 접근 가능한 인트로 페이지</td></tr>
<tr><td>&nbsp;- 성인 또는 회원만 접근이 가능한 사이트에 사용됩니다. 성인을 인증할 수 있는 본인확인 인증서비스를 신청하고 이용하여 주세요.</td></tr>
<tr><td>② 메인 페이지 접속이 회원만 접근 가능한 인트로 페이지</td></tr>
<tr><td>&nbsp;- 회원만 접근이 가능한 사이트에 사용되며, 상품 구매는 회원만 가능합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>




<script>
table_design_load();
setHeight_ifrmCodi();
</script>
