<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_mileage.php?'.$_SERVER['QUERY_STRING']);
exit;
$location = "��ǰ���� > ���� �����ݼ���";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/design_main.$cfg[tplSkin].php";

### ���� ����
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." a	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno");

$selected[skey][$_GET[skey]] = "selected";
$selected[smain][$_GET[smain]] = "selected";
$selected[sevent][$_GET[sevent]] = "selected";
$selected[percent][$_GET[percent]] = "selected";
$selected[roundunit][$_GET[roundunit]] = "selected";
$selected[roundtype][$_GET[roundtype]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[indicate][$_GET[indicate]] = "checked";
$checked[method][$_GET[method]] = "checked";
$checked[isall][$_GET[isall]] = "checked";

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];
	}

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno
	";

	if ($category){
		$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
		$where[] = "category like '$category%'";
	}
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);
}
else if ($_GET[indicate] == 'main'){
	$orderby = "c.sort, b.sno";

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno
	left join ".GD_GOODS_DISPLAY." c on b.goodsno=c.goodsno
	";

	if ($_GET[smain] != '') $where[] = "c.mode = '{$_GET[smain]}'";
}
else if ($_GET[indicate] == 'event'){
	$orderby = "c.sort, b.sno";

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno
	left join ".GD_GOODS_DISPLAY." c on b.goodsno=c.goodsno
	";

	if ($_GET[sevent] != '') $where[] = "c.mode = 'e{$_GET[sevent]}'";
}

if (in_array($_GET[indicate], array('search', 'main', 'event')) === true){
	$pg = new Page($_GET[page]);
	$pg->field = "a.goodsno,a.goodsnm,a.open,a.img_s,a.use_emoney,b.*";
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
	if (fObj['method'][0].checked === true){
		if (chkText(fObj['reserve'],fObj['reserve'].value,'') === false) return false;
		if (chkPatten(fObj['reserve'],'regNum') === false) return false;
	}
	if (fObj['isall'].checked === false && isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return false;
	}

	var msg = '';
	if (fObj['method'][0].checked === true){
		msg += '�������� �ϰ� ' + fObj['reserve'].value + '������ �����Ͻðڽ��ϱ�?';
	}
	else {
		msg += '�������� �ǸŰ��� ' + fObj['percent'].value;
		msg += '%�� ' + fObj['roundunit'].value;
		msg += '�� ������ ' + fObj['roundtype'].options[fObj['roundtype'].selectedIndex].text;
		msg += '�Ͽ� �ϰ������� �����Ͻðڽ��ϱ�?';
	}
	msg += "\n\n" + '[����] �ϰ����� �Ŀ��� �������·� ������ �ȵǹǷ� �����ϰ� �����Ͻñ� �ٶ��ϴ�.';
	if (!confirm(msg)) return false;

	fObj.target = "_self";
	fObj.mode.value = "reserve";
	fObj.action = "indb.php";
	return true;
}

/*** �̺�Ʈ��� ��û ***/
function getEventList(sobj, selValue)
{
	if (sobj.options[sobj.selectedIndex].getAttribute("call") == null) return;
	function setcallopt(idx, text, value, defaultSelected, selected, call){
		if (idx == 0) for (i = sobj.options.length; i > 0; i--) sobj.remove(i);
		sobj.options[idx] = new Option(text, value, defaultSelected, selected);
		if (call != null) sobj.options[idx].setAttribute('call', call);
	}
	var ajax = new Ajax.Request( "../goods/indb.php",
	{
		method: "post",
		parameters: "mode=getEvent&page=" + sobj.options[sobj.selectedIndex].getAttribute("call") + "&selValue=" + (selValue != null ? selValue : ''),
		onLoading: function (){ setcallopt(0, '== �� �� �� ... ==', ''); },
		onComplete: function ()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var jsonData = eval( '(' + req.responseText + ')' );
				var lists = jsonData.lists;
				var page = jsonData.page;
				var idx = 0;
				if (page.prev != null) setcallopt(idx++, '�� ó����Ϻ���', '', false, false, '1');
				if (page.prev != null) setcallopt(idx++, '�� ������Ϻ���', '', false, false, page.prev);
				if (lists.length == 0) setcallopt(idx++, '== �̺�Ʈ�� �����ϴ� ==', '', false, false);
				for (i = 0; i < lists.length; i++){
					if (i == 0 || (selValue != null && selValue == lists[i].sno)) selected = true; else selected = false;
					setcallopt(idx++, '[' + lists[i].sdate + ' ~ ' + lists[i].edate + '] ' + lists[i].subject, lists[i].sno, false, selected);
				}
				if (page.next != null) setcallopt(idx++, '�� ������Ϻ���', '', false, false, page.next);
				sobj.form['seventpage'].value = page.now;
			}
			else {
				setcallopt(0, '�� �ε� �����ϱ�', '', false, false, '1');
				setcallopt(1, '[�ε�����] ��ε��ϼ���.', '', true, true);
			}
		}
	} );
}
--></script>

