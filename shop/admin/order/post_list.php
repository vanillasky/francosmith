<?
$location = "�ù迬�� ���� > ��ü���ù� �����ȣ�߱�(1�ܰ�)";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/godopost.class.php";

$godopost = new godopost();

if(!$godopost->linked) {
	msg("��ü���ù� ������ ��û�ϼž� ��� �Ͻ� �� �ֽ��ϴ�");
	go("post_admin.php");
	exit;
}

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$period = $cfg['orderPeriod'];


$page = ((int)$_GET['page']?(int)$_GET['page']:1);
$sword = $_GET['sword'];
$skey = ($_GET['skey']?$_GET['skey']:'all');
$dvcodeflag = $_GET['dvcodeflag'];
$reserved = $_GET['reserved'];
$arStep = (array)$_GET['step'];
$settlekind = $_GET['settlekind'];
$regdt_start = (int)$_GET['regdt'][0];
$regdt_end = (int)$_GET['regdt'][1];


// �˻� �迭 �����
$arWhere=array();

// Ű���� �˻�
if($sword) {
	$sword = $db->_escape($sword);
	switch($skey) {
		case 'all':
			$arWhere[] = "(
				o.ordno = '{$sword}' or
				o.nameOrder = '{$sword}' or
				o.bankSender = '{$sword}'
			)";
			break;
		case 'ordno':
			$arWhere[] = "o.ordno = '{$sword}'";
			break;
		case 'nameOrder':
			$arWhere[] = "o.nameOrder = '{$sword}'";
			break;
		case 'bankSender':
			$arWhere[] = "o.bankSender = '{$sword}'";
			break;
	}
}

// �����ȣ �߱޻���
if($dvcodeflag=='yes') {
	$arWhere[] = 'o.deliverycode <> ""';
}
elseif($dvcodeflag=='no') {
	$arWhere[] = 'o.deliverycode = ""';
}
elseif($dvcodeflag=='error') {
	$arWhere[] = 'TRIM(o.mobileReceiver) NOT REGEXP \'^([0-9]{3,4})-?([0-9]{3,4})-?([0-9]{4})$\'';
}

// �ֹ����� �˻�
if(count($arStep)) {
	foreach($arStep as $k=>$v) {
		$arStep[$k]=(int)$v;
	}
	$arWhere[] = 'o.step in ('.implode(',',$arStep).')';
	$arWhere[] = 'o.step2 = 0';
}

// �ֹ��� �˻�
if($regdt_start && $regdt_end) {
	$tmp_start = date("Y-m-d 00:00:00",strtotime($regdt_start));
	$tmp_end = date("Y-m-d 23:59:59",strtotime($regdt_end));
	$arWhere[] = "o.orddt between '{$tmp_start}' and '{$tmp_end}'";
}
elseif($regdt_start) {
	$tmp_start = date("Y-m-d 00:00:00",strtotime($regdt_start));
	$arWhere[] = "o.orddt >= '{$tmp_start}'";
}
elseif($regdt_end) {
	$tmp_end = date("Y-m-d 23:59:59",strtotime($regdt_end));
	$arWhere[] = "o.orddt <= '{$tmp_end}'";
}

// ������� �˻�
if($settlekind) {
	$settlekind = $db->_escape($settlekind);
	$arWhere[] = "o.settlekind = '{$settlekind}'";
}

// ������� �˻�����
if($reserved=='yes') {
	$strJoinReserve = ' left join gd_godopost_reserved as p on o.deliverycode=p.deliverycode and o.deliveryno="100"';
	$arWhere[] = 'p.deliverycode';
}
elseif($reserved=='no') {
	$strJoinReserve = ' left join gd_godopost_reserved as p on o.deliverycode=p.deliverycode and o.deliveryno="100"';
	$arWhere[] = 'isnull(p.deliverycode)';
}

if(count($arWhere)) {
	$strWhere = 'where '.implode(" and ",$arWhere);
}


