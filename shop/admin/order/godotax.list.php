<?php
$location = "�����ڼ��ݰ�꼭 > ���ڼ��ݰ�꼭 �߱޿�û���";
include "../_header.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

if(!array_key_exists('status',$_GET)) {
	$_GET['status']='no';
}

$godotax = Core::loader('godotax');
$config_godotax = $config->load('godotax');

if(!$config_godotax['site_id'] || !$config_godotax['api_key']) {
	msg("�����ڼ��ݰ�꼭 ����Ʈ�� ���� ID�� API_KEY�� �������ּž��մϴ�",'../order/godotax.setting.php');
	exit;
}

$tax_step = array( '�����û', '�������', '����Ϸ�', '���ڹ���','��������');

$page = ((int)$_GET['page']?(int)$_GET['page']:1);

if (!$_GET['page_num']) $_GET['page_num'] = 10; // ������ ���ڵ��
$selected[page_num][$_GET[page_num]] = "selected";

$enableOrderBy = array(
	'issuedate desc','issuedate desc','sno asc','sno desc','ordno asc','ordno desc'
);
if(!$_GET['sort']) $_GET['sort'] = 'sno desc';
if(in_array($_GET['sort'],$enableOrderBy)) {
	$orderby = $_GET['sort'];
}else{
	$orderby = "sno desc";
}
$selected[sort][$orderby] = "selected";

// ���ǽ� �����
$arWhere=array();
if($_GET['status']=='yes') {
	$arWhere[] = 't.step="4"';
}
elseif($_GET['status']=='no') {
	$arWhere[] = 't.step="0"';
}
else {
	$arWhere[] = '(t.step="0" or t.step="4")';
}

if($_GET['sword']) {
	$sword = $db->_escape($_GET['sword']);
	switch($_GET['skey']) {
		case 'company': $arWhere[] = "t.company like '{$sword}%'"; break;
		case 'name': $arWhere[] = "t.name = '{$sword}'"; break;
		case 'm_name': $arWhere[] = "m.name = '{$sword}'"; break;
		case 'm_id': $arWhere[] = "m.m_id = '{$sword}'"; break;
		case 'ordno': $arWhere[] = "o.ordno = '{$sword}'"; break;
	}
}

if($_GET['regdt'][0] && $_GET['regdt'][1]) {
	$regdt_start = substr($_GET['regdt'][0],0,4).'-'.substr($_GET['regdt'][0],4,2).'-'.substr($_GET['regdt'][0],6,2).' 00:00:00';
	$regdt_end = substr($_GET['regdt'][1],0,4).'-'.substr($_GET['regdt'][1],4,2).'-'.substr($_GET['regdt'][1],6,2).' 23:59:59';
	$arWhere[] = $db->_query_print('t.regdt between [s] and [s]',$regdt_start,$regdt_end);
}
elseif($_GET['regdt'][0]) {
	$regdt_start = substr($_GET['regdt'][0],0,4).'-'.substr($_GET['regdt'][0],4,2).'-'.substr($_GET['regdt'][0],6,2).' 00:00:00';
	$arWhere[] = $db->_query_print('t.regdt >= [s]',$regdt_start);
}
elseif($_GET['regdt'][1]) {
	$regdt_end = substr($_GET['regdt'][1],0,4).'-'.substr($_GET['regdt'][1],4,2).'-'.substr($_GET['regdt'][1],6,2).' 23:59:59';
	$arWhere[] = $db->_query_print('t.regdt <= [s]',$regdt_end);
}

$strWhere = implode(' and ',$arWhere);

$query = "
	select
		t.*,
		o.step as ord_step,
		o.step2 as ord_step2,
		o.prn_settleprice as ord_prn_settleprice,
		o.nameOrder as nameOrder,
		o.cashreceipt as cashreceipt,
		o.m_no,
		m.m_id,
		m.name as m_name
	from
		gd_tax as t
		left join gd_order as o on t.ordno=o.ordno
		left join gd_member as m on o.m_no=m.m_no
	where
		{$strWhere}
	order by
		{$orderby}
";
$taxList = $db->_select_page($_GET['page_num'],$page,$query);

$query = "select count(*) as cnt from gd_tax";
$result = $db->_select($query);
$totalTaxRecord = $result[0]['cnt'];

?>
<script type="text/javascript">
document.observe("dom:loaded", function() {
	$$('.btnGodotaxDetail').each(function(ele){
		ele.observe('click',function(event){
			Event.stop(event);
			var element = Event.element(event);
			window.open(ele.href,"taxinvoice","width=830,height=500,scrollbars=yes");
		});

	});



});

