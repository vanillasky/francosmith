<?

$location = "������ũ ���½�Ÿ�� ���� > ��ǰ�ϰ�����";
$scriptLoad = '<script src="../interpark/js/common.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." where inpk_dispno!=''");

$selected[skey][$_GET[skey]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[register][$_GET[register]] = "checked";
$checked[isall][$_GET[isall]] = "checked";

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";
	$where[] = "inpk_dispno!=''";
	$where[] = "a.todaygoods = 'n'";	// �����̼� ��ǰ ����

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];
	}

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
	";

	// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
	$whereArr	= getCategoryLinkQuery('c.category', $category);

	if ($category){
		$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
		$where[]	= $whereArr['where'];
	}
	if ($_GET[register] =='Y') $where[] = "inpk_prdno!=''";
	else if ($_GET[register] =='N') $where[] = "inpk_prdno=''";
	if ($_GET[inpk_dispno]) $where[] = "inpk_dispno = '$_GET[inpk_dispno]'";
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

	$pg = new Page($_GET[page]);
	$pg->field = $whereArr['distinct']." a.goodsno,a.goodsnm,a.open,a.regdt,a.maker,a.inpk_dispno,a.inpk_prdno,a.totstock,b.*";
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();

	$res = $db->query($pg->query);
}

?>

<script><!--
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function chkFormList(fObj){
	if (chkForm(fObj) === false) return false;
	if (fObj['isall'][0].checked === false && isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return false;
	}

	var msg = '';
	msg += '�ϰ������� �����Ͻðڽ��ϱ�?';
	if (!confirm(msg)) return false;

	fObj.target = "_self";
	fObj.action = "transmit_action.php";
	return true;
}
--></script>

<div class="title title_top">��ǰ�ϰ�����<span>���� ��ǰ�� ���ϰ� ������ũ�� ���� �� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=23')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>�ʵ�! ��ǰ�ϰ������̶�?</b></font></div>
<div style="padding-top:2"><font  color=777777>������ũ�� ��������� ������ ������ ��� ��ǰ���� ������ũ�� �����ؾ� �մϴ�.</div>
<div style="padding-top:2">�������� �� ������ ����� ��ǰ����  ���ٸ� �ϳ��ϳ� ������ũ�� �����ϴµ� �ð��� ���� �ɸ��� �˴ϴ�.</div>
<div style="padding-top:2"><font color=0074BA>�Ʒ� ����� ������ũ ���½�Ÿ�Ͽ� �������� ��ϵ� ��ǰ�� ������ũ�� �Ѳ����� �ϰ� �����ϴ� ����Դϴ�.</font></div>
<div style="padding-top:2">����, ��ǰ����Ʈ���� �� ��ǰ�� ���ε��� �����ص� ��������ϴ�. ������ �ϰ������� �Ϸ��� �Ʒ� ����� ����ϼ���.</div>


<div style="padding-top:5"><font color="#EA0095"><b>��ǰ�ϰ����� ����</b></font></div>
<div style="padding-top:2">�� ��ǰ���� �̹����� �̹���ȣ������ �̿��Ͽ� ����Ǿ� �ִ��� üũ�ϼ���.</div>
<div style="padding-top:2">�� ������ũ ī�װ��� ���� <a href="./link.php">[�з��ϰ���Ī]</a> �޴����� �ϰ� ��Ī�ϼ���</div>
<div style="padding-top:2">�� ��Ī�� ī�װ��� ��ǰ�� �ϰ� �����Ͻø� ������ũ�� ��ǰ�� ��ϵ˴ϴ�.</div>
</div>


<!-- ��ǰ������� : start -->
<form name=frmList onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���� �Ʒ����� ������ũ�� ������ ��ǰ�� �˻��մϴ�.</b></font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�����з�����</td>
	<td>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
	</td>
</tr>
<tr>
	<td>������ũ�з�����</td>
	<td>
	<input class="lline" style="letter-spacing:-1px; width:450px;" readonly id="inpk_dispnm">
	<input type=hidden name=inpk_dispno value="<?=$_GET[inpk_dispno]?>">
	<a href="javascript:;" onclick="popupLayer('../interpark/popup.category.php?spot=inpk_dispno',650,500);"><img src= "../img/btn_interpark_catesearch.gif" align=absmiddle></a>