<div class="title title_top">���� �����ݼ���<span>��ǰ�������� ������ �ϰ������� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<!-- ��ǰ������� : start -->
<form name=frmList onsubmit="return chkForm(this)">

<div style="padding:10 0 5 5"><font class="def1" color="#000000"><b><font size="3">��</font> ���� �Ʒ����� �����ݼ����� ��ǰ�� �˻��մϴ�. <font class=extext>(�Ʒ� 3���� ����� �Ѱ����� �����Ͽ� �˻�)</font></b></font></div>
<div class="noline" style="padding:0 0 5 20"><input type="radio" name="indicate" value="search" <?=$checked[indicate]['search']?> required label="��ǰ�˻�����"><b>��ü��ǰ �˻�</b> <font class="extext">(��ü��ǰ�� ������� �˻��մϴ�. Ư�� �з��� ��ǰ�� ��� ������ �з� ������ ��ǰ���� �������� �ΰ� �˻�)</font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>�з�����</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td><font class=small1>�˻���</td>
	<td>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
	<option value="a.goodsno" <?=$selected[skey][a.goodsno]?>>������ȣ
	<option value="goodscd" <?=$selected[skey][goodscd]?>>��ǰ�ڵ�
	<option value="keyword" <?=$selected[skey][keyword]?>>����˻���
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
<tr>
	<td><font class=small1>��ǰ��¿���</td>
	<td class="noline"><font class=small1 color=555555>
	<input type=radio name=open value="" <?=$checked[open]['']?>>��ü
	<input type=radio name=open value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type=radio name=open value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
</tr>
</table>

<div class="noline" style="padding:15 0 5 20"><input type="radio" name="indicate" value="main" <?=$checked[indicate]['main']?> required label="��ǰ�˻�����"><b>���λ�ǰ</b> <font class="extext">(������������ ������ ��� ��ǰ�� ����մϴ�)</font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>��ǰ��������</td>
	<td>
	<select name=smain>
<? for ($i=0;$i<5;$i++){ ?>
	<option value="<?=$i?>" <?=$selected[smain][$i]?>><?=$cfg_step[$i][title]?>
<? } ?>
	</select>
	</td>
</tr>
</table>

<div class="noline" style="padding:15 0 5 20"><input type="radio" name="indicate" value="event" <?=$checked[indicate]['event']?> required label="��ǰ�˻�����"><b>�̺�Ʈ��ǰ</b> <font class="extext">(�̺�Ʈ��ǰ���� �����س��� ��ǰ���� ����մϴ�)</font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>�̺�Ʈ</td>
	<td>
	<select name=sevent onchange="getEventList(this)">
	<option value="" call="<?=$_GET[seventpage]?>">�� �ε� �����ϱ�</option>
	</select>
	<input type="hidden" name="seventpage">
	<script>window.onload = function (){ getEventList(frmList.sevent, '<?=$_GET[sevent]?>'); }</script>
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

</form>
<!-- ��ǰ������� : end -->

<form name="fmList" method="post" onsubmit="return chkFormList(this)">
<input type=hidden name=mode>
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" required msgR="�ϰ����� �� ��ǰ�� ���� �˻��ϼ���.">

