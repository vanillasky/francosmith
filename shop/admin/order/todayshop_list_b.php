<?
if ($_GET['goodstype']=='coupon') $loc_detail = '����(�ϰ��߱�)';
else $loc_detail = '�ǹ�(�ϰ��߼�)';

$location = "�ֹ����� > ".$loc_detail." �ֹ�����Ʈ";
include "../_header.php";
@include "../../conf/config.pay.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";
$_arStats = array('','�ǸŴ��','�Ǹ���','�ǸŽ���','�ǸſϷ�',);
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg('��û�Ŀ� ��밡���� �����Դϴ�.', -1);
}

// �⺻�� ����
if($_GET['first'] && $cfg['orderPeriod'])$_GET['period'] = $cfg['orderPeriod'];
if ($_GET[period] != ''){
	$_GET[regdt][0] = date("Ymd",strtotime("-$_GET[period] day"));
	$_GET[regdt][1] = date("Ymd");
}
$_GET['list'] = isset($_GET['list']) ? $_GET['list'] : 'goods';
$_GET['processtype'] = 'b';
$_GET['todaygoods'] = 'y';

$style['cyn']['y'] = "<b style='color:#0A246A'>";

if (!$_GET[dtkind]) $_GET[dtkind] = 'orddt'; # ó����
$checked[dtkind][$_GET[dtkind]] = $checked[settlekind][$_GET[settlekind]] = "checked";

$checked['goodstype'][$_GET[goodstype]] = 'checked';
$checked['stats'][$_GET[stats]] = 'checked';

$selected[skey][$_GET[skey]] = "selected";
$selected[sgkey][$_GET[sgkey]] = "selected";
$selected['company'][$_GET['company']] = "selected";

// ���޾�ü ��������
$res = $db->query("SELECT cp_sno, cp_name FROM ".GD_TODAYSHOP_COMPANY);
while($tmpData = $db->fetch($res, 1)) $cpData[] = array('cp_sno'=>$tmpData['cp_sno'], 'cp_name'=>$tmpData['cp_name']);
unset($res);

