<?

$location = "������ũ ���½�Ÿ�� ���� > �з��ϰ���Ī";
$scriptLoad = '<script src="../interpark/js/common.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_GOODS);

if ($_GET[isall] == '') $_GET[isall]='N';
$selected[skey][$_GET[skey]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[isall][$_GET[isall]] = "checked";

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";

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
	if ($_GET[unlink] == 'Y') $where[] = "inpk_dispno=''";
	if ($_GET[inpk_dispno]) $where[] = "inpk_dispno = '$_GET[inpk_dispno]'";
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

	$pg = new Page($_GET[page]);
	$pg->field = $whereArr['distinct']." a.goodsno,a.goodsnm,a.open,a.regdt,a.maker,a.inpk_prdno,a.inpk_dispno,a.totstock,b.*";
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

function chkFormList(mode){
	var fObj = document.forms['fmList'];
	if (chkForm(fObj) === false) return;
	if (fObj['isall'][0].checked === false && isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return;
	}
	if (mode == 'link' && document.getElementsByName("sinpk_dispno")[0].value == ''){
		alert("������ ��ǰ�� ���� �� �з��� �������ּ���.");
		_ID("sinpk_dispnm").focus();
		return;
	}

	var msg = '';
	if (mode == 'link') msg += '������ ��ǰ�� �ش� �з��� �����Ͻðڽ��ϱ�?';
	else if (mode == 'unlink') msg += '������ ��ǰ�� �з��� �����Ͻðڽ��ϱ�?';
	if (!confirm(msg)) return;

	fObj.target = "_self";
	fObj.mode.value = mode;
	fObj.action = "indb.php";
	fObj.submit();
}
--></script>

<div class="title title_top">�з��ϰ���Ī<span>����Ͻ� ��ǰ�� ���ϰ� ������ũ �з�(ī�װ�)�� ���� �� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>�ʵ�! �з��ϰ���Ī�̶�?</b></font></div>
<div style="padding-top:2"><font  color=777777>������ũ�� ��������� ���� ���� ������ ��� ��ǰ���� �з��� ������ũ�з��� ��Ī���Ѿ߸� �մϴ�.</div>
<div style="padding-top:2">�������� �� ������ ����� ��ǰ����  ���ٸ� �ϳ��ϳ� ������ũ�� �з������ϴµ� �ð��� ���� �ɸ��� �˴ϴ�.</div>
<div style="padding-top:2"><font color=0074BA>�Ʒ� ����� ������ũ ���½�Ÿ�Ͽ� �����ϱ� ���� ��ϵ� ��ǰ�� ������ũ�з��� �Ѳ����� �ϰ� �����ϴ� ����Դϴ�.</font></div>
<div style="padding-top:2">����, ��ǰ����Ʈ���� �� ��ǰ�� ���ε��� �з������� �ص� ��������ϴ�. ������ �з������� �Ϸ��� �Ʒ� ����� ����ϼ���.</div>
</div>



<!-- ��ǰ������� : start -->
<form name=frmList onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���� �Ʒ����� ������ũ �з��� ������ ��ǰ�� �˻��մϴ�.</b></font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�����з�����</td>
	<td>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
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
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

</form>
<!-- ��ǰ������� : end -->


<div style="padding: 3 0 10 12"><font color=EA0095><b>��</b></font> <font class=small1 color=EA0095>�����Ұ�ǥ�ð� �ִ� ��ǰ�� �̹� ������ ��ǰ�̹Ƿ� ������ũ �з�(ī�װ�) ������ �Ұ����մϴ�.</font></div>


<form name="fmList" method="post" onsubmit="return false">
<input type=hidden name=mode>
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" required msgR="�ϰ����� �� ��ǰ�� ���� �˻��ϼ���.">

<div class="pageInfo ver8">�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>����</a></th>
	<th><font class=small1><b>��ȣ</th>
	<th><font class=small1><b>��ǰ��</th>
	<!--<th><font class=small1><b>������</th>-->
	<th><font class=small1><b>�����</th>
	<th><font class=small1><b>�ǸŰ�</th>
	<th><font class=small1><b>������</th>
	<th><font class=small1><b>���</th>
	<th><font class=small1><b>����</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col><col width=150><col width=60><col width=80 span=2><col width=55 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$stock = $data[totstock];
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<? if ($data[inpk_prdno]){ ?>
	<td align=center class="noline" valign=middle><font class=small1 color=red>����<div>�Ұ�</div></font></td>
	<? } else { ?>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<? } ?>
	<? if ($data[link]){ ?>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<? } else { ?><td><!--<?=$pg->idx--?>--></td><td></td>
	<? } ?>
	<!--<td align=center><font class=small1 color=666666><?=$data[maker]?></font></td>-->
	<td align=center><font class="ver81" color="#444444"><?=substr($data[regdt],0,10)?></td>
	<td align=center style="padding-right:10px" nowrap><font class="ver81" color="#444444"><b><?=number_format($data[price])?></b></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver81" color="#444444"><?=number_format($data[reserve])?></font></td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr>
	<td colspan=2></td>
	<td colspan=10>
	<? if ($data[inpk_dispno]){ ?>
	<font class=small1 color="#EA0095">������ũ �з� : <span id="dispnm<?=$pg->idx?>" style="letter-spacing:-1px;"></span></font>
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
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> �� ��ǰ����Ʈ�� �ִ� ��ǰ��, �Ʒ� ������ũ �з��� �����մϴ�.</b></font></div>
<div class="noline" style="padding:0 0 5 5">
	<input type="radio" name="isall" value="Y" <?=$checked[isall]['Y']?>>�˻��� ��ǰ ��ü<?=($pg->recode[total]?"({$pg->recode[total]}��)":"")?>�� ����(�Ǵ� ����)�մϴ�. <span class=small1>(��, ������ũ�� ���۵� ��ǰ�� ������ �Ұ����մϴ�.)</span><br>
	<input type="radio" name="isall" value="N" <?=$checked[isall]['N']?>>������ ��ǰ�� ����(�Ǵ� ����)�մϴ�.
</div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>������ũ �з�����</td>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ��
	<input class="lline" style="letter-spacing:-1px; width:450px; text-align:center" readonly id="sinpk_dispnm">
	<input type=hidden name=sinpk_dispno value="<?=$_GET[sinpk_dispno]?>">
	<a href="javascript:popupLayer('../interpark/popup.category.php?spot=sinpk_dispno',650,500);"><img src= "../img/btn_interpark_catesearch.gif" align=absmiddle></a>
	����
	<a href="javascript:chkFormList('link')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="����"></a>
<? if ($_GET[sinpk_dispno]){ ?>
	<script>getDispNm('<?=$_GET[sinpk_dispno]?>','sinpk_dispnm');</script>
<? } ?>
	</div>
	</td>
</tr>
<tr height=35>
	<td>������ũ �з�����</td>
	<td>������ ��ǰ�� ������ũ �з�(ī�װ�)�� <a href="javascript:chkFormList('unlink')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="����"></a> <font class=small1 color=555555>(�����ϰ� �����ϼ���. ��ưŬ���� ��ǰ�� ����� ������ũ �з��� �����˴ϴ�)</td>
</tr>
</table>
<!-- ���� : end -->

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ũ �з����� : ��ǰ�� ������ũ �з�(ī�װ�)�� �����ϴ� ����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ũ �з����� : ���� ����� ������ũ �з��� �����ϴ� ����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>�����Ұ�ǥ�ð� �ִ� ��ǰ�� �̹� ������ ��ǰ�̹Ƿ� ������ũ �з�(ī�װ�) ������ �Ұ����մϴ�.</b></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
