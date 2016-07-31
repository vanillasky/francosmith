<?php

$location = "����ϼ����� > ���θ� ���� ��뿩�� ����";
include "../_header.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

if($_POST['save'] == 'setting'){

	$config_shoppingApp = array(
		'useApp'=>(string)$_POST['useApp'],
		'orderby'=>(string)$_POST['orderby'],
	);
	$config->save('shoppingApp',$config_shoppingApp);

	echo "
	<script>
	alert('����Ǿ����ϴ�');
	location.href='shopping_app_set.php';
	</script>
	";
	exit;
}

$load_config_shoppingApp = $config->load('shoppingApp');

$checked['useApp'][$load_config_shoppingApp['useApp']] = "checked";
$checked['orderby'][$load_config_shoppingApp['orderby']] = "checked";

$e_exceptions = unserialize($load_config_shoppingApp['e_exceptions']);

$page = ((int)$_GET['page']?(int)$_GET['page']:1);
if (!$_GET['page_num']) $_GET['page_num'] = 10; // ������ ���ڵ��

$selected[page_num][$_GET[page_num]] = "selected";

### ���� ����

$orderby = ($_GET['sort']) ? $_GET['sort'] : "a.regdt desc";

if(!empty($load_config_shoppingApp['orderby']) && !$_GET['sort']){
	$orderby = "a.".$load_config_shoppingApp['orderby']." desc";
}

// ���ǽ� �����
$arWhere=array();

### ī�װ�

if ($_GET['cate']){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}
if ($category){
	$arWhere[] = "category like '$category%'";
}

### �˻���
if($_GET['sword']) {
	$sword = $db->_escape($_GET['sword']);
	switch($_GET['skey']) {
		case 'goodsnm': $arWhere[] = "goodsnm like '{$sword}%'"; break;
		case 'goodsno': $arWhere[] = "a.goodsno = '{$sword}'"; break;
		case 'goodscd': $arWhere[] = "goodscd = '{$sword}'"; break;
		case 'keyword': $arWhere[] = "keyword = '{$sword}'"; break;
	}
}

### ��ǰ�����
if($_GET['regdt'][0] && $_GET['regdt'][1]) {
	$regdt_start = substr($_GET['regdt'][0],0,4).'-'.substr($_GET['regdt'][0],4,2).'-'.substr($_GET['regdt'][0],6,2).' 00:00:00';
	$regdt_end = substr($_GET['regdt'][1],0,4).'-'.substr($_GET['regdt'][1],4,2).'-'.substr($_GET['regdt'][1],6,2).' 23:59:59';
	$arWhere[] = $db->_query_print('a.regdt between [s] and [s]',$regdt_start,$regdt_end);
}
elseif($_GET['regdt'][0]) {
	$regdt_start = substr($_GET['regdt'][0],0,4).'-'.substr($_GET['regdt'][0],4,2).'-'.substr($_GET['regdt'][0],6,2).' 00:00:00';
	$arWhere[] = $db->_query_print('a.regdt >= [s]',$regdt_start);
}
elseif($_GET['regdt'][1]) {
	$regdt_end = substr($_GET['regdt'][1],0,4).'-'.substr($_GET['regdt'][1],4,2).'-'.substr($_GET['regdt'][1],6,2).' 23:59:59';
	$arWhere[] = $db->_query_print('a.regdt <= [s]',$regdt_end);
}

### �ֱټ�����
if($_GET['updatedt'][0] && $_GET['updatedt'][1]) {
	$regdt_start = substr($_GET['updatedt'][0],0,4).'-'.substr($_GET['updatedt'][0],4,2).'-'.substr($_GET['updatedt'][0],6,2).' 00:00:00';
	$regdt_end = substr($_GET['updatedt'][1],0,4).'-'.substr($_GET['updatedt'][1],4,2).'-'.substr($_GET['updatedt'][1],6,2).' 23:59:59';
	$arWhere[] = $db->_query_print('a.updatedt between [s] and [s]',$regdt_start,$regdt_end);
}
elseif($_GET['updatedt'][0]) {
	$regdt_start = substr($_GET['updatedt'][0],0,4).'-'.substr($_GET['updatedt'][0],4,2).'-'.substr($_GET['updatedt'][0],6,2).' 00:00:00';
	$arWhere[] = $db->_query_print('a.updatedt >= [s]',$regdt_start);
}
elseif($_GET['updatedt'][1]) {
	$regdt_end = substr($_GET['updatedt'][1],0,4).'-'.substr($_GET['updatedt'][1],4,2).'-'.substr($_GET['updatedt'][1],6,2).' 23:59:59';
	$arWhere[] = $db->_query_print('a.updatedt <= [s]',$regdt_end);
}

### ��ǰ����
if($_GET['price'][0] && $_GET['price'][1]) {
	$price_start = $_GET['price'][0];
	$price_end = $_GET['price'][1];
	$arWhere[] = $db->_query_print('b.price between [s] and [s]',$price_start,$price_end);
}
elseif($_GET['price'][0]) {
	$regdt_start = $_GET['price'][0];
	$arWhere[] = $db->_query_print('b.price >= [s]',$regdt_start);
}
elseif($_GET['price'][1]) {
	$regdt_end = $_GET['price'][1];
	$arWhere[] = $db->_query_print('b.price <= [s]',$regdt_end);
}

