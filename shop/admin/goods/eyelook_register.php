<?
$location = "��ǰ���� > ���̷� ��ǰ �̹��� ���";
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

<!-- ��ǰ���� -->
<div class=title>���̷� ��ǰ����<span>���̷� ��ǰ ������ ����մϴ�. <!--<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>--></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width=150px nowrap><?=($_GET[mode] == "register") ? "��ǰ����" : "��ǰ��"?></td>
	<td>
		<table width="600px" cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td style="width:55px;">
				<? if($_GET[mode] == "register") { ?> 
					<img src="../img/btn_addpro.gif" align="absmiddle" onclick="popup('eyelook_popup.php',700,650)" style="cursor:pointer;">
					<input type=hidden name=goodsno id="goodsno" fld_esssential value="" msgR="��ǰ���� �ʼ�����">
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
				<? if($_GET[mode] == "register") { ?>  <div style="height:20;padding-top:10px" class=extext>[��ǰ����] ��ư�� Ŭ���Ͽ� ��ǰ�� �����Ͽ� �ֽñ� �ٶ��ϴ�.</font></div><? } ?>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td width=150px nowrap>���̷� ��ǰ �̹���</td>
	<td>
		<input type=file name="img_eyelook[]" fld_esssential class=line style="width:400px;" msgR="���̷� ��ǰ �̹��� �ʼ�����">
		<?=goodsimg($data['img_eyelook'],40,"style='border:1 solid #cccccc' onclick=popupImg('../data/goods/".$data['img_eyelook']."','../') class=hand align=absmiddle",2)?>
		<div style="height:20;padding-top:10px" class=extext>���̷� ��ǰ �̹����� ��ǰ �� ����� ���� ó�� �� PNG, GIF ���ϸ� ��� �� �ּ���.(��������� 800*800)</font></div>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ ���� ��ư�� ���� ���̷� �̹����� ����� ��ǰ�� �����ϼ���.</td></tr>
<? } else { ?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ϵ� ���̷� ��ǰ �̹����� ��ü�� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ��ư�� ������ ��ϵ� ���̷� ��ǰ �̹����� ���� �˴ϴ�.</td></tr>
<? } ?>
</table>
</div>
<script>cssRound('MSG01')</script>

<?
include "../_footer.php";

?>