$query = "
	select 
		o.ordno,
		o.orddt,
		group_concat(concat_ws(',',cast(i.goodsno as char),cast(i.ea as char),i.goodsnm) SEPARATOR '\n\n') as goodsinfo,
		o.nameOrder,
		o.settlekind,
		o.settleprice,
		o.phoneReceiver,
		o.mobileReceiver,
		o.delivery,
		o.deli_type,
		o.deliveryno,
		o.deliverycode,
		o.step,
		o.step2
	from
		gd_order as o 
		left join gd_order_item as i on o.ordno=i.ordno
		{$strJoinReserve}
	{$strWhere}
	group by
		o.ordno
	order by
		o.ordno asc
";

$result = $db->_select_page(10,$page,$query);

foreach($result['record'] as $k=>$v) {
	$ar_line = explode("\n\n",$v['goodsinfo']);
	$ar_goods = array();
	foreach($ar_line as $each_line) {
		preg_match('/^([0-9]+),([0-9]+),(.+)$/',$each_line,$matches);
		$ar_goods[]=array(
			'goodsno'=>$matches[1],
			'ea'=>$matches[2],
			'goodsnm'=>$matches[3]
		);
	}
	unset($result['record'][$k]['goodsinfo']);
	$result['record'][$k]['goods']=$ar_goods;
}


// �����ȣ �̹߱� �ֹ� ���� �˾Ƴ���
$arWhere[] = 'o.deliverycode = ""';
$strWhere = 'where '.implode(" and ",$arWhere);
$query = "
	select
		count(*) as unassign_count
	from
		gd_order as o
	{$strWhere}
";
$tmp = $db->_select($query);
$unassign_count = $tmp[0]['unassign_count'];



// ��۾�ü ����
$query = "select deliveryno,deliverycomp from gd_list_delivery where useyn='y' order by deliverycomp asc";
$delivery_list = $db->_select($query);


?>
<script language='javascript'> 
function checkAll() {
	var ar_checkbox = $$(".sel_checkbox");
	if(ar_checkbox[0]) {
		var action=!ar_checkbox[0].checked;
	}
	ar_checkbox.each(function(item){
		item.checked=action;
		boxClick(item);
	});
}

function boxClick(obj) {
	var ar_checkbox = $$(".sel_checkbox");
	var checked_number=0;
	ar_checkbox.each(function(item){
		if(item.checked) checked_number++;
	});
	$('checked_number').innerHTML=checked_number;
}

function assign_deliverycode() {
	if(document.fmList.ps_method[0].checked) {
		var ar_checkbox = $$(".sel_checkbox");
		var checked_number=0;
		ar_checkbox.each(function(item){
			if(item.checked) checked_number++;
		});
		if(checked_number==0) {
			alert('���õ� �ֹ��� �����ϴ�');
		}
		else {
			document.fmList.submit();
		}
	}
	else {
		<? if($unassign_count): ?>
			document.fmList.submit();
		<? else: ?>
			alert('�����ȣ �̹߱� �ֹ��� �����ϴ�');
		<? endif; ?>
	}
}

function popupGodoPostItemAssign(ordno) {
	popupLayer('popup.godopost.itemassign.php?ordno='+ordno,780,500);
}

</script>
<div class="title title_top">��ü���ù� �����ȣ�߱�<span>��ü���ù� �ý��ۿ��� �����ȣ�� �ڵ����� �߱޹޽��ϴ�</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=13')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
</div>

<form name="fm" method='get'>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><font class="small1">���θ� �����ȣ �Ҵ����</font></td>
	<td>
		<? if($set['delivery']['basis'] == 0): ?>
			�ֹ��� ���ó��
		<? else: ?>
			��ǰ�� ���ó��
		<? endif; ?>
	</td>
</tr>
<tr>
	<td><font class="small1">Ű����˻�</font></td>
	<td>
		<select name="skey">
			<option value="all" <?=frmSelected($skey,'all')?>> ���հ˻�
			<option value="a.ordno" <?=frmSelected($skey,'a.ordno')?>> �ֹ���ȣ
			<option value="nameOrder" <?=frmSelected($skey,'nameOrder')?>> �ֹ��ڸ�
			<option value="bankSender" <?=frmSelected($skey,'bankSender')?>> �Ա��ڸ�
			<option value="m_id" <?=frmSelected($skey,'m_id')?>> ���̵�
		</select>
		<input type="text" name="sword" value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
