<?php
include "../_header.popup.php";

$font_list = $db->_select("select * from gd_webfont order by font_no");
$godofont = $config->load('godofont');

?>
<style type="text/css">

<? foreach($font_list as $each_font): ?>
	<? foreach(explode(',',$each_font['enable_size']) as $each_size): ?>
	<?
		$fontCode = $each_font['font_code'].'_'.sprintf('%02d',$each_size);
	?>

	@font-face {
		font-family: <?=$fontCode?>;
		src: url(../../proc/fonteot.php?name=<?=$fontCode?>);
	}
	.<?=$fontCode?> {font-family:<?=$fontCode?> !important;font-size:<?=$each_size?>pt !important}
	<? endforeach; ?>
<? endforeach; ?>


</style>


<script>

document.observe("dom:loaded", function() {
	var frm = $('frmFont');
	frm.setValue('major_font',"<?=$godofont['major_font']?>");
});

function checkMajor(obj) {
	if(obj.value=="") {
		return true;
	}
	var elTr = Element.extend(obj.parentNode.parentNode);
	if(!elTr.select("[type=checkbox]")[0].checked) {
		alert('��뿩�θ� �����ϼž� ��ǥ��Ʈ�� ����ϽǼ� �ֽ��ϴ�');
		return false;
	}
	else {
		return true;
	}


}
</script>


<form name="fmList" method="post" action="indb.webfont.php" target="ifrmHidden" id="frmFont">
<div class="title title_top">��� ��Ʈ ����<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=16')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<table cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="8"></td></tr>
<tr class="rndbg">
	<th>��Ʈ��</th>
	<th>������</th>
	<th>����</th>
	<th>������</th>
	<th>HTML Class��</th>
	<th>��뿩��</th>
	<th>��ǥ</th>
</tr>
<tr><td class="rnd" colspan="8"></td></tr>

<col align="center" width="120"/>
<col align="center" width="100" />
<col align="center" width="180" />
<col align="center" width="70" />
<col align="center" width="150" />
<col align="center" width="60" />
<col align="center" width="60" />

<? foreach($font_list as $each_font): ?>
<?
	$ar_enable=explode(',',$each_font['enable_size']);
	$ar_use=explode(',',$each_font['use'])
?>

<tr><td height="4" colspan="8"></td></tr>
<tr>
	<td rowspan="<?=count($ar_enable)?>"><?=$each_font['font_name']?></td>
	<td rowspan="<?=count($ar_enable)?>"><?=$each_font['expire_start']?> ~<br> <?=$each_font['expire_end']?></td>

	<? foreach($ar_enable as $k=>$v): ?>
	<? if($k): ?> <tr> <?endif;?>
	<td height="20" class="<?=$each_font['font_code']?>_<?=sprintf('%02d',$v)?>" style="color:#4c4c4c">�����ٶ�abcdABCD</td>
	<td height="20" style="font-size:8pt"><?=(int)$v?>pt</td>
	<td height="20" style="font-size:8pt"><?=$each_font['font_code']?>_<?=sprintf('%02d',$v)?></td>
	<td height="20" class="noline">
		<? if(in_array($v,$ar_use)): ?>
			<input type="checkbox" name="use[<?=$each_font['font_code']?>][]" value="<?=$v?>" checked>
		<? else: ?>
			<input type="checkbox" name="use[<?=$each_font['font_code']?>][]" value="<?=$v?>">
		<? endif; ?>
	</td>
	<td height="20" class="noline">
		<input type="radio" name="major_font" value="<?=$each_font['font_code']?>_<?=sprintf('%02d',$v)?>" onmousedown="return checkMajor(this)">
	</td>
	<? endforeach; ?>



<tr><td colspan="8" class="rndline"></td></tr>
<? endforeach; ?>
<tr>
	<td colspan="6" align="right" style="font-size:8pt">��ǥ��Ʈ������</td>
	<td class="noline">
		<input type="radio" name="major_font" value="">
	</td>
</tr>
</table>

<br><br>


<div style="width:800px;text-align:center;margin-top:30px">
	<input type="image" src="../img/btn_register.gif" style="border:none">
</div>
</form>

<div id="MSG01">
<table cellpadding="2" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��� ������ ��Ʈ ����Ʈ�Դϴ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��뿩�θ� üũ�Ͽ��߸� ����Ʈ�� �ش� ��Ʈ�� ������� ��� �� �� �ֽ��ϴ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǥ��Ʈ�� �����Ͽ� ����Ʈ ��ü�� ��Ʈ�� �ϰ������� ���� �� �� �ֽ��ϴ�</td>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�±׿� ���� �����ϴ� ����� �±׿� HTML Class���� �־��ֽø� �˴ϴ�<br>
&nbsp; &nbsp; &nbsp;��)  &lt;div class='yd_kokossing_09'&gt;�ؽ�Ʈ&lt;/div&gt;</td>
</tr>

</table>
</div>
<script>cssRound('MSG01')</script>


<script>
table_design_load();
setHeight_ifrmCodi();
document.observe("dom:loaded", function() {
	parent.document.getElementById('leftfooter').src = "../img/footer_left.gif";
	parent._ID('sub_left_menu').style.display = "none";
	parent._ID('btn_menu').style.display = "none";
	parent._ID('leftMenu').style.display = "block";

	var menus = parent.document.getElementsByName("navi");
	for(i=0;i<menus.length;i++) {
		if(menus[i].href && /iframe\.webfont\.setting\.php/.test(menus[i].href)) {
			menus[i].style.fontWeight='bold';
		}
		else {
			menus[i].style.fontWeight='';
		}
	}
});
</script>