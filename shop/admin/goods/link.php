<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_link.php?'.$_SERVER['QUERY_STRING']);
exit;
$location = "��ǰ���� > ���� �̵�/����/����";
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_GOODS."");

$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$selected[sbrandno][$_GET[sbrandno]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[isToday][$_GET[isToday]] = "checked";

if ($_GET[sCate]){
	$sCategory = array_notnull($_GET[sCate]);
	$sCategory = $sCategory[count($sCategory)-1];
}

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];
	}

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link
	";

	if ($category || $_GET[unlink] == 'Y'){
		$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
		$where[] = ($_GET[unlink] == 'Y') ? "ISNULL(c.goodsno)" : "category like '$category'";
	}
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET['brandno']) $where[] = "brandno='{$_GET['brandno']}'";
	if ($_GET['unbrand'] == 'Y') $where[] = "brandno='0'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

	$pg = new Page($_GET[page]);
	$pg->field = "
		distinct a.goodsno,a.goodsnm,a.open,a.regdt,a.brandno,a.inpk_prdno,a.totstock,a.img_s,
		b.link, b.reserve, b.price
	";
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();

	$res = $db->query($pg->query);
}

// �귣��
$brands = array();
$bRes = $db->query("select * from gd_goods_brand order by sort");
while ($tmp=$db->fetch($bRes)) $brands[$tmp['sno']] = $tmp['brandnm'];

?>

<script><!--
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function chkFormList(mode){
	var fObj = document.forms['fmList'];
	if (inArray(mode, new Array('move','copyGoodses','unlink')) && fObj.category.value == ''){
		if (mode == 'move') alert("�з��̵��� �з��� �˻����� ��츸 �����մϴ�.");
		else if (mode == 'copyGoodses') alert("��ǰ����� �з��� �˻����� ��츸 �����մϴ�.");
		else if (mode == 'unlink') alert("���������� �з��� �˻����� ��츸 �����մϴ�.");
		document.getElementsByName("cate[]")[0].focus();
		return;
	}
	if (isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return;
	}
	if (mode == 'delGoodses'){
		tobj = document.getElementsByName('chk[]');
		for(i=0; i< tobj.length; i++){
			if (tobj[i].checked === true && tobj[i].getAttribute('notDel') == 'notInpk'){
				alert("������ũ�� ��ϵ� ��ǰ�� ������ �� �����ϴ�.");
				tobj[i].focus();
				return;
			}
		}
	}
	if (inArray(mode, new Array('link','move','copyGoodses')) && document.getElementsByName("sCate[]")[0].value == ''){
		if (mode == 'link') alert("������ ��ǰ�� ���� �� �з��� �������ּ���.");
		else if (mode == 'move') alert("������ ��ǰ�� �̵� �� �з��� �������ּ���.");
		else if (mode == 'copyGoodses') alert("������ ��ǰ�� ���� �� �з��� �������ּ���.");
		document.getElementsByName("sCate[]")[0].focus();
		return;
	}
	else if (mode == 'linkBrand' && document.getElementsByName("sbrandno")[0].value == ''){
		alert("������ ��ǰ�� ���� �� �귣�带 �������ּ���.");
		document.getElementsByName("sbrandno")[0].focus();
		return;
	}

	var msg = '';
	if (mode == 'link') msg += '������ ��ǰ�� �ش� �з��� �����Ͻðڽ��ϱ�?';
	else if (mode == 'move') msg += '������ ��ǰ�� �ش� �з��� �̵��Ͻðڽ��ϱ�?';
	else if (mode == 'copyGoodses') msg += '������ ��ǰ�� �ش� �з��� �����Ͻðڽ��ϱ�?';
	else if (mode == 'unlink') msg += '������ ��ǰ�� �з��� �����Ͻðڽ��ϱ�?';
	else if (mode == 'delGoodses') msg += '������ ��ǰ�� ���� �����Ͻðڽ��ϱ�?' + "\n\n" + '[����] ���� �Ŀ��� ������ �ȵǹǷ� �����ϰ� �����Ͻñ� �ٶ��ϴ�.';
	else if (mode == 'linkBrand') msg += '������ ��ǰ�� �ش� �귣�带 �����Ͻðڽ��ϱ�?';
	else if (mode == 'unlinkBrand') msg += '������ ��ǰ�� �귣�带 �����Ͻðڽ��ϱ�?';
	if (!confirm(msg)) return;

	fObj.target = "_self";
	fObj.mode.value = mode;
	fObj.action = "indb.php";
	fObj.submit();
}
--></script>