<tr>
	<td><font class="small1">�����ȣ �߱޻���</font></td>
	<td class="noline">
	<table>
		<tr>
			<td><input type="radio" name="dvcodeflag" value="" <?=frmChecked($dvcodeflag,'')?>><font class="small1" color="#5C5C5C">��ü</font></td>
			<td><input type="radio" name="dvcodeflag" value="yes" <?=frmChecked($dvcodeflag,'yes')?>><font class="small1" color="#5C5C5C">�߱�</font></td>
			<td><input type="radio" name="dvcodeflag" value="no" <?=frmChecked($dvcodeflag,'no')?>><font class="small1" color="#5C5C5C">�̹߱�</font></td>
			<td><input type="radio" name="dvcodeflag" value="error" <?=frmChecked($dvcodeflag,'error')?>><font class="small1" color="#DB5200">����ó����</font></td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td><font class="small1">�������</font></td>
	<td class="noline">
	<table>
		<tr>
			<td><input type="radio" name="reserved" value="" <?=frmChecked($reserved,'')?>><font class="small1" color="#5C5C5C">��ü</font></td>
			<td><input type="radio" name="reserved" value="no" <?=frmChecked($reserved,'no')?>><font class="small1" color="#5C5C5C">���� ��</font></td>
			<td><input type="radio" name="reserved" value="yes" <?=frmChecked($reserved,'yes')?>><font class="small1" color="#5C5C5C">���� ��</font></td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td><font class="small1">�ֹ�����</font></td>
	<td class="noline">
	<table>
		<tr>
			<td><input type="checkbox" name="step[0]" value="0" <?=frmChecked($arStep[0],'0')?>><font class="small1" color="#5C5C5C">�ֹ�����</font></td>
			<td><input type="checkbox" name="step[1]" value="1" <?=frmChecked($arStep[1],'1')?>><font class="small1" color="#5C5C5C">�Ա�Ȯ��</font></td>
			<td><input type="checkbox" name="step[2]" value="2" <?=frmChecked($arStep[2],'2')?>><font class="small1" color="#5C5C5C">����غ���</font></td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td><font class=small1>�ֹ���</td>
	<td>
	<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" class=cline> -
	<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" class=cline>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td><font class="small1">�������</font></td>
	<td class="noline"><font class="small1" color="#5C5C5C">
	<input type="radio" name="settlekind" value="" <?=frmChecked($settlekind,'')?>> ��ü
	<input type="radio" name="settlekind" value="a" <?=frmChecked($settlekind,'a')?>> ������
	<input type="radio" name="settlekind" value="c" <?=frmChecked($settlekind,'c')?>> ī��
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

<br>


<form name="fmList" method="post" action="indb.godopost.assign.php" target="ifrmHidden">
<input type="hidden" name="mode" value="order_assign">

<input type="hidden" name="skey" value="<?=$skey?>">
<input type="hidden" name="sword" value="<?=$sword?>">
<input type="hidden" name="dvcodeflag" value="<?=$dvcodeflag?>">
<input type="hidden" name="reserved" value="<?=$reserved?>">
<input type="hidden" name="regdt[0]" value="<?=$regdt[0]?>">
<input type="hidden" name="regdt[1]" value="<?=$regdt[1]?>">
<input type="hidden" name="settlekind" value="<?=$settlekind?>">

<? foreach($arStep as $k=>$v): ?>
	<input type="hidden" name="step[<?=$k?>]" value="<?=$v?>"/>
<? endforeach; ?>



<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="8"></td></tr>
<tr class="rndbg">
	<th><span onclick="checkAll()" style="cursor:pointer">����</span></th>
	<th>�ֹ���</th>
	<th>�ֹ���ȣ</th>
	<th>��ǰ��</th>
	<th>�ֹ��ڸ�</th>
	<th>������</th>
	<th>���������</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="8"></td></tr>

<col align="center" width="40"/>
<col align="center" width="110" />
<col align="center" width="100" />
<col align="left" />
<col align="center" width="70" />
<col align="center" width="90" />
<col align="center" width="210" />
<col align="center" width="80" />

