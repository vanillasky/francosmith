<?php

$location = "����ϼ����� > ����ϼ� ���λ�ǰ ���� ����";
include "../_header.php";
include "../../conf/config.mobileShop.php";
include "../../conf/config.mobileShop.main.php";

$query = "
select
	distinct a.mode,a.goodsno,b.goodsnm,b.img_s,c.price
from
	".GD_GOODS_DISPLAY_MOBILE." a,
	".GD_GOODS." b,
	".GD_GOODS_OPTION." c
where
	a.goodsno=b.goodsno
	and a.goodsno=c.goodsno and link and go_is_deleted <> '1'
order by sort
";

$res = $db->query($query);
while ($data=$db->fetch($res)) $loop[$data[mode]][] = $data;

?>

<form id=form action="indb.php" method=post>
<input type=hidden name=mode value="disp_main">

<?
for ($i=0;$i<2;$i++){
	$checked[tpl][$i][$cfg_mobile_step[$i][tpl]] = "checked";
	$selected[img][$i][$cfg_mobile_step[$i][img]] = "selected";
?>

<div class=title <?if(!$i){?>style="margin-top:0"<?}?>>����ϼ� ���λ�ǰ���� <?=$i+1?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a> <span><a href="javascript:popup2('<?php echo $cfgMobileShop['mobileShopRootDir']?>','320','480','1');"><font color=0074BA>[����ȭ�麸��]</font></a></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>����</td>
	<td><input type=text name=title[] value="<?=$cfg_mobile_step[$i][title]?>" class=lline></td>
</tr>
<tr>
	<td>�������</td>
	<td class=noline>
	<input type=checkbox name=chk[<?=$i?>] <? if ($cfg_mobile_step[$i][chk]){ ?>checked<?}?>> üũ�� ����ϼ� ������������ ����̵˴ϴ�
	</td>
</tr>
<tr>
	<td>���÷�������</td>
	<td>

	<table>
	<col align=center span=2>
	<tr>
		<td><img src="../img/goodalign_style_01.gif"></td>
		<td><img src="../img/goodalign_style_02.gif"></td>
	</tr>
	<tr class=noline>
		<td><input type=radio name=tpl[<?=$i?>] value="tpl_01" checked <?=$checked[tpl][$i][tpl_01]?>></td>
		<td><input type=radio name=tpl[<?=$i?>] value="tpl_02" <?=$checked[tpl][$i][tpl_02]?>></td>
	</tr>
	</table>
	<font class=extext>
		[�������� ����]<br />
		����ȭ�� : �� ���ο� 4���� ��ǰ�� ���÷��� �˴ϴ�.<br />
		����ȭ�� : �� ���ο� 6���� ��ǰ�� ���÷��� �˴ϴ�.<br />
		(����ȭ��꼼��ȭ�� ��ȯ�� ��ǰ ���÷��� ���� �ڵ����� �˴ϴ�)<br />
	</font>
	</td>
</tr>
<tr>
	<td>������� ��ǰ��</td>
	<td><input type=text name=page_num[] value="<?=$cfg_mobile_step[$i][page_num]?>" class="rline"> �� <font class=extext>������������ �������� �� ��ǰ���Դϴ�</td>
</tr>
<tr>
	<td>������ ��ǰ����<div style="padding-top:3px"></div>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><font class=extext_l>[��ǰ�������� ���]</font></a>
	<div style="padding-top:3px"></div>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a>
	</td>
	<td>
		<div style="padding-top:5px;z-index:-10"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step<?=$i?>[]', 'step<?=$i?>X');" align="absmiddle" /> <font class="extext">������: ��ǰ���� �� �ݵ�� �ϴ� �����ư�� �����ž� ���� ������ �˴ϴ�.</font></div>
		<div style="position:relative;">
			<div id=step<?=$i?>X style="padding-top:3px">
				<? if ($loop[$i]){ foreach ($loop[$i] as $v){ ?>
					<?=goodsimg($v[img_s],'40,40','',1)?>
					<input type=hidden name=e_step<?=$i?>[] value="<?=$v[goodsno]?>">
				<? }} ?>
			</div>
		</div>
	</td>
</tr>
</table>


<? } ?>


<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="../mobileShop/mobile_goods_list.php"><img src='../img/btn_list.gif'></a>
</div>

</form>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<!--<tr><td><img src="../img/arrow_blue.gif" align=absmiddle>��ǰ���÷��� �������</td></tr>-->
<tr><td><img src="../img/icon_list.gif" align=absmiddle>��ǰ�����ϱ� ��ư�� ���� ������ ��ǰ�� �������ּ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>������ â�� �ݰ� �Ʒ��� �����ư�� �����ž� ���� ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>������ �� �ִ� ��ǰ�� �ִ밳���� 300�� �Դϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>����ϼ��� ������ ��ǰ�� �������� ������, PC�� ���λ�ǰ������ ������ ��ǰ����Ʈ�� ��µ˴ϴ�. </td></tr>
</table>

</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>