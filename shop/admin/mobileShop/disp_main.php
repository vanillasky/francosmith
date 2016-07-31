<?php

$location = "모바일샵관리 > 모바일샵 메인상품 진열 설정";
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

<div class=title <?if(!$i){?>style="margin-top:0"<?}?>>모바일샵 메인상품진열 <?=$i+1?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a> <span><a href="javascript:popup2('<?php echo $cfgMobileShop['mobileShopRootDir']?>','320','480','1');"><font color=0074BA>[메인화면보기]</font></a></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>설명</td>
	<td><input type=text name=title[] value="<?=$cfg_mobile_step[$i][title]?>" class=lline></td>
</tr>
<tr>
	<td>사용유무</td>
	<td class=noline>
	<input type=checkbox name=chk[<?=$i?>] <? if ($cfg_mobile_step[$i][chk]){ ?>checked<?}?>> 체크시 모바일샵 메인페이지에 출력이됩니다
	</td>
</tr>
<tr>
	<td>디스플레이유형</td>
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
		[갤러리형 설명]<br />
		가로화면 : 한 라인에 4개의 상품이 디스플레이 됩니다.<br />
		세로화면 : 한 라인에 6개의 상품이 디스플레이 됩니다.<br />
		(가로화면↔세로화면 전환시 상품 디스플레이 수가 자동변경 됩니다)<br />
	</font>
	</td>
</tr>
<tr>
	<td>메인출력 상품수</td>
	<td><input type=text name=page_num[] value="<?=$cfg_mobile_step[$i][page_num]?>" class="rline"> 개 <font class=extext>메인페이지에 보여지는 총 상품수입니다</td>
</tr>
<tr>
	<td>진열할 상품선정<div style="padding-top:3px"></div>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><font class=extext_l>[상품순서변경 방법]</font></a>
	<div style="padding-top:3px"></div>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a>
	</td>
	<td>
		<div style="padding-top:5px;z-index:-10"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step<?=$i?>[]', 'step<?=$i?>X');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 저장버튼을 누르셔야 최종 저장이 됩니다.</font></div>
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
<!--<tr><td><img src="../img/arrow_blue.gif" align=absmiddle>상품디스플레이 선정방법</td></tr>-->
<tr><td><img src="../img/icon_list.gif" align=absmiddle>상품선택하기 버튼을 눌러 진열될 상품을 선택해주세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>펼쳐진 창을 닫고 아래의 저장버튼을 누르셔야 최종 저장됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>진열할 수 있는 상품의 최대개수는 300개 입니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>모바일샵에 진열될 상품을 설정하지 않으면, PC용 메인상품진열에 설정된 상품리스트가 출력됩니다. </td></tr>
</table>

</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>