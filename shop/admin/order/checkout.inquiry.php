<?php
/**
 * ���̹�üũ�ƿ� �ֹ� > ���ǰ���
 * @author sunny, oneorzero
 */
$location = '���̹�üũ�ƿ� �ֹ� > ���ǰ���';
include '../_header.php';

// ���ڰ� ����
$page = ((int)$_GET['page']?(int)$_GET['page']:1);
$sword = $_GET['sword'];
$skey = ($_GET['skey']?$_GET['skey']:'all');
$Category = $_GET['Category'];
$IsAnswered = $_GET['IsAnswered'];
$regdt_start = (int)$_GET['regdt'][0];
$regdt_end = (int)$_GET['regdt'][1];

// �˻� �迭 �����
$arWhere=array();

// Ű���� �˻�
if($sword) {
	$sword = $db->_escape($sword);
	switch($skey) {
		case 'Title':
			$arWhere[] = "Title like '%{$sword}%'";
			break;
		case 'CustomerID':
			$arWhere[] = "CustomerID = '{$sword}'";
			break;
		case 'OrdererName':
			$arWhere[] = "OrdererName = '{$sword}'";
			break;
	}
}

// �з�
if($Category) {
	$tmp = explode('/',$Category);
	$Category1 = $tmp[0];
	$Category2 = $tmp[1];
	if($Category1 && $Category2) {
		$arWhere[] = $db->_query_print('(Category1=[s] and Category2=[s])',$Category1,$Category2);
	}
	elseif($Category1) {
		$arWhere[] = $db->_query_print('Category1=[s]',$Category1);
	}
}

// �亯����
if($IsAnswered=='y') {
	$arWhere[] = 'IsAnswered="y"';
}
elseif($IsAnswered=='n') {
	$arWhere[] = 'IsAnswered="n"';
}

// �ֹ��� �˻�
if($regdt_start && $regdt_end) {
	$tmp_start = date("Y-m-d 00:00:00",strtotime($regdt_start));
	$tmp_end = date("Y-m-d 23:59:59",strtotime($regdt_end));
	$arWhere[] = "InquiryDateTime between '{$tmp_start}' and '{$tmp_end}'";
}
elseif($regdt_start) {
	$tmp_start = date("Y-m-d 00:00:00",strtotime($regdt_start));
	$arWhere[] = "InquiryDateTime >= '{$tmp_start}'";
}
elseif($regdt_end) {
	$tmp_end = date("Y-m-d 23:59:59",strtotime($regdt_end));
	$arWhere[] = "InquiryDateTime <= '{$tmp_end}'";
}

if(count($arWhere)) {
	$strWhere = 'where '.implode(" and ",$arWhere);
}

$query = "select * from gd_navercheckout_inquiry $strWhere order by inquiryNo desc";
$inquiryList = $db->_select_page(10,$page,$query);

// ī�װ� ��� �����
$query = 'select Category1,Category2 from gd_navercheckout_inquiry group by Category1,Category2 order by Category1,Category2';
$result = $db->_select($query);
$categoryList = array();
$tmp='';
foreach($result as $eachResult) {
	if($tmp!=$eachResult['Category1']) {
		$categoryList[]=$eachResult['Category1'];
	}
	$categoryList[] = $eachResult['Category1'].'/'.$eachResult['Category2'];
	$tmp=$eachResult['Category1'];
}
?>
<script type="text/javascript">
function showDetail(inquiryNo) {
	$$('iframe.inquiryDetail').invoke('hide');
	var ifrm = $('inquiryDetail_'+inquiryNo);
	ifrm.style.height=100;
	ifrm.show();
	ifrm.src='checkout.inquiry.detail.php?inquiryNo='+inquiryNo;
}
</script>
<div class="title title_top">���ǰ���</div>
<form name="fm" method="get">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><font class="small1">Ű����˻�</font></td>
	<td>
		<select name="skey">
			<option value="Title" <?=frmSelected($skey,'Title')?>>����
			<option value="CustomerID" <?=frmSelected($skey,'CustomerID')?>>���̹����̵�
			<option value="OrdererName" <?=frmSelected($skey,'OrdererName')?>>�ֹ��ڸ�
		</select>
		<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line">
	</td>
