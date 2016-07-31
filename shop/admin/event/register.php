<?

$location = "이벤트관리 > 이벤트만들기";
include "../_header.php";
@include_once "../../conf/qr.cfg.php";
@require_once("../../lib/qrcode.class.php");

$data[tpl] = "tpl_01";
if (!$_GET[mode]) $_GET[mode] = "addEvent";

if ($_GET[mode]=="modEvent"){
	$data = $db->fetch("select * from ".GD_EVENT." where sno='$_GET[sno]'");
	if($data[category]) list($data[catnm]) = $db->fetch("select catnm from gd_category where category='$data[category]'");
	$s_category = $s_brand = array();
	if($data['r_category']) $s_category = explode('|',$data['r_category']);
	if($data['r_brand']) $s_brand = explode('|',$data['r_brand']);
}

$checked[tpl][$data[tpl]] = "checked";

### QR 사용 정보 가져오기
if($qrCfg['useEvent'] == "y"){
$qrdata = $db->fetch("select count(*) from ".GD_QRCODE." where qr_type='event' and contsNo=$_GET[sno]");
if($qrdata[0]>0){ $data['qrcode'] = "y" ;}else{ $data['qrcode'] = "n";}
$checked['qrcode'][$data['qrcode']] = "checked";  // qrcode 설정
}
?>

<script>
function copyTxt(val){
	var obj = document.getElementsByName('clipb')[0];
	obj.value = val;
	var clip=obj.createTextRange();
	clip.execCommand('copy');
	alert('클립보드로 복사되었습니다.');
}
</script>

<div class="title title_top">이벤트만들기<span>이벤트페이지를 직접 디자인하고 이벤트상품들을 선정하실 수 있습니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>&nbsp; <a href="../design/design_banner.php"><font class=extext_l>[이벤트배너등록하기]</font></a></div>

<form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name=clipb value="">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>이벤트제목</td>
	<td><input type=text name=subject style="width:600px" value="<?=$data[subject]?>" required  class=line></td>
</tr>
<tr>
	<td>이벤트기간</td>
	<td>
	<input type=text name=sdate value="<?=$data[sdate]?>" onclick="calendar(event)"  class=line> -
	<input type=text name=edate value="<?=$data[edate]?>" onclick="calendar(event)"  class=line>
	<font class="extext">기간을 입력하면 종료일 자정까지 효력이 발휘됩니다</font>
	</td>
</tr>
<tr>
	<td>이벤트내용<br>디자인 & HTML입력</td>
	<td>
	<textarea name=body style="width:100%;height:350px" type=editor><?=$data[body]?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/");</script>
	</td>
</tr>
<tr>
	<td>이벤트상품 선정</td>
	<td style="padding:5px">

	<?
	$query = "
	select
		a.mode,a.goodsno,b.goodsnm,b.img_s,c.price,b.brandno
	from
		".GD_GOODS_DISPLAY." a,
		".GD_GOODS." b,
		".GD_GOODS_OPTION." c
	where
		a.goodsno=b.goodsno
		and a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
		and a.mode = 'e{$data[sno]}'
	";
	$res = $db->query($query);
	?>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr>
			<td>
				<div id=divRefer style="position:relative;z-index:99">
					<div style="padding:5px 0px 0px 0px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_refer[]', 'referX');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 등록(수정)버튼을 누르셔야 최종 저장이 됩니다.</font></div>
					<div id="referX" style="padding-top:3px;">
						<?php
						while ($v = $db->fetch($res)){
							$r_brandno[] = $v['brandno'];
						?>
							<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
							<input type=hidden name="e_refer[]" value="<?php echo $v['goodsno']; ?>" />
						<?php } ?>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td><div style="padding-top:5;padding-left:3;"><a href="../goods/price.php?indicate=event&sevent=<?=$data[sno]?>" target=blank><img src="../img/btn_quick_price.gif"></a>&nbsp;&nbsp;<a href="../goods/reserve.php?indicate=event&sevent=<?=$data[sno]?>" target=blank><img src="../img/btn_quick_reserve.gif"></a></div></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>분류(카테고리) 만들기</td>
	<td><div style="padding-top:5px"></div>
	<input type="text" name="catnm" value="<?=$data['catnm']?>" label="현재분류명" maxlen="30" class="line" /> <font class="extext">위에서 선정한 이벤트 상품들을 위한 분류(카테고리)를 만듭니다. 안만드려면 공란으로 두세요.</font>
	<div style="font:0;height:9"></div>
	<div style="padding-left:5px"><font class="extext">* 일반 카테고리와 똑같은 기능의 1차 카테고리가 자동으로 만들어지고, 분류감춤모드로 설정됩니다.</font></div>
	<div style="padding:3px 0px 0px 5px"><font class="extext">* 분류를 만든 후, 분류보임으로 수정 또는 분류를 삭제하려면 <a href="../goods/category.php" target="_blank"><font class="extext_l">[카테고리 관리]</font></a> 에서 관리하세요.</font></div>
	<div style="padding-top:3px"></div>
	</td>