<? foreach($result['record'] as $k=>$data): ?>
<?
$data['orddt'] = date("Y-m-d H:i",strtotime($data['orddt']));
if(count($data['goods'])>1) {
	$data['goodsnm']=$data['goods'][0]['goodsnm'].' �� '.(count($data['goods'])-1).'��';
}
else {
	$data['goodsnm']=$data['goods'][0]['goodsnm'];
}
?>


<tr><td height="4" colspan="8"></td></tr>
<tr height="25">
	<td class="noline"><input type="checkbox" name="sel_ordno[]" value="<?=$data['ordno']?>" class="sel_checkbox" onclick="boxClick(this)"></td>
	<td><font class="ver81" color="#616161"><?=$data['orddt']?></font></td>
	<td><a href="view.php?ordno=<?=$data[ordno]?>"><font class=ver81 color=0074BA><b><?=$data[ordno]?></b></font></a></td>
	<td><font class="ver81" color="#616161"><?=$data['goodsnm']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['nameOrder']?></font></td>
	<td><font class="ver81" color="#616161"><?=number_format($data['settleprice'])?>��</font></td>
	<td><font class="ver81" color="#616161">
	<div>
	<? if (getPurePhoneNumber($data[mobileReceiver]) == '' && $data[deliveryno] == '0' && $data[deliverycode] == '') { ?>
	<font class="small1" color="#DB5200">����ó����</font>
	<? } else { ?>
		<? foreach($delivery_list as $each_delivery): ?>
			<? if($each_delivery['deliveryno']==$data['deliveryno']):?>
				<?=$each_delivery['deliverycomp']?>
			<? endif;?>
		<? endforeach; ?>
		<?=$data['deliverycode']?>
	<? } ?>
	</div>
	<div>
		<? if($set['delivery']['basis'] == '1'): ?>
			<input type="button" value=" ��ǰ�� ���� ��ȣ �Է� " onclick="popupGodoPostItemAssign('<?=$data['ordno']?>')">
		<? endif; ?>
	</div>
	</font></td>
	<td><font class="ver81" color="#616161"><?=getStepMsg($data['step'],$data['step2'])?></font></td>
</tr>


<? endforeach; ?>

<tr><td height="4"></td></tr>
<tr><td colspan="8" class="rndline"></td></tr>
</table>


<? $pageNavi = &$result['page']; ?>
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


<div class="noline" style="border:1px solid #cccccc;padding:10px">

<table>
<tr>
<td>
	<input type="radio" name="ps_method" value="selected" checked> 
	���õ� <span id="checked_number">0</span>���� �ֹ��� ���ؼ� ���ο� ��ü���ù� �����ȣ�� �߱��մϴ�<br>
	<input type="radio" name="ps_method" value="searched"> 
	�˻��� �ֹ� <?=$result['page']['totalcount']?>���� ��,
	�����ȣ �̹߱� �ֹ� (<?=$unassign_count?>)���� �ϰ� �߱��մϴ�.
</td>
<td style="padding-left:30px">
	<img src="../img/btn_postoffice_number.gif" style="cursor:pointer" onclick="assign_deliverycode()">
</td>
</table>

</div>




</form>

<br>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>

<tr><td><font class=def1 color=ffffff><strong>1.���õ� �ֹ��Ǻ� �����ȣ �߱�</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����Ʈ���� �����ȣ�� �߱� �ް��� �ϴ� �ֹ����� �����Ͽ� �߱޹��� �� �ֽ��ϴ�.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><strong>2.�����ȣ �ϰ� �߱�</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�˻��� �ֹ��ǿ� ���� �ϰ��� ���� ��ȣ�� �߱� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><strong>3.�����ȣ �߱� ������ ó��</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ �������� ����ó ������ �߸��� ������ ���(ex : Ư������,���� ��)�� �ֹ����� �����ȣ�� �߱޵��� �ʽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�߱��� ���� ���� �� ��������� ������ '����ó ����'�� ǥ��˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����ó ������ �ֹ����� ����ó�� ���� �Ͻ� �� �� �߱� �����ñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ���ȣ�� Ŭ���ϸ� �ֹ� ������ �����Ͻ� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>


<? include "../_footer.php"; ?>