### ��ǰ���� ����
if($_GET['openYN']){
	if (count($e_exceptions)>0) {
		foreach($e_exceptions as $v) {
			if (strlen($exceptions_tmp)>0) 	$exceptions_tmp .= "','";
			$exceptions_tmp .= $v ;
		}
		$a_not = ($_GET['openYN'] == 'Y') ? "" : "not";
		$arWhere[] = " a.goodsno ". $a_not ." in ('".$exceptions_tmp."') ";
	}elseif($_GET['openYN'] == 'Y'){
		$arWhere[] = " 0 ";
	}
}

$strWhere = implode(' and ',$arWhere);

if(empty($strWhere)=== true) $strWhere = " 1 ";

$query = "
	select
		distinct a.goodsno,a.goodsnm,a.img_s,a.icon,a.open,a.regdt,a.runout,a.usestock,a.inpk_prdno,a.totstock,b.price,b.reserve,a.use_emoney,a.updatedt
	from
		".GD_GOODS." a left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1' left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno
	where
		{$strWhere}
	order by
		{$orderby}
";
$appList = $db->_select_page($_GET['page_num'],$page,$query);

$query = "select count(*) as cnt from gd_goods";
$result = $db->_select($query);
$totalTaxRecord = $result[0]['cnt'];

?>

<div class="title title_top">���θ� ���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=700>
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b>�� ���θ� ���� ���� �ȳ��Դϴ�. �� �о����.</b></div>
<div style="padding-top:7"><font class=g9 color=666666>�� ���θ� ���� ���� ��û �� �ݵ�� ���� ��ü�� ����� ���� ���� �ϼž� �մϴ�.(�����ȭ:070-4142-7481)</div>
<div style="padding-top:5"><font class=g9 color=666666>�� �⺻�� ��û�� ��ǰ �� �������� �� �ϳ��� �����Ͽ� ���� �� �� �ֽ��ϴ�. </div>
<div style="padding-top:5"><font class=g9 color=666666>�� ����� ��û�� ��ǰ�� ���������� ������ �� ������, 2���� �� ��θ� ���� ������ ������ �� �ֽ��ϴ�. </div></td></tr>
</table>

<div style="padding-top:10px;"></div>

<form method="post" action="shopping_app_set.php">
<input type="hidden" name="save" value="setting">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��뿩��</td>
	<td class="noline">
		<input type="radio" name="useApp" value="Y" <?=$checked['useApp']['Y']?> />�����<span class="small"><font class="extext">(���θ� ���÷� ��ǰ ������ ������ ��)</font></span> <input type="radio" name="useApp" value="N" <?=$checked['useApp']['N']?><?=$checked['useApp']['']?> />������<span class="small"><font class="extext">(���θ� ���÷� ��ǰ ������ ������ �� ����)</font></span>
	</td>
</tr>
<tr>
	<td>���ı���</td>
	<td class="noline">
		<input type="radio" name="orderby" value="regdt" <?=$checked['orderby']['regdt']?> <?=$checked['orderby']['']?>/>��ǰ �����
		<input type="radio" name="orderby" value="updatedt" <?=$checked['orderby']['updatedt']?> />�ֱ� ������
		<input type="radio" name="orderby" value="goodsno" <?=$checked['orderby']['goodsno']?> />��ǰ��ȣ <span class="small"><font class="extext">(������ ���ı������� ���ÿ� ��µ˴ϴ�.)</font></span>
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_save.gif"></div>
</font>
</form>

<form name="frmList">
<input type=hidden name=sort value="<?=$_GET['sort']?>">

<div class="title title_top">���θ� ���� ��ǰ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td class="noline"><script>new categoryBox('cate[]',4,'<?=$category?>','','frmList');</script></td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
		<select name=skey>
		<? foreach ( array('goodsnm'=>'��ǰ��','a.goodsno'=>'������ȣ','goodscd'=>'��ǰ�ڵ�','keyword'=>'����˻���') as $k => $v) { ?>
			<option value="<?=$k?>" <?=($k == $_GET['skey']) ? 'selected' : ''?>><?=$v?></option>
		<? } ?>
		<? unset($k,$v) ?>
		</select>
		<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td>
		<input type=text name=price[] value="<?=$_GET[price][0]?>" onkeydown="onlynumber()" size="15" class="rline"> �� -&nbsp;
		<input type=text name=price[] value="<?=$_GET[price][1]?>" onkeydown="onlynumber()" size="15" class="rline"> ��
	</td>