function sendGodoetax() {
	var frm = $('fmList');
	frm.action="../order/godotax.send.indb.php";
	var checked=false;
	frm.select(".chkbox").each(function(v){
		if(v.checked) checked=true;
	});
	if(checked==false) {
		alert("�����Ͻ� ������ ������ �ּ���");
		return;
	}
	frm.submit();
}

function modifyRequest() {
	var frm = $('fmList');
	frm.action="../order/godotax.modify.indb.php";
	var checked=false;
	frm.select(".chkbox").each(function(v){
		if(v.checked) checked=true;
	});
	if(checked==false) {
		alert("������ ���ݰ�꼭�� ������ �ּ���");
		return;
	}
	frm.submit();
}

function deleteRequest() {
	if(window.confirm('������ ���ݰ�꼭�� �����Ͻðڽ��ϱ�?')==false) {
		return;
	}
	var frm = $('fmList');
	frm.action="../order/godotax.delete.indb.php";
	var checked=false;
	frm.select(".chkbox").each(function(v){
		if(v.checked) checked=true;
	});
	if(checked==false) {
		alert("������ ���ݰ�꼭�� ������ �ּ���");
		return;
	}
	frm.submit();
}


function selectAll() {
	$$('.chkbox').each(function(v) {
		v.checked=true;
	});
}

function selectReserve() {
	$$('.chkbox').each(function(v) {
		if(v.checked) {
			v.checked=false;
		}
		else {
			v.checked=true;
		}
	});
}

function selectDeselect() {
	$$('.chkbox').each(function(v) {
		v.checked=false;
	});
}

function putPrice(sno) {
	var price = document.getElementsByName('modify['+sno+'][price]')[0].value
	if( !price ) price = 0;
	var supply	= Math.round( price / 1.1 );
	var surtax	= price - supply;
	document.getElementsByName('modify['+sno+'][supply]')[0].value = supply;
	document.getElementsByName('modify['+sno+'][surtax]')[0].value = surtax
}

</script>


<div class="title title_top">���ڼ��ݰ�꼭 �߱޿�û���<span>���Ű��� ��û�� ���ݰ�꼭�� ��ȸ�ϰ� �߱�ó�� �մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=21')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>


<form>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>����</td>
	<td>
		<select name="status">
		<option value="" >(��ü)</option>
		<option value="yes" <?=frmSelected($_GET['status'],'yes')?>>����</option>
		<option value="no" <?=frmSelected($_GET['status'],'no')?>>������</option>
		</select>
	</td>
</tr>
<tr>
	<td>Ű����˻�</td>
	<td>
		<select name="skey">
		<option value="company" <?=frmSelected($_GET['skey'],'company')?>> ��ȣ </option>
		<option value="name" <?=frmSelected($_GET['skey'],'name')?>> ��ǥ�� </option>
		<option value="m_name" <?=frmSelected($_GET['skey'],'m_name')?>> ��û�� </option>
		<option value="m_id" <?=frmSelected($_GET['skey'],'m_id')?>> ���̵� </option>
		<option value="ordno" <?=frmSelected($_GET['skey'],'ordno')?>> �ֹ���ȣ </option>
		</select>
		<input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
</tr>
<tr>
	<td>�����</td>
	<td>
		<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" class=cline> ~
		<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" class=cline>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif">
</div>

<table width=100%>
<tr>
	<td class=pageInfo>
		<font class=ver8>
			<? $pageNavi = &$taxList['page']; ?>
			�� <b><?=$totalTaxRecord?></b>��,
			�˻� <b><?=number_format($pageNavi['totalcount'])?></b>��,
			<b><?=number_format($pageNavi['nowpage'])?></b> of <?=number_format($pageNavi['totalpage'])?> Pages
		</font>
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="sno desc" <?=$selected[sort]['sno desc']?>>- ��û�� ���ġ�</option>
	<option value="sno asc" <?=$selected[sort]['sno asc']?>>- ��û�� ���ġ�</option>
	<optgroup label="------------"></optgroup>
	<option value="issuedate desc" <?=$selected[sort]['issuedate desc']?>>- �ۼ��� ���ġ�</option>
	<option value="issuedate asc" <?=$selected[sort]['issuedate asc']?>>- �ۼ��� ���ġ�</option>
	<option value="ordno desc" <?=$selected[sort]['ordno desc']?>>- �ֹ���ȣ ���ġ�</option>
	<option value="ordno asc" <?=$selected[sort]['ordno asc']?>>- �ֹ���ȣ ���ġ�</option>
	</select>&nbsp;
	<select name=page_num onchange="this.form.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>�� ���
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form method="post" action="" id="fmList" target="ifrmHidden">
<table width=100% cellspacing=0 cellpadding=0 border=1 bordercolor="#D9D9D9" style="border-collapse: collapse; word-break:break-all;">
<col width=35><col width=35><col width=120><col><col width=12%><col width=15%><col width=74><col width=100>
<tr class=rndbg>
	<th rowspan=2>����</th>
	<th rowspan=2>��ȣ</th>
	<th>�ֹ���ȣ</th>
	<th colspan=3>���������</th>
	<th>�ۼ���</th>
	<th rowspan=2>����</th>