</tr>
<input type="hidden" name="category" value="<?=$data['category']?>">
<tr>
	<td>상품 디스플레이 유형</td>
	<td>

	<table>
	<col align=center span=3>
	<tr>
		<td><img src="../img/goodalign_style_01.gif"></td>
		<td><img src="../img/goodalign_style_02.gif"></td>
		<td><img src="../img/goodalign_style_03.gif"></td>
	</tr>
	<tr class=noline>
		<td><input type=radio name=tpl value="tpl_01" <?=$checked[tpl][tpl_01]?>></td>
		<td><input type=radio name=tpl value="tpl_02" <?=$checked[tpl][tpl_02]?>></td>
		<td><input type=radio name=tpl value="tpl_03" <?=$checked[tpl][tpl_03]?>></td>
	</tr>
	</table>

	</td>
</tr>
<tr>
	<td>현재 선정된 상품의<br />카테고리 노출하기</td>
	<td>
	<div style="padding:4px 0px 5px 5px"><font class="extext">* 아래 분류는 위에서 선정한 상품들의 카테고리입니다. 이벤트페이지에 카테고리를 보여주려면 체크하세요.</font></div>
	<table class="tb">
	<tr class="cellC">
		<td align="center"><font class=small1><b>선택<b></font></td>
		<td align="center" colspan="3"><font class=small1><b>분류명 <font class=extext>(이미지작업시 주소복사를 활용하세요)</font></td>
	</tr>
	<?
	$query = "
	select
		distinct c.category
	from
		".GD_GOODS_DISPLAY." a
		left join ".GD_GOODS_LINK." b on a.goodsno=b.goodsno
		left join ".GD_CATEGORY." c on b.category = c.category

	where
		a.mode = 'e{$data[sno]}' and b.category != ''
	order by c.category
	";
	$res = $db->query($query);
	while($tmp = $db->fetch($res)){
		$r_cate[]=$tmp[category];
	}
	if($r_cate)foreach($r_cate as $v){
		$clen = strlen($v);
		for($i=3;$i<=$clen;$i+=3) $arrCategory[] = substr($v,0,$i);
	}
	$tmp = '';
	if($arrCategory)$arrCategory = array_unique($arrCategory);
	if($arrCategory){foreach($arrCategory as $tmp){
		unset($catnm);
		$len = strlen($tmp);
		for($i=3;$i<=$len;$i+=3){
			$ctmp = substr($tmp,0,$i);
			list($catnm[]) = $db->fetch("select catnm from ".GD_CATEGORY." where category='$ctmp' and hidden=0");
		}

		$len = strlen($tmp) / 3;
		$lcatnm = @implode(' > ',$catnm);
		if($lcatnm){
	?>
	<tr>
		<td width="30" align="center"><input type="checkbox" name="chkcate[]" value="<?=$tmp?>" class="null"<?if(in_array($tmp,$s_category))echo" checked";?> /></td>
		<td align="center" width="30"><?=$len?>차</td>
		<td><?=$lcatnm?></td>
		<td width="50" align="center"><a href="javascript:copyTxt('<?=$cfg[rootDir]?>/goods/goods_event.php?category=<?=$tmp?>&sno=<?=$_GET[sno]?>')"><img src="../img/btn_catelink_copy.gif" align="absmiddle"></a></td>
	</tr>
	<?}}}else{?>
	<tr>
		<td align="center" colspan=4>※ 이벤트를 등록하시면 이벤트 상품으로 선정된 상품들이 속하는 카테고리(코드값) 리스트가 안내 되어집니다.</td>
	</tr>
	<?}?>
	</table>
	</td>