</tr>
<tr>
	<td>��ǰ�����</td>
	<td>
		<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
		<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
		<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>�ֱټ�����</td>
	<td>
		<input type=text name=updatedt[] value="<?=$_GET['updatedt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
		<input type=text name=updatedt[] value="<?=$_GET['updatedt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
		<a href="javascript:setDate('updatedt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
		<a href="javascript:setDate('updatedt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
		<a href="javascript:setDate('updatedt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
		<a href="javascript:setDate('updatedt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
		<a href="javascript:setDate('updatedt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
		<a href="javascript:setDate('updatedt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td class="noline">
		<input type="radio" name="openYN" value="" <?=frmChecked($_GET['openYN'],'')?>/> ��ü <input type="radio" name="openYN" value="Y" <?=frmChecked($_GET['openYN'],'Y')?>/> ���ξ��� ����<input type="radio" name="openYN" value="N" <?=frmChecked($_GET['openYN'],'N')?>/> ���ξ��� ������
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_search2.gif"></div><br/>

<table width=100%>
<tr>
	<td class=pageInfo>
		<font class=ver8>
			<? $pageNavi = &$appList['page']; ?>
			�� <b><?=$totalTaxRecord?></b>��,
			�˻� <b><?=number_format($pageNavi['totalcount'])?></b>��,
			<b><?=number_format($pageNavi['nowpage'])?></b> of <?=number_format($pageNavi['totalpage'])?> Pages
		</font>
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="a.goodsno desc" <?=frmSelected($orderby,'a.goodsno desc')?>>- ��ǰ��ȣ ���ġ�</option>
	<option value="a.goodsno asc" <?=frmSelected($orderby,'a.goodsno asc')?>>- ��ǰ��ȣ ���ġ�</option>
	<optgroup label="------------"></optgroup>
	<option value="a.regdt desc" <?=frmSelected($orderby,'a.regdt desc')?>>- ����� ���ġ�</option>
	<option value="a.regdt asc" <?=frmSelected($orderby,'a.regdt asc')?>>- ����� ���ġ�</option>
	<option value="b.price desc" <?=frmSelected($_GET['sort'],'b.price desc')?>>- ���� ���ġ�</option>
	<option value="b.price asc" <?=frmSelected($_GET['sort'],'b.price asc')?>>- ���� ���ġ�</option>
	<option value="a.updatedt desc" <?=frmSelected($orderby,'a.updatedt desc')?>>- �ֱ� ������ ���ġ�</option>
	<option value="a.updatedt asc" <?=frmSelected($orderby,'a.updatedt asc')?>>- �ֱ� ������ ���ġ�</option>
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

<form name="fmList" method="post" action="./indb.php" target="_self">
<input type="hidden" name="mode" value="AppSet">

<table width=100% cellpadding=0 cellspacing=0 style="border-collapse: collapse; word-break:break-all;">
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('goodsno[]'),'rev')" class=white>����</a></th>
	<th>��ȣ</th>
	<th></th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>�ֱټ�����</th>
	<th>����</th>
	<th>���</th>
	<th>���� ����</th>
</tr>
<? foreach($appList['record'] as $data): ?>
<tr height=43 align="center">
	<td class=noline><input type=checkbox name="goodsno[]" value="<?=$data['goodsno']?>" class="chkbox"></td>
	<td><font class=ver8 color=444444><?=$data['_rno']?></td>
	<td><font class=ver8 color=444444><a href="/shop/goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td align="left"><font class=ver8 color=444444><a href="/shop/admin/goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>" target="_blank"><?=$data['goodsnm']?></a></td>
	<td><font class=ver8 color=444444><?=$data['regdt']?></td>
	<td><font class=ver8 color=444444><?=$data['updatedt']?></td>
	<td><font class=ver8 color=444444><?=number_format($data['price'])?>��</td>
	<td><font class=ver8 color=444444><?=$data['totstock']?></td>
	<td><font class=ver8 color=444444><?=@in_array($data['goodsno'],$e_exceptions) ? "<img src='../img/icn_1.gif'>" : "<img src='../img/icn_0.gif'>";?></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? endforeach; ?>
</table>

<? $pageNavi = &$appList['page']; ?>
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

<div class="title title_top">���θ� ���� ��ǰ ���� <font class=extext> ������ ��ǰ�� ���� ���¸� �����մϴ�.</font> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��������</td>
	<td class="noline">
		<input type="radio" name="useYN" value="all" checked/>���θ��� ��ϵ� ��ü ��ǰ�� ���θ� ���ÿ��� �������� �ʽ��ϴ�.<br/>
		<input type="radio" name="useYN" value="Y" />���� ��ǰ�� ���θ� ���ÿ� �����մϴ�.<br/>
		<input type="radio" name="useYN" value="N" />���� ��ǰ�� ���θ� ���ÿ� �������� �ʽ��ϴ�.
	</td>
</tr>
</table>
<br/>
<div><font class=extext>���ʱ� ���� ���� ��� <b>������</b> �����Դϴ�. ���ϸ� ���̱� ���Ͽ� �����Ͻ� ��ǰ�� �����Ͽ� �������θ� �������ֽñ� �ٶ��ϴ�.</font></div>

<div class=button_top><input type=image src="../img/btn_save.gif"></div>

</form>

<? include "../_footer.php"; ?>