</tr>
<tr class=rndbg>
	<th>��û�ڸ�</th>
	<th>��ǰ��</th>
	<th>�����ݾ�</th>
	<th>����ݾ�</th>
	<th>��û��</th>
</tr>

<? foreach($taxList['record'] as $data): ?>
<?
	$data['ord_step_str'] = $r_stepi[$data['ord_step']][$data['ord_step2']]; // �ֹ��� �ֹ��ܰ�
	$data['cashMsg'] = ($data['cashreceipt'])?"���ݿ���������":"";
	$data['step_str'] = $tax_step[$data['step']]; // ���ڼ��ݰ�꼭����

	if ( !$data['m_no'] ) $namestr = $data['nameOrder'];
	else $namestr = "{$data[m_name]}/<span id=\"navig\" name=\"navig\" m_id=\"{$data[m_id]}\" m_no=\"{$data[m_no]}\"><font color=0074BA><b>{$data[m_id]}</b></font></span>";

	$itemList = $db->_select("select goodsno,goodsnm,ea from gd_order_item where ordno='{$data['ordno']}'");

	if($data['company']) $data['company'] = htmlspecialchars($data['company']);

	$order = Core::loader('order');
	$order->load($data['ordno']);
	$data['ord_prn_settleprice'] = $order->getRealPrnSettleAmount();
?>
<tr height=25 align="center">
	<td rowspan=2 class=noline><input type=checkbox name="chk[]" value="<?=$data['sno']?>" class="chkbox"></td>
	<td rowspan=2><font class=ver8 color=444444><?=$data['_rno']?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><font class=ver81 color=0074BA><b><?=$data['ordno']?></b></font></a> <font class=small color=EA0095><nobr><b><?=$data['ord_step_str']?></b></font></td>
	<td colspan=3 align=left style="padding:5 0 5 7"><font class=small color=444444>
	����ڹ�ȣ : <input type=text name="modify[<?=$data['sno']?>][busino]" value="<?=$data['busino']?>" style="width:85" maxlength=10 class=line>&nbsp;&nbsp;
	ȸ��� : <input type=text name="modify[<?=$data['sno']?>][company]" value="<?=$data['company']?>" style="width:25%;" class=line><br>
	��ǥ�ڼ��� : <input type=text name="modify[<?=$data['sno']?>][name]" value="<?=$data['name']?>" style="width:85;" class=line>&nbsp;&nbsp;
	����<font color=white>��</font> : <input type=text name="modify[<?=$data['sno']?>][service]" value="<?=$data['service']?>" style="width:17%;" class=line>&nbsp;&nbsp;
	���� : <input type=text name="modify[<?=$data['sno']?>][item]" value="<?=$data['item']?>" style="width:17%;" class=line><br>
	������ּ� : <input type=text name="modify[<?=$data['sno']?>][address]" value="<?=$data['address']?>" style="width:422;" class=line>
	</td>
	<td><input type=text name="modify[<?=$data['sno']?>][issuedate]" value="<?=$data['issuedate']?>" size=10 maxlength=10 style="text-align:center;" class=line></td>
	<td rowspan=2>
		<font color=EA0095><b><?=$data['step_str']?></b></font>
		<div style='padding-top:3'><font class=ver81 color=0074BA><b><?=$cashMsg?></b></font></div>
		<? if($data['doc_number']): ?>
			<br>
			<div style='padding-top:3'><a href="<?=$godotax->getLinkDetail($data['doc_number'])?>" class="btnGodotaxDetail"><img src="../img/btn_bill_view.gif"></a></div>
		<? endif; ?>
	</td>