</tr>
<tr>
	<td><font class="small1">�з�</font></td>
	<td>
		<select name="Category">
			<option value="">(�з���ü)
			<? foreach($categoryList as $eachCategory): ?>
			<option value="<?=$eachCategory?>" <?=frmSelected($Category,$eachCategory)?>><?=$eachCategory?></option>
			<? endforeach; ?>
		</select>
	</td>
</tr>
<tr>
	<td><font class="small1">�亯����</font></td>
	<td class="noline">
	<table>
		<tr>
			<td><input type="radio" name="IsAnswered" value="" <?=frmChecked($_GET['IsAnswered'],'')?>><font class="small1" color="#5C5C5C">��ü</font></td>
			<td><input type="radio" name="IsAnswered" value="y" <?=frmChecked($_GET['IsAnswered'],'y')?>><font class="small1" color="#5C5C5C">��</font></td>
			<td><input type="radio" name="IsAnswered" value="n" <?=frmChecked($_GET['IsAnswered'],'n')?>><font class="small1" color="#5C5C5C">�ƴϿ�</font></td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td><font class=small1>�����Ͻ�</td>
	<td>
	<input type=text name=regdt[] value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" class=cline> -
	<input type=text name=regdt[] value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" class=cline>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>

<table width="100%">
<tr>
	<td align="center">
	<input type="image" src="../img/btn_search2.gif" border="0" style="border:0px">
	</td>
</tr>
</table>
</form>


<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="9"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>��з�/�ߺз�</th>
	<th>����</th>
	<th>�ֹ���ȣ</th>
	<th>���̹����̵�</th>
	<th>�ֹ���</th>
	<th>�����Ͻ�</th>
	<th>�亯����</th>
	<th>�亯�ΰ�</th>
</tr>
<tr><td class="rnd" colspan="9"></td></tr>

<col align="center" width="40"/>
<col align="center" width="150" />
<col align="left" />
<col align="center" width="90" />
<col align="center" width="90" />
<col align="center" width="80" />
<col align="center" width="150" />
<col align="center" width="70" />
<col align="center" width="70" />
<? foreach($inquiryList['record'] as $k=>$data): ?>
<tr><td height="4" colspan="9"></td></tr>
<tr height="25">
	<td><font class="ver81" color="#616161"><?=$data['inquiryNo']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['Category1']?>/<?=$data['Category2']?></font></td>
	<td><font class="ver81" color="#616161"><span style="cursor:pointer" onclick="showDetail(<?=$data['inquiryNo']?>)"><?=$data['Title']?></span></font></td>
	<td><font class="ver81" color="#616161">
	<a href="#" onclick="popup('checkout.popup.orderdetail.php?orderNo=<?=$data['orderNo']?>',900,700)"><?=$data['OrderID']?></a></font></td>
	<td><font class="ver81" color="#616161"><?=$data['CustomerID']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['OrdererName']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['InquiryDateTime']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['IsAnswered']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['Answerable']?></font></td>
</tr>
<tr><td colspan="9" >
<iframe src="about:blank" style="width:100%;display:none;margin:0px;padding:0px" class="inquiryDetail" id="inquiryDetail_<?=$data['inquiryNo']?>" frameborder="0"></iframe>
</td></tr>
<tr><td colspan="9" class="rndline"></td></tr>
<? endforeach; ?>
<tr><td height="4"></td></tr>
</table>


<? $pageNavi = &$inquiryList['page']; ?>
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


<div style="margin-top:30px;"></div>
<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>������ Ŭ���Ͻø� �� ���ǿ� ���� �󼼳����� Ȯ���Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�亯�� ���̹��κ��� ������ �ΰ�Ǿ�� �󼼳��뿡�� �亯�� �ۼ��ϽǼ� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�� ���ǿ� ���ؼ��� 24�ð� �� �亯�� �ּ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include '../_footer.php'; ?>