// ���� ����
	// ����Ʈ �ʵ�.
		$_SQL['FIELD'] = "
			a.*,
			b.m_id, b.m_no,
			G.goodsno, G.goodsnm,
			TG.tgsno, TG.goodsno, TG.encor, TG.visible, TG.startdt, TG.enddt, TG.regdt, TG.buyercnt, TG.fakestock, TG.limit_ea, TG.goodstype,

			IF (TG.processtype = 'i',
			4,
				IF (
					NOW() < TG.startdt,
					1,	/* �ǸŴ�� */
					IF (
						(NOW() <= TG.enddt OR TG.enddt IS NULL) AND G.runout = 0,
						2,	/* �Ǹ��� */
						IF (
							TG.fakestock2real = 1,
								IF (TG.limit_ea <> 0 AND (TG.buyercnt + TG.fakestock) < TG.limit_ea,
								3,	/* �ǸŽ��� */
								4	/* �ǸſϷ� = �Ǹ����� */
								)
								,
								IF (TG.limit_ea <> 0 AND TG.buyercnt < TG.limit_ea,
								3,	/* �ǸŽ��� */
								4	/* �ǸſϷ� = �Ǹ����� */
								)
						)
					)
				)
			) AS stats,

			(SELECT count(*) cntDv FROM ".GD_ORDER_ITEM." WHERE ordno=a.ordno and dvcode != '' and dvno != '') AS cntDv
		";



	// ��� ���̺�
		$_SQL['TABLE'] = "".
			GD_ORDER." AS a
			LEFT JOIN ".GD_MEMBER." AS b
			ON a.m_no=b.m_no
			INNER JOIN ".GD_ORDER_ITEM." AS OI
			ON OI.ordno = a.ordno
			INNER JOIN ".GD_GOODS." AS G
			ON OI.goodsno = G.goodsno
			INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
			ON G.goodsno = TG.goodsno
		";

	// GROUP ��
		$_SQL['GROUP'] = '';

	// ORDER ��
		if ($_GET['list']=="goods"){
			$_SQL['ORDER'] = "step2*10+step,dyn,a.ordno DESC";
		}
		else {
			$_SQL['ORDER'] = "a.ordno desc";	// �⺻ ����
		}

		//$_SQL['ORDER'] = "a.orddt DESC";	// �⺻ ��ǰ�� �ֹ� �׷� ����
		$_SQL['ORDER'] = "TG.goodsno DESC, a.orddt DESC";	// �⺻ ��ǰ�� �ֹ� �׷� ����


	// WHERE �� (�� �迭 �׸�� and ������)
		// ��ǰó������
		if ($_value = trim($_GET['processtype'])) {
			$_SQL['WHERE'][] = "TG.processtype = '$_value'";
		}

		// ��ǰó������
		if ($_value = trim($_GET['todaygoods'])) {
			$_SQL['WHERE'][] = "G.todaygoods = '$_value'";
		}

		// ���޾�ü
		if ($_value = trim($_GET[company])) {
			$_SQL['WHERE'][] = "TG.company = '$_value'";
		}

		// ���� ����
		if ($_value = trim($_GET[settlekind])) {
			$_SQL['WHERE'][] = "settlekind = '$_value'";
		}

		// ���� �˻�
		if ($_value = trim($_GET[sword])) {
			$_SQL['WHERE'][] = (($_GET[skey]=="all") ? "CONCAT( a.ordno, nameOrder, nameReceiver, bankSender, ifnull(m_id,'') )" : $_GET[skey])." LIKE '%$_value%'";
		}

		// ��ǰ �˻�
		if ($_value = trim($_GET[sgword])) {
			$_SQL['WHERE'][] = $_GET[sgkey]." LIKE '%$_value%'";

			// �ֹ� ��ǰ ���̺� �߰� JOIN
			$_SQL['TABLE'] .= "
					LEFT JOIN ".GD_ORDER_ITEM." AS c
					ON a.ordno=c.ordno
					";

			$_SQL['GROUP'] = "GROUP BY a.ordno";
		}

		// ó������
		if ($_GET[regdt][0]){
			if (!$_GET[regdt][1]) $_GET[regdt][1] = date("Ymd");
			$_SQL['WHERE'][] = $_GET[dtkind]." BETWEEN DATE_FORMAT(".$_GET[regdt][0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET[regdt][1].",'%Y-%m-%d 23:59:59')";
		}

		// ��ǰ����
		if ($_value = trim($_GET[goodstype])) {
			$_SQL['WHERE'][] = " TG.goodstype = '".$_value."'";
		}

		// �ǸŻ���
		if ($_value = trim($_GET[stats])) {
			switch ($_value) {
				case 2:
					$_SQL['WHERE'][] = " ((NOW() <= TG.enddt OR TG.enddt IS NULL) AND G.runout = 0) ";	// �Ǹ���
					break;
				case 3:
					$_SQL['WHERE'][] = " (NOW() >= TG.enddt OR G.runout = 1) AND (TG.limit_ea <> 0 AND TG.buyercnt+TG.fakestock < TG.limit_ea) ";	// �Ǹ� ����
					break;
				case 4:
					$_SQL['WHERE'][] = " (NOW() >= TG.enddt OR G.runout = 1) AND (TG.limit_ea = 0 OR TG.buyercnt+TG.fakestock >= TG.limit_ea) ";	// �Ǹ�����(=�ǸſϷ�)
					break;
			}
		}

		// �ֹ����� (���� ������ �����ϹǷ� OR ����)
		$_SQL['WHERE']['OR'] = array();
		if ($_GET[step]){
			$_SQL['WHERE']['OR'][] = "
					(step IN (".implode(",",$_GET[step]).") AND step2 = '')
					";

			foreach ($_GET[step] as $v) $checked[step][$v] = "checked";
		}

		if ($_GET[step2]) {
			foreach ($_GET[step2] as $v) {
				switch ($v){
					case "1": $_SQL['WHERE']['OR'][] = "(step=0 and step2 between 1 and 49)"; break;
					case "2": $_SQL['WHERE']['OR'][] = "(step in (1,2) and step2!=0) OR (a.cyn='r' and step2='44' and a.dyn!='e')"; break;
					case "3": $_SQL['WHERE']['OR'][] = "(step in (3,4) and step2!=0)"; break;
					case "60" :
						$_SQL['WHERE']['OR'][] = "(c.dyn='e' and c.cyn='e')";
						$_SQL['TABLE'] .= " left join ".GD_ORDER_ITEM." c on a.ordno=c.ordno";
						$_SQL['GROUP'] = "group by a.ordno";
					break; //��ȯ�Ϸ�
					case "61" : $_SQL['WHERE']['OR'][] = "oldordno != ''";break; //���ֹ�
					default:
						$_SQL['WHERE']['OR'][] = "step2=$v";
					break;
				}
				$checked[step2][$v] = "checked";
			}
		}

		if (!empty($_SQL['WHERE']['OR'])) $_SQL['WHERE'][] = "(".implode(" OR ",$_SQL['WHERE']['OR']).")";
		unset($_SQL['WHERE']['OR']);

	// ����¡ �� ����
		if(!$cfg['orderPageNum'])$cfg['orderPageNum'] = 15;
		$pg = new Page($_GET[page],$cfg['orderPageNum']);
		if ($_GET[mode]=="group") $pg->nolimit = 1;
		$pg->field = $_SQL['FIELD'];
		$pg->setQuery($_SQL['TABLE'],$_SQL['WHERE'],$_SQL['ORDER'],$_SQL['GROUP']);
		$pg->exec();
		$res = $db->query($pg->query);
		unset($_SQL);


// ������ ����� ���� ���ڵ� ����
	$arRow = array();

	if ($_GET['list'] == 'goods') {
		// ��ǰ�� �׷�
		while ($row = $db->fetch($res)) {
			$goodsno = $row['goodsno'];
			$arRow[$goodsno][] = $row;
		}
	}
	else {
		// �ֹ��Ǻ�
		while ($row = $db->fetch($res)) {
			$arRow[] = $row;
		}
	}

	$idx = 0;
?>

<script type="text/javascript" src="../todayshop/todayshop.js"></script>
<script>

function fnChangeList(m) {
	window.location.href = '<?=$_SERVER['PHP_SELF']?>?list='+m;
}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');

//	var c_table = row.lastChild.firstChild;
//	c_table.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');

	return;
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

function chkBox2(El,s,e,mode)
{
	if (!El || !El.length) return;
	for (i=s;i<e;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

function dnXls(mode)
{
	var fm = document.frmDnXls;
	fm.mode.value = mode;
	fm.target = "ifrmHidden";
	fm.action = "dnXls.php";
	fm.submit();
}

</script>

<div class="title title_top" style="position:relative;padding-bottom:15px">�����̼� <?=$loc_detail?> �ֹ�����Ʈ<span>�����̼��� �ֹ��� Ȯ���ϰ� �ֹ����¸� �����մϴ�</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=5')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
<!--div style="position:absolute;left:100%;width:231px;height:44px;margin-left:-240px;margin-top:-15px"><a href="../order/post_introduce.php"><img src="../img/btn_postoffic_reserve_go.gif"></a></div-->
</div>
<form>
	<input type="hidden" name="list" value="<?=$_GET['list']?>">
	<input type="hidden" name="goodstype" value="<?=$_GET['goodstype']?>">
	<table class=tb>
	<col class=cellC><col class=cellL style="width:250">
	<col class=cellC><col class=cellL>
	<tr>
		<td><font class=small1>���޾�ü</font></td>
		<td>
			<select name="company">
				<option value="">= ���޾�ü ���� =</option>
				<? for ($i = 0; $i < count($cpData); $i++){ ?>
				<option value="<?=$cpData[$i]['cp_sno']?>" <?=$selected['company'][$cpData[$i]['cp_sno']]?>><?=$cpData[$i]['cp_name']?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><font class=small1>�ֹ��˻� (����)</td>
		<td>
		<select name=skey>
		<option value="all"> = ���հ˻� =
		<option value="a.ordno" <?=$selected[skey][a.ordno]?>> �ֹ���ȣ
		<option value="a.nameOrder" <?=$selected[skey][nameOrder]?>> �ֹ��ڸ�
		<option value="a.nameReceiver" <?=$selected[skey][nameReceiver]?>> �����ڸ�
		<option value="a.bankSender" <?=$selected[skey][bankSender]?>> �Ա��ڸ�
		<option value="b.m_id" <?=$selected[skey][m_id]?>> ���̵�
		</select>
		<input type=text name=sword value="<?=$_GET[sword]?>" class=line>
		</td>
		<td><font class=small1>��ǰ�˻� (����)</td>
		<td>
		<select name=sgkey>
		<option value="G.goodsnm" <?=$selected[sgkey][goodsnm]?>> ��ǰ��
		<option value="G.brandnm" <?=$selected[sgkey][brandnm]?>> �귣��
		<option value="G.maker" <?=$selected[sgkey][maker]?>> ������
		</select>
		<input type=text name=sgword value="<?=$_GET[sgword]?>" class=line>
		</td>
	</tr>
	<tr>
		<td><font class=small1>�ֹ�����</td>
		<td colspan=3 class=noline>
		<?
			foreach ($r_step as $k=>$v){
				if ($_GET['goodstype'] == 'coupon' && in_array($k, array('0','2','3'))) continue;
		?>
		<div style="float:left; padding-right:10px"><font class=small1 color=5C5C5C><input type=checkbox name=step[] value="<?=$k?>" <?=$checked[step][$k]?>><?=$v?></div>
		<? } ?>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="1" <?=$checked[step2][1]?>><font class=small1 color=5C5C5C>�ֹ����</div>
		<div style="clear:both;"></div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="2" <?=$checked[step2][2]?>><font class=small1 color=5C5C5C>ȯ�Ұ���</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="3" <?=$checked[step2][3]?>><font class=small1 color=5C5C5C>��ǰ����</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="60" <?=$checked[step2][60]?>><font class=small1 color=5C5C5C>��ȯ�Ϸ�</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="61" <?=$checked[step2][61]?>><font class=small1 color=5C5C5C>���ֹ�</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="50" <?=$checked[step2][50]?>><font class=small1 color=5C5C5C>�����õ�</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="54" <?=$checked[step2][54]?>><font class=small1 color=5C5C5C>��������</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="51" <?=$checked[step2][51]?>><font class=small1 color=5C5C5C>PGȮ�ο��</div>
		</td>
	</tr>
	<tr>
		<td><font class=small1>�ǸŻ���</td>
		<td colspan=3 class="noline">
			<span class="small1" style="color:5C5C5C; margin-right:20px;">
			<label><input type="radio" name="stats" value="" <?=$checked[stats]['']?>>��ü</label>
			<label><input type="radio" name="stats" value="2" <?=$checked[stats][2]?>>�Ǹ���</label>
			<label><input type="radio" name="stats" value="4" <?=$checked[stats][4]?>>�ǸſϷ�</label>
			<label><input type="radio" name="stats" value="3" <?=$checked[stats][3]?>>�ǸŽ���</label>
			</span>
		</td>
	</tr>
	<tr>
		<td><font class=small1>ó������</td>
		<td colspan=3>
		<span class="noline small1" style="color:5C5C5C; margin-right:20px;">
		<input type=radio name=dtkind value="orddt" <?=$checked[dtkind]['orddt']?>>�ֹ���
		<input type=radio name=dtkind value="cdt" <?=$checked[dtkind]['cdt']?>>����Ȯ����
		<input type=radio name=dtkind value="ddt" <?=$checked[dtkind]['ddt']?>>�����
		<input type=radio name=dtkind value="confirmdt" <?=$checked[dtkind]['confirmdt']?>>��ۿϷ���
		</span>
		<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" size=12 class=line> -
		<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" size=12 class=line>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td><font class=small1>�������</td>
		<td colspan=3 class=noline><font class=small1 color=5C5C5C>
		<input type=radio name=settlekind value="" <?=$checked[settlekind]['']?>>��ü
		<input type=radio name=settlekind value="c" <?=$checked[settlekind]['c']?>>�ſ�ī��
		<input type=radio name=settlekind value="o" <?=$checked[settlekind]['o']?>>������ü
		<input type=radio name=settlekind value="v" <?=$checked[settlekind]['v']?>>�������
		<input type=radio name=settlekind value="h" <?=$checked[settlekind]['h']?>>�ڵ���
		<input type=radio name=settlekind value="d" <?=$checked[settlekind]['d']?>>��������
		<? if ($cfg['settlePg'] == "inipay") { ?>
		<input type=radio name=settlekind value="y" <?=$checked[settlekind]['y']?>>��������
		<? } ?>
		</td>
	</tr>
	</table>
	<div class="button_top">
	<input type=image src="../img/btn_search2.gif">
	</div>
</form>

<div style="padding-top:15px"></div>

<form name=frmList method=post action="./indb.todayshop_list.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="chgAll">
<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td align=right>
	<img src="../img/today_list01<?=($_GET['list']!='order') ? 'on' : ''?>.gif" onMouseOver='this.src="../img/today_list01on.gif";' onMouseOut='this.src="../img/today_list01<?=($_GET['list']!='order') ? 'on' : ''?>.gif";' border=0 align=absmiddle onClick="fnChangeList('goods')" class="hand">
	<img src="../img/today_list02<?=($_GET['list']=='order') ? 'on' : ''?>.gif" onMouseOver='this.src="../img/today_list02on.gif";' onMouseOut='this.src="../img/today_list02<?=($_GET['list']=='order') ? 'on' : ''?>.gif";' border=0 align=absmiddle onClick="fnChangeList('order')" class="hand">
	</td>
</tr>
<tr><td height=3></td></tr>
</table>
<? if ($_GET['list'] == 'goods') { ?>
<!-- ��ǰ�� ����Ʈ-->
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<colgroup>
		<col align="left">
		<col width="150">
		<col width="80">
		<col width="80">
		<col width="40">
		<col width="120">
		<col width="150">
		<col width="100">
		<col width="100">
		<col width="80">
		<col width="100">
		<col width="60">
	</colgroup>
	<tr><td class=rnd colspan=12></td></tr>
	<tr class=rndbg>
		<th style="padding-left:10px;">��ǰ��</th>
		<th>����Ⱓ</th>
		<th>�Ǹŷ�</th>
		<th>�ǸŻ���</th>
		<th>��ȣ</th>
		<th>�ֹ���</th>
		<th>�ֹ���ȣ</th>
		<th>�ֹ���</th>
		<th>�޴º�</th>
		<th>����</th>
		<th>�ݾ�</th>
		<th>�ֹ�����</th>
	</tr>
	<tr><td class=rnd colspan=12></td></tr>
	<?
	$arRow_keys = array_keys($arRow);
	for ($i=0,$max=sizeof($arRow_keys);$i<$max;$i++) {
		unset($supply); unset($selected);

		$data = $arRow[ $arRow_keys[$i] ];
		$data_size = sizeof($data);

		$item = $data[0];

		// �ֹ���ȣ �ܱ�
		$ordnos = array();
		for ($j=0;$j<$data_size;$j++) {
			$ordnos[] = $data[$j]['ordno'];
		}
	?>
	<tr align=center bg="">
		<td class="noline small4" valign="top" style="padding:9px">
			<?=$item[goodsnm]?>
			<p>
			<img src="../img/today_list03.gif" onMouseOver='this.src="../img/today_list03on.gif";' onMouseOut='this.src="../img/today_list03.gif";' border=0 onClick="<?=($item['stats'] > 1) ? 'nsTodayshopControl.order.view('.$item['goodsno'].');' : 'nsTodayshopControl.order.notAvail();'?>" class="hand">
			</p>
		</td>
		<td class="noline small4" valign="top" style="padding:9px"><?=$item['startdt']?><br/> ~ <br/><?=$item['enddt']?></td>
		<td class="noline small4" valign="top" style="padding:9px"><?=number_format($item['buyercnt'] + $item['fakestock'])?> (<?=number_format($item['buyercnt'])?> + <?=number_format($item['fakestock'])?>) / <?=($item['limit_ea'] > 0) ? number_format($item['limit_ea']) : '������'?></td>
		<td class="noline small4" valign="top" style="padding:9px"><?=$_arStats[$item['stats']]?></td>
		<td colspan="8">
			<table width="100%">
			<colgroup>
			<col width="40" align="center">
			<col width="120" align="center">
			<col width="150" align="center">
			<col width="100" align="center">
			<col width="100" align="center">
			<col width="80" align="center">
			<col width="100" align="center">
			<col width="60" align="center">
			</colgroup>
			<? for ($j=0;$j<$data_size;$j++) { ?>
			<?
				$row = $data[$j];

				$bgcolor = ($row[step2]) ? "#F0F4FF" : "#ffffff";
				$disabled = ($row[step2]) ? "disabled" : "";

				$stepMsg = $step = getStepMsg($row[step],$row[step2],$row[ordno]);
				if(strlen($step) > 10) $step = substr($step,10);

				if ( $row[deliverycode] || $row[cntDv] ) {
					$step = "<a href=\"javascript:popup('popup.delivery.php?ordno=$row[ordno]',650,500)\"><font color=0074BA><b><u>$step</u></b></font></a>";
				}

				$grp[settleprice][''] += $row[prn_settleprice];
			?>
			<tr height="30">
				<td class=noline valign="top" style="padding-top:7px;"><span class="ver8" style="color:#616161"><?=($pg->idx--)?></span></td>
				<td><font class=ver81 color=616161><?=substr($row[orddt],0,-3)?></font></td>
				<td>
					<a href="view.php?ordno=<?=$row[ordno]?>"><font class=ver81 color=0074BA><b><?=$row[ordno]?></b></font></a>
					<a href="javascript:popup('popup.order.php?ordno=<?=$row[ordno]?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
				</td>
				<td>
					<? if ($row[m_id]) { ?><span id="navig" name="navig" m_id="<?=$row[m_id]?>" m_no="<?=$row[m_no]?>"><? } ?><font class=small1 color=0074BA>
					<b><?=$row[nameOrder]?></b><? if ($row[m_id]){ ?> (<?=$row[m_id]?>)</font><? if ($row[m_id]) { ?></span><? } ?>
					<? } ?>
				</td>
				<td><font class=small1 color=444444><?=$row[nameReceiver]?></td>
				<td class=small4><?=$r_settlekind[$row[settlekind]]?></td>
				<td class=ver81><b><?=number_format($row[prn_settleprice])?></b></td>
				<td class=small4><?=($row[goodstype] == 'coupon') ? str_replace("���","�߱�",$step) : $step;?></td>
			</tr>
			<? if ($j < $data_size-1) {?><tr><td colspan=9 bgcolor=E4E4E4></td></tr><? } ?>
			<? } ?>
			</table>
		</td>
	</tr>
	<tr><td colspan=12 bgcolor=E4E4E4></td></tr>

	<? } ?>
	<?
		$cnt = $pr * ($idx+1);
		$s = $idx_grp - $cnt;
	?>
	<tr>
		<td align=right height=30 colspan=12 style=padding-right:8>�հ�: <!--(<?=$cnt?>��)--> <font class=ver9><b><?=number_format($grp[settleprice][$preStepMsg])?></font>��</b></td>
		<td></td>
	</tr>
	<tr bgcolor=#f7f7f7 height=30>
		<td colspan=12 align=right style=padding-right:8>��ü�հ� : <span class=ver9><b><?=number_format(@array_sum($grp[settleprice]))?>��</b></span></td>
		<td></td>
	</tr>
	<tr><td height=4 colspan="12"></td></tr>
	<tr><td colspan=12 class=rndline></td></tr>
	</table>

<!-- //��ǰ�� ����Ʈ-->
<? } else { ?>
<!-- �ֹ��� ����Ʈ-->
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<colgroup>
		<col width="40">
		<col width="100">
		<col width="130">
		<col align="left">
		<col width="80">
		<col width="80">
		<col width="150">
		<col width="80">
		<col width="100">
		<col width="60">
	</colgroup>
	<tr><td class=rnd colspan=12></td></tr>
	<tr class=rndbg>
		<th>��ȣ</th>
		<th>�ֹ���</th>
		<th>�ֹ���ȣ</th>
		<th>��ǰ��</th>
		<th>�ǸŻ���</th>
		<th>�ֹ���</th>
		<th>�޴º�</th>
		<th>����</th>
		<th>�ݾ�</th>
		<th>�ֹ�����</th>
	</tr>
	<tr><td class=rnd colspan=12></td></tr>
	<?
	$arRow_keys = array_keys($arRow);
	for ($i=0,$max=sizeof($arRow_keys);$i<$max;$i++) {
		unset($supply); unset($selected);
		$data = $row = $arRow[ $arRow_keys[$i] ];

		$item = $data;

		// �ֹ���ȣ �ܱ�
		$ordnos = array();

		$bgcolor = ($row[step2]) ? "#F0F4FF" : "#ffffff";
		$disabled = ($row[step2]) ? "disabled" : "";

		$stepMsg = $step = getStepMsg($row[step],$row[step2],$row[ordno]);
		if(strlen($step) > 10) $step = substr($step,10);

		if ( $row[deliverycode] || $row[cntDv] ) {
			$step = "<a href=\"javascript:popup('popup.delivery.php?ordno=$row[ordno]',650,500)\"><font color=0074BA><b><u>$step</u></b></font></a>";
		}

		$grp[settleprice][''] += $row[prn_settleprice];
	?>
	<tr height="35" align=center bg="">
		<td class=noline><font class=ver8 color=616161><?=($pg->idx--)?></span></td>
		<td><font class=ver81 color=616161><?=substr($row[orddt],0,-3)?></font></td>
		<td>
			<a href="view.php?ordno=<?=$row[ordno]?>"><font class=ver81 color=0074BA><b><?=$row[ordno]?></b></font></a>
			<a href="javascript:popup('popup.order.php?ordno=<?=$row[ordno]?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
		</td>
		<td class="noline small4"><?=$item[goodsnm]?></td>
		<td class="noline small4"><?=$_arStats[$item['stats']]?></td>
		<td>
			<? if ($row[m_id]) { ?><span id="navig" name="navig" m_id="<?=$row[m_id]?>" m_no="<?=$row[m_no]?>"><? } ?><font class=small1 color=0074BA>
			<b><?=$row[nameOrder]?></b><? if ($row[m_id]){ ?> (<?=$row[m_id]?>)</font><? if ($row[m_id]) { ?></span><? } ?>
			<? } ?>
		</td>
		<td><font class=small1 color=444444><?=$row[nameReceiver]?></td>
		<td class=small4><?=$r_settlekind[$row[settlekind]]?></td>
		<td class=ver81><b><?=number_format($row[prn_settleprice])?></b></td>
		<td class=small4><?=($row[goodstype] == 'coupon') ? str_replace("���","�߱�",$step) : $step;?></td>
	</tr>
	<tr><td colspan=12 bgcolor=E4E4E4></td></tr>
	<? } ?>
	<?
		$cnt = $pr * ($idx+1);
		$s = $idx_grp - $cnt;
	?>
	<tr>
		<td align=right height=30 colspan=12 style=padding-right:8>�հ�: <!--(<?=$cnt?>��)--> <font class=ver9><b><?=number_format($grp[settleprice][$preStepMsg])?></font>��</b></td>
		<td></td>
	</tr>
	<tr bgcolor=#f7f7f7 height=30>
		<td colspan=12 align=right style=padding-right:8>��ü�հ� : <span class=ver9><b><?=number_format(@array_sum($grp[settleprice]))?>��</b></span></td>
		<td></td>
	</tr>
	<tr><td height=4 colspan="13"></td></tr>
	<tr><td colspan=12 class=rndline></td></tr>
	</table>
<!-- //�ֹ��� ����Ʈ-->
<? } ?>
<div class=pageNavi align=center><font class=ver8><?=$pg->page[navi]?></font></div>
<div class=button>
<input type=image src="../img/btn_modify.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ��� �Ǵ� �ֹ�ó���帧 ������� �ֹ������� �����Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ����¸� �����Ͻ÷��� �ֹ��� ���� - ó���ܰ輱�� �� ������ư�� ��������.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ����º����� ���� �� �ֹ�ó���ܰ� (�ֹ�����, �Ա�Ȯ��, ����غ�, �����, ��ۿϷ�) �� ������  ó���Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><b>- ī������ֹ��� �Ʒ��� ���� ��찡 �߻��� �� �ֽ��ϴ�. (�ʵ��ϼ���!) -</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ش� PG�� �����ڸ�忡�� ������ �Ǿ�����, �ֹ�����Ʈ���� �ֹ����°� '�Ա�Ȯ��'�� �ƴ� '�����õ�'�� �Ǿ� �ִ� ��찡 �߻��� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̴� �߰��� ��Ż��� ������ ���ϰ��� ����� ���� ���� �ֹ����°� ������ ���� ���� ���Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��, �̿Ͱ��� ������ �Ǿ����� �ֹ����°� '�����õ�'�� ��� �ش��ֹ����� �ֹ��󼼳��� ���������� "�����õ�, ���� ����" ó���� �Ͻø� �ֹ�ó�����°� "�Ա�Ȯ��"���� �����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�׷��� �������� ���ϰ��� �޾� �ֹ�ó�����°� ����� ���̱⿡ �̿� ���ؼ��� ��Ȯ�� �����α׸� �ֹ��󼼳������������� Ȯ���� �� �� �����ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ���� ī������� �ֹ��� 1�� �����ߴµ� ��Ȥ PG�� �ʿ����� 2���� ����(�ߺ�����)�Ǵ� ��찡 �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ���� �ش� PG���� �����ڸ��� ���� �ߺ����ε� 2���߿� 1���� ������� ���ֽø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ߺ����ΰ��� üũ�ؼ� �ٷ� �������ó������ ������ �̼����� �߻��Ǿ� ���̰� �ǰ�, �ش� PG��κ��� �ŷ�������û ���� �������� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������ΰ��� �ֹ����¿� �ߺ����ΰ� ó���� �����ϰ� üũ�ؾ� �ϸ� �̿� ���� å���� ���θ� ��ڿ��� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�׻� ī��������� �̰� �ֹ�����Ʈ�� PG�翡�� �����ϴ� ������������ �������ΰǰ� ���ϸ鼭 ���Ǳ�� üũ�Ͽ� ó���Ͻñ� �ٶ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>
<? @include dirname(__FILE__) . "/../interpark/_order_list.php"; // ������ũ_��Ŭ��� ?>

<? include "../_footer.php"; ?>