<div class="pageInfo ver8">�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>��ü����</a></th>
	<th><font class=small1><b>��ȣ</th>
	<th colspan="2"><font class=small1><b>��ǰ��</th>
	<th><font class=small1><b>�ɼ�1</th>
	<th><font class=small1><b>�ɼ�2</th>
	<th><font class=small1><b>����</th>
	<th><font class=small1><b>�ǸŰ�</th>
	<th><font class=small1><b>���԰�</th>
	<th><font class=small1><b>������</th>
	<th><font class=small1><b>���</th>
	<th><font class=small1><b>����</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col span="2"><col width=70 span=5><col width=60><col width=35 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){

	if ($data['use_emoney'] == 0) {
		$disabled = 'disabled';
		$reserve = '�⺻��å';
	}
	else {
		$disabled = '';
		$reserve = number_format($data[reserve]);
	}
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[sno]?>" onclick="iciSelect(this)" <?=$disabled?>></td>
	<? if ($data[link]){ ?>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td>
		<a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a>
	</td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<? } else { ?><td><!--<?=$pg->idx--?>--></td><td></td><td></td>
	<? } ?>
	<td align=center><font class=small color=555555><?=$data[opt1]?></td>
	<td align=center><font class=small color=555555><?=$data[opt2]?></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[consumer])?></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[price])?></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[supply])?></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#0074BA"><b><?=$reserve?></b></font></td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($data[stock])?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="padding:20 0 5 5"><font class="def1" color="#000000"><b><font size="3">��</font> �� ��ǰ����Ʈ�� �ִ� ��ǰ�� ��������, �Ʒ� ������ �����Ͽ� �ϰ������մϴ�. <font class=extext>(�����ϰ� �����ϰ� �����ϼ���)</b></font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���������Ǽ���</td>
	<td>
	<div style="margin:5px 0">
	<span class="noline"><input type="radio" name="method" value="direct" <?=$checked[method]['direct']?> required label="�ϰ�������"></span>�������� �ϰ�
	<input type="text" name="reserve" value="<?=$_GET[reserve]?>" size="6" class="rline" label="������"> ������ �����մϴ�.
	</div>
	<div style="margin:5px 0">
	<span class="noline"><input type="radio" name="method" value="price" <?=$checked[method]['price']?> required label="�ϰ�������"></span>�������� �ǸŰ���
	<select name="percent">
	<?
	$idx = 0;
	while (($idx += ($idx <= 0.9 ? 0.1 : 1)) <= 100) echo "<option value=\"{$idx}\" " . $selected[percent]["{$idx}"] . ">{$idx}</option>";
	?>
	</select>%��
	<select name="roundunit">
	<option value="1" <?=$selected[roundunit][1]?>>1</option>
	<option value="10" <?=$selected[roundunit][10]?>>10</option>
	<option value="100" <?=$selected[roundunit][100]?>>100</option>
	<option value="1000" <?=$selected[roundunit][1000]?>>1000</option>
	</select>
	�� ������
	<select name="roundtype">
	<option value="down" <?=$selected[roundtype][down]?>>����</option>
	<option value="halfup" <?=$selected[roundtype][halfup]?>>�ݿø�</option>
	<option value="up" <?=$selected[roundtype][up]?>>�ø�</option>
	</select>
	�Ͽ� �����մϴ�.
	</div>
	<div style="margin:5px 0" class="noline">
	<input type="checkbox" name="isall" value="Y" <?=$checked[isall]['Y']?>>�˻��� ��ǰ ��ü<?=($pg->recode[total]?"({$pg->recode[total]}��)":"")?>�� �����մϴ�. <font class=extext>(��ǰ���� ���� ��� ������մϴ�. �����ϸ� �� �������� �����Ͽ� �����ϼ���)</div></td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_save.gif"></div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ϰ����� �� ��ǰ�� �˻� �� ��ǰ�������� �ϰ�ó�� ���ǿ� ���� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[����1] �ϰ����� �Ŀ��� <b>�������·� ������ �ȵǹǷ� �����ϰ� �����Ͻñ� �ٶ��ϴ�.</b></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[����2] ���� ���ϵ� �������� ���񽺸� ���ؼ� �˻������ ���� ��쿡�� �˻���� ��ü������ ���Ͻñ� �ٶ��ϴ�.</td></tr>
<tr><td style="padding-top:4"><img src="../img/icon_list.gif" align="absmiddle"><b>[�����ݼ��� ����]</b></td></tr>
<tr><td style="padding-left:10">�ǸŰ��� 5.5% ���ε� �������� �������� �ϰ������� �����ϰ�, ���� ������ 100�� ������ �����Ͽ� �����Ѵٸ�,</td></tr>
<tr><td style="padding-left:10">�ǸŰ� 10,000���� ��ǰ�� ������ ������ �����ϴ�.</td></tr>
<tr><td style="padding-left:10">�� 10,000 �� (5.5 / 100) = 550���̸�,</td></tr>
<tr><td style="padding-left:10">�� 100�� ���� �����ϸ� 500�� ���� ���� �����ݼ����� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