</tr>
<tr>
	<td>현재 선정된 상품의<br>브랜드 노출하기</td>
	<td>
	<div style="padding:4px 0px 5px 5px"><font class="extext">* 아래 브랜드는 위에서 선정한 상품들의 브랜드입니다. 이벤트페이지에 브랜드를 보여주려면 체크하세요.</font></div>
	<table class="tb">
	<tr class="cellC">
		<td align="center"><font class=small1><b>선택<b></font></td>
		<td align="center" colspan="2"><font class=small1><b>브랜드명<b></font> <font class=extext>(이미지작업시 주소복사를 활용하세요)</font></td>
	</tr>
	<?

	if($r_brandno){
		$r_brandno = array_unique($r_brandno);
		$query = "select sno,brandnm from ".GD_GOODS_BRAND." where sno in (".implode(',',$r_brandno).") and sno != '0' order by brandnm";
		$res = $db->query($query);
		while($tmp = $db->fetch($res)){
	?>
	<tr>
		<td width="30" align="center"><input type="checkbox" name="chkbrand[]" value="<?=$tmp[sno]?>" class="null"<?if(in_array($tmp[sno],$s_brand))echo" checked";?> /></td>
		<td><?=$tmp['brandnm']?></td>
		<td width="50" align="center"><a href="javascript:copyTxt('<?=$cfg[rootDir]?>/goods/goods_event.php??brandno=<?=$tmp[sno]?>&sno=<?=$_GET[sno]?>')"><font class="extext"><img src="../img/btn_catelink_copy.gif" align=absmiddle></a></td>
	</tr>
	<?
	}
	}else{
	?>
	<tr>
		<td align="center" colspan=4>※ 이벤트를 등록하시면 브랜드 상품으로 선정된 상품들이 속하는 브랜드(코드값) 리스트가 안내 되어집니다.</td>
	</tr>
	<?}?>
	</table>
	</td>
</tr>
<!--
<tr>
	<td>이미지사이즈</td>
	<td><input type=text name=size value="<?=$data[size]?>"> <font class=ver8>pixel</font></td>
</tr>
-->
<tr>
	<td>상품출력수 조정</td>
	<td>
	<input type=text name=page_num value="<?=$data[page_num]?>" size=25  class=line>
	<span class=gray style="padding-left:5px"><font class="extext">예) 20, 40, 60, 80 (첫번째숫자는 기본출력수, 다음숫자부터는 출력수량조정)</font> <a href="javascript:popup('http://guide.godo.co.kr/guide/php/manual_event.php#qa',870,800)"><img src="../img/icon_sample.gif" border=0 hspace=2 align=absmiddle></a></td>
</tr>
<tr>
	<td>라인당 상품수</td>
	<td><input type=text name=cols value="<?=$data[cols]?>" class=line> 개</td>
</tr>
<? if($qrCfg['useEvent'] == "y"){ ?>
<tr>
	<td>QR Code 노출</td>
	<td><input type=radio name=qrcode value=y onfocus=blur()  <?=$checked['qrcode']['y']?> class=null>사용
<input type=radio name=qrcode value=n onfocus=blur()  <?=$checked['qrcode']['n']?> class=null>사용안함
<?
		if($data['qrcode'] == 'y'){
			$QRCode = new QRCode;
			echo  $QRCode->get_GoodsViewTag($goodsno, "event_down");
		}
?>
</td>
</tr>
<?}?>
</table>

<div class=button>
<input type=image src="../img/btn_modify.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>


<div style="padding-top:10px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이벤트관련 배너를 만드시려면 <a href="../design/design_banner.php"><font color=white><u>[배너관리]</u></font></a> 로 가서 배너를 등록하시면 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


</form>


<? include "../_footer.php"; ?>