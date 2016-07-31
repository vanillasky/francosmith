<?
$location = "상품관리 > 아이룩 상품 이미지 등록";
include "../_header.php";

$returnUrl = ($_GET[returnUrl]) ? $_GET[returnUrl] : $_SERVER[HTTP_REFERER];
if (!$_GET[mode]) $_GET[mode] = "register";

if($_GET[idx] && $_GET[mode] == "modify") {
	$data = $db->fetch("select a.*, b.goodsnm, b.img_s from ".GD_EYELOOK." a inner join ".GD_GOODS." b on a.goodsno = b.goodsno where a.idx='".$_GET[idx]."'",1);
}
?>
<script>

function form_submit() {

	obj = eval("document.fm");

	if(chkForm(obj)) {
		obj.submit();
	}
	
}

</script>
<form name=fm method=post action="eyelook_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=idx value="<?=$_GET[idx]?>">
<input type=hidden name=chk[] value="<?=$_GET[idx]?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<!-- 상품정보 -->
<div class=title>아이룩 상품정보<span>아이룩 상품 정보를 등록합니다. <!--<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>--></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width=150px nowrap><?=($_GET[mode] == "register") ? "상품선택" : "상품명"?></td>
	<td>
		<table width="600px" cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td style="width:55px;">
				<? if($_GET[mode] == "register") { ?> 
					<img src="../img/btn_addpro.gif" align="absmiddle" onclick="popup('eyelook_popup.php',700,650)" style="cursor:pointer;">
					<input type=hidden name=goodsno id="goodsno" fld_esssential value="" msgR="상품선택 필수사항">
				<? } else { ?>
					<a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a>
				<? } ?>
			</td>
			<td style="width:545px;">
				<span id="goodsnm"><?=$data['goodsnm']?></span> 
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<? if($_GET[mode] == "register") { ?>  <div style="height:20;padding-top:10px" class=extext>[상품선택] 버튼을 클릭하여 상품을 선택하여 주시기 바랍니다.</font></div><? } ?>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td width=150px nowrap>아이룩 상품 이미지</td>
	<td>
		<input type=file name="img_eyelook[]" fld_esssential class=line style="width:400px;" msgR="아이룩 상품 이미지 필수사항">
		<?=goodsimg($data['img_eyelook'],40,"style='border:1 solid #cccccc' onclick=popupImg('../data/goods/".$data['img_eyelook']."','../') class=hand align=absmiddle",2)?>
		<div style="height:20;padding-top:10px" class=extext>아이룩 상품 이미지는 상품 외 배경이 투명 처리 된 PNG, GIF 파일만 등록 해 주세요.(권장사이즈 800*800)</font></div>
	</td>
</tr>
</table>
<div style="padding-top:20px"></div>
<div style="border-top:3px #efefef solid;"></div>

<div class=button>

<? if($_GET[idx]) { ?>
<img src="../img/btn_del.gif" onclick="document.fm.mode.value = 'delete'; document.fm.submit();" style="cursor:pointer;">
<input type=image src="../img/btn_modify.gif">
<? } else { ?>
<input type=image src="../img/btn_register.gif">
<? } ?>
<img src="../img/btn_list.gif" onclick="location.href='<?=$returnUrl?>';" style="cursor:pointer;">

</div>
</form>
</div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<? if($_GET[mode] == "register") { ?> 
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 선택 버튼을 눌러 아이룩 이미지를 등록할 상품을 선택하세요.</td></tr>
<? } else { ?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">등록된 아이룩 상품 이미지를 교체할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">삭제 버튼을 누르면 등록된 아이룩 상품 이미지가 삭제 됩니다.</td></tr>
<? } ?>
</table>
</div>
<script>cssRound('MSG01')</script>

<?
include "../_footer.php";

?>