</tr>
<tr height=25 align="center">
	<td><font class=small color=444444><?=$namestr?></td>
	<td>
		<?
			if(count($itemList)>1) {
				echo $itemList[0]['goodsnm'].' �� '.(count($itemList)-1).' ��';
			}
			else {
				echo $itemList[0]['goodsnm'];
			}
		?>
	</td>
	<td><?=number_format($data['ord_prn_settleprice'])?></td>
	<td><font class=small color=444444>
	����� <input type=text name="modify[<?=$data['sno']?>][price]" value="<?=$data[price]?>" size=8 maxlength=11 style="text-align:right;" onKeyDown="onlynumber();" onkeyup="putPrice( '<?=$data[sno]?>');" class=rline><br>
	���޾� <input type=text name="modify[<?=$data['sno']?>][supply]" value="<?=$data[supply]?>" size=8 maxlength=11 style="text-align:right;" readonly class=rline><br>
	�ΰ��� <input type=text name="modify[<?=$data['sno']?>][surtax]" value="<?=$data[surtax]?>" size=8 maxlength=11 style="text-align:right;" readonly class=rline>
	</td>
	<td><font class=ver8 color=444444><?=$data[regdt]?></td>
</tr>
<? endforeach; ?>
</table>
</form>



<? $pageNavi = &$taxList['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['prev']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">�� </a>
	<? endif; ?>
	<? foreach($pageNavi['page'] as $v): ?>
		<? if($v==$pageNavi['nowpage']): ?>
			<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
		<? else: ?>
			<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
		<? endif; ?>
	<? endforeach; ?>
	<? if($pageNavi['next']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">��</a>
	<? endif; ?>
</div>




<div style="float:left;">
<img src="../img/btn_allselect_s.gif" alt="��ü����"  border="0" align='absmiddle' style="cursor:hand" onclick="selectAll()">
<img src="../img/btn_allreselect_s.gif" alt="���ù���"  border="0" align='absmiddle' style="cursor:hand" onclick="selectReserve()">
<img src="../img/btn_alldeselect_s.gif" alt="��������"  border="0" align='absmiddle' style="cursor:hand" onclick="selectDeselect()">
<img src="../img/btn_alldelet_s.gif" alt="���û���" border="0" align='absmiddle' style="cursor:hand" onclick="deleteRequest()">
</div>

<div style="float:right;">
<img src="../img/btn_allmodify_s.gif" alt="�ϰ�����" border=0 align=absmiddle style="cursor:hand" onclick="modifyRequest()">
</div>

<div style="clear:both; text-align:center;">
<img src="../img/btn_godobill_go.gif" alt="�����ڼ��ݰ�꼭 ����" border=0 align=absmiddle style="cursor:hand" onclick="sendGodoetax()">
</div>

<a href="<?=$godotax->getLinkList()?>" target="_blank">
<img src="../img/btn_godobill_list.gif" alt="�����ڼ��ݰ�꼭 ���������" border=0 align=absmiddle style="cursor:hand">
</a>
<div style="clear:both; padding-top:35;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڼ��ݰ�꼭 ���� ���</td></tr>
<tr><td>&nbsp; &nbsp; : ���ڼ��ݰ�꼭 �߱� ��û��� ��ȸ �� �߱� ���ϴ� ��� ���� �� [������ ����] Ŭ�� �� ���� Ȩ���������� ���ڼ��ݰ�꼭 ����</td></tr>
<tr><td height=8></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ó������ �ȳ�</td></tr>
<tr><td>&nbsp; &nbsp; a. �����û : ���Ű��� ���ڼ��ݰ�꼭�� ���� ��û�� �����Դϴ�.</td></tr>
<tr><td>&nbsp; &nbsp; b. �������� : �߻���û ���� ������ ������ ����ó�� �Ϸ��� �����Դϴ�.</td></tr>
<tr><td height=8></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������� �߱��� ���ڼ��ݰ�꼭��  �̸��Ϸ� ������ �߼۵Ǹ�, ���ÿ� SMS�� �߼� ������ ������ �մϴ�.</td></tr>
<tr><td height=8></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڼ��ݰ�꼭 ��û�� ���θ��� �ֹ�/�����ȸ �޴����� ���Ű��� ������û �� �� �ֽ��ϴ�.</td></tr>
<tr><td height=8></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڼ��ݰ�꼭 ����</td></tr>
<tr><td>&nbsp; &nbsp; a. ����� ��ǰ�� ���ܵ˴ϴ�.</td></tr>
<tr><td>&nbsp; &nbsp; b. ��ۺ�� ����ݾ׿� ���Ե��� �ʽ��ϴ�.</td></tr>
<tr><td>&nbsp; &nbsp; c. ����ݾ��� ���ξ�(ȸ������+��������+�����ݻ��+������) ��ŭ �����˴ϴ�</td></tr>
<tr><td height=8></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>