<div class="title title_top">���� �̵�/����/����<span>����Ͻ� ��ǰ�� ���ϰ� �̵�/����/���� �� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<!-- ��ǰ������� : start -->
<form name=frmList onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
	&nbsp;&nbsp;&nbsp;<a href="?indicate=search&unlink=Y"><img src="../img/btn_without_cate.gif" alt="�̿����ǰ����" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>�귣��</td>
	<td>
	<select name="brandno">
	<option value="">-- �귣�� ���� --
	<? foreach($brands as $sno => $brandnm){ ?>
	<option value="<?=$sno?>" <?=$selected['brandno'][$sno]?>><?=$brandnm?></option>
	<? } ?>
	</select>
	&nbsp;&nbsp;&nbsp;<a href="?indicate=search&unbrand=Y"><img src="../img/btn_without_brand.gif" alt="�̿����ǰ����" align=absmiddle></a>
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
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
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

<form name="fmList" method="post" onsubmit="return false">
<input type=hidden name=mode>
<input type=hidden name=category value="<?=$category?>">

<div class="pageInfo ver8" style="margin-top:20px;">�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>����</a></th>
	<th><font class=small1><b>��ȣ</th>
	<th colspan="2"><font class=small1><b>��ǰ��</th>
	<th><font class=small1><b>�귣��</th>
	<th><font class=small1><b>�����</th>
	<th><font class=small1><b>�ǸŰ�</th>
	<th><font class=small1><b>������</th>
	<th><font class=small1><b>���</th>
	<th><font class=small1><b>����</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col span=2><col width=150><col width=60><col width=80 span=2><col width=55 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$stock = $data['totstock'];
	$notDel = ($data['inpk_prdno'] && $inpkOSCfg['use'] == 'Y' ? 'notInpk' : '');
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)" notDel="<?=$notDel?>"></td>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td>
		<a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a>
	</td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<td align=center><?=$brands[$data['brandno']]?></td>
	<td align=center><font class="ver81" color="#444444"><?=substr($data[regdt],0,10)?></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><b><?=number_format($data[price])?></b></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[reserve])?></font></td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<table class=tb style="margin:30px 0;">
<col class=cellC><col class=cellL>
<tr>
	<td>����/�̵�/����</td>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ�� <script>new categoryBox('sCate[]',4,'<?=$sCategory?>','','fmList');</script> ����
	<a href="javascript:chkFormList('link')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="����"></a>
	<a href="javascript:chkFormList('move')"><img src="../img/btn_cate_move.gif" align="absmiddle" alt="�̵�"></a>
	<a href="javascript:chkFormList('copyGoodses')"><img src="../img/btn_cate_copy.gif" align="absmiddle" alt="����"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<input type="checkbox" name="isToday" value="Y" <?=$checked[isToday]['Y']?>>�ش� ��ǰ�� ������� ���� ��Ͻð����� �����մϴ�. <font class=extext>(������ ��쿡�� ������ ����ð����� ����˴ϴ�)
	</div>
	</td>
</tr>
<tr height=35>
	<td>�з�����</td>
	<td>������ ��ǰ�� �з�(ī�װ�)�� <a href="javascript:chkFormList('unlink')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="����"></a> <font class=extext>(�����ϰ� �����ϼ���. ��ưŬ���� ��ǰ�� ����� �з�(ī�װ�)�� �����˴ϴ�)</td>
</tr>
<tr>
	<td>�귣�忬��</td>
	<td>
	<div style="margin:5px 0">
	������ ��ǰ��
	<select name="sbrandno">
	<option value="">-- �귣�� ���� --
	<? foreach($brands as $sno => $brandnm){ ?>
	<option value="<?=$sno?>" <?=$selected['sbrandno'][$sno]?>><?=$brandnm?></option>
	<? } ?>
	</select> ����
	<a href="javascript:chkFormList('linkBrand')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="����"></a>
	<a href="javascript:chkFormList('unlinkBrand')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="����"></a>
	</div>
	</td>
</tr>
<tr height=35>
	<td>��ǰ����</td>
	<td>������ ��ǰ�� <a href="javascript:chkFormList('delGoodses')"><img src="../img/btn_cate_del.gif" align="absmiddle" alt="����"></a> <font class=extext>(�����ϰ� �����ϼ���. ��ưŬ���� ������ ��ǰ���� �����˴ϴ�. �����Ǹ� �������� �ʽ��ϴ�)</td>
</tr>
</table>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�з����� : ��ǰ�� �з�(ī�װ�)�� �����ϴ� ����Դϴ�.(���ߺз��������)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�з��̵� : ���� ����� �з����� �ٸ� �з��� �̵��ϴ� ����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�з����� : ���� ����� �з��� �����ϴ� ����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ���� : �ٸ� �з��� �Ȱ��� ��ǰ�� �ϳ� �� ����(����)�ϴ� ����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ���� : ��ǰ�� �����ϴ� ������� ���� �Ŀ��� ������ �ȵǹǷ� �����ϰ� �����Ͻñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[����] �� ��ǰ�˻��� ��ǰ�� ����� �����з����� ��Ȯ�ϰ� ������ �� �˻��ϼ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[����] ��ǰ���� ��� ��ǰ����/��ǰ�ı�� ������� �ʽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