<? if ($_GET[inpk_dispno]){ ?>
	<script>getDispNm('<?=$_GET[inpk_dispno]?>','inpk_dispnm');</script>
<? } ?>
	</td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
	<option value="a.goodsno" <?=$selected[skey]['a.goodsno']?>>������ȣ
	<option value="goodscd" <?=$selected[skey][goodscd]?>>��ǰ�ڵ�
	<option value="keyword" <?=$selected[skey][keyword]?>>����˻���
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>">
	</td>
</tr>
<tr>
	<td>��ǰ��¿���</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>��ü
	<input type=radio name=open value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type=radio name=open value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
</tr>
<tr>
	<td>������ũ��Ͽ���</td>
	<td class=noline>
	<input type=radio name=register value="" <?=$checked[register]['']?>>��ü
	<input type=radio name=register value="Y" <?=$checked[register][Y]?>>��ϻ�ǰ
	<input type=radio name=register value="N" <?=$checked[register][N]?>>�̵�ϻ�ǰ
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

</form>
<!-- ��ǰ������� : end -->

<form name="fmList" method="post" onsubmit="return chkFormList(this)">
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" required msgR="�ϰ����� �� ��ǰ�� ���� �˻��ϼ���.">

<div class="pageInfo ver8">�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>����</a></th>
	<th><font class=small1><b>��ȣ</th>
	<th><font class=small1><b>��ǰ��</th>
	<th><font class=small1><b>������</th>
	<th><font class=small1><b>�����</th>
	<th><font class=small1><b>�ǸŰ�</th>
	<th><font class=small1><b>������</th>
	<th><font class=small1><b>���</th>
	<th><font class=small1><b>����</th>
	<th><font class=small1><b>���</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col><col width=150><col width=60><col width=80 span=2><col width=55><col width=40 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$stock = $data['totstock'];
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<? if ($data[link]){ ?>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<? } else { ?><td><!--<?=$pg->idx--?>--></td><td></td>
	<? } ?>
	<td align=center><?=$data[maker]?></td>
	<td align=center><font class="ver81" color="#444444"><?=substr($data[regdt],0,10)?></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><b><?=number_format($data[price])?></b></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[reserve])?></font></td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
	<td align=center><img src="../img/icn_<?=($data[inpk_prdno] ? '1' : '0')?>.gif"></td>
</tr>
<tr>
	<td colspan=2></td>
	<td colspan=10>
	<? if ($data[inpk_dispno]){ ?>
	������ũ �з� : <span id="dispnm<?=$pg->idx?>" style="letter-spacing:-1px;"></span>
	<script>getDispNm('<?=$data[inpk_dispno]?>','dispnm<?=$pg->idx?>');</script>
	<? } ?>
	</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<!-- ���� : start -->
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> �� ��ǰ����Ʈ�� �ִ� ��ǰ�� ������ũ�� �����մϴ�.</b></font></div>
<div class="noline" style="padding:0 0 5 5">
	<div style="float:left;">
	<input type="radio" name="isall" value="Y" <?=$checked[isall]['Y']?>>�˻��� ��ǰ ��ü<?=($pg->recode[total]?"({$pg->recode[total]}��)":"")?>�� �����մϴ�.<br>
	<input type="radio" name="isall" value="" <?=$checked[isall]['']?>>������ ��ǰ�� �����մϴ�.
	</div>
	<div style="padding-left:210px;"><input type=image src="../img/btn_interpark_transmit.gif" align=top></div>
</div>
<!-- ���� : end -->

</form>

<div style="padding-top:30"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ũ ���½�Ÿ�Ϸ� ������ ������ ��� ��ǰ�� ������ũ�� �����ؾ� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������� ����� ��ǰ�� ���� ��� �ϳ��� �����Ϸ��� �ð��� ���� �ҿ�˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ϳ��� ����ϴ� ���ŷο���� �ϰ������� �� �ֵ��� �� ����� �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
