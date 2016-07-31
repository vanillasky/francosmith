<?
include "../_header.popup.php";

if (is_file('../../conf/config.member_group.php')) {
	include('../../conf/config.member_group.php');
}
else {
	$member_grp_ruleset = array(
	'automaticFl' => '',
	'apprSystem' => '',
	'apprPointTitle' => '',
	'apprPointLabel' => '',
	'terms_p01' => '',
	'apprPointOrderPriceUnit' => '',
	'apprPointOrderPricePoint' => '',
	'terms_p02' => '',
	'apprPointOrderRepeatPoint' => '',
	'terms_p03' => '',
	'apprPointReviewRepeatPoint' => '',
	'terms_p04' => '',
	'apprPointLoginRepeatPoint' => '',
	);
}

if (!$_GET['mode']) $_GET['mode'] = "addGrp";
$title = ($_GET['mode']!="modGrp") ? "�߰�" : "����";

if ($_GET['mode']=="modGrp") $disabled = "disabled";
$query = "
	SELECT

		GRP.*,
		RULE.sno AS r_sno,
		RULE.type,
		RULE.by_score_limit,
		RULE.by_score_max,
		RULE.by_number_buy_limit,
		RULE.by_number_buy_max,
		RULE.by_number_review_require,
		RULE.by_number_order_require,
		RULE.mobile_by_number_buy_limit,
		RULE.mobile_by_number_buy_max,
		RULE.mobile_by_number_review_require,
		RULE.mobile_by_number_order_require
	FROM ".GD_MEMBER_GRP." AS GRP
	LEFT JOIN ".GD_MEMBER_GRP_RULESET." AS RULE
	ON GRP.sno = RULE.sno
	WHERE GRP.sno = '".$_GET['sno']."'
";
$data = $db->fetch($query,1);

if($_GET['mode'] == 'modGrp'){
	$query = "select count(*) from ".GD_MEMBER." where level='".$data['level']."'";
	list($memberCount) = $db->fetch($query);
}
/*
 * ��ġ�� �ű� ������ �������, ��ġ�� ���°�(�����ݾ� ����) ���� ��µǵ��� ����
 */
if ($data['moddt'] <= '2011-10-05 09:00:00') {
	if ($data['add_emoney'] > 0 && $data['add_emoney_type'] == 'N') $data['add_emoney_type'] = 'settle_amt';
	if ($data['dc'] > 0 && $data['dc_type'] == 'N') $data['dc_type'] = 'settle_amt';
}

$checked['free'][$data['free_deliveryfee']]		= "checked";

# ���� ������� �������� �������
$res = $db->query("select grpnm,level from ".GD_MEMBER_GRP." order by level asc");
while($row = $db->fetch($res)){
	$except_level['lv'][$row['level']] = $row['level'];
	$except_level['nm'][$row['level']] = $row['grpnm'];
}

# ������ ����
if(!$_GET['adminAuth']){
	$slevel	= 1;
	$elevel	= 79;
}else{
	$slevel	= 80;
	$elevel	= 100;
}

### ���� ī�װ� ����Ʈ
$arr_excate=array();
if ($data['excate']){
	$query = "
	select
		catnm,category
	from
		".GD_CATEGORY."
	where
		category in (".$data['excate'].")
	";
	$res = $db->query($query);
	while ($exc = $db->fetch($res)){
		 $r_category[$exc['category']] = $exc['catnm'];
	}
}

### ���� ��ǰ ����Ʈ
$arr_excep=array();
if ($data['excep']){
	$query = "
	select
		a.goodsno,a.goodsnm,a.img_s,b.price
	from
		".GD_GOODS." a,
		".GD_GOODS_OPTION." b
	where
		a.goodsno=b.goodsno and link and go_is_deleted <> '1'
		and a.goodsno in (".$data['excep'].")
	";
	$res = $db->query($query);
	while ($exc=$db->fetch($res)){
		 $arr_excep[] = $exc;
		 $r_excep[] = $exc['goodsno'];
	}
}

// ȸ�� ��� ������ �������� �� ������ �б�
$icon_path = '../../data/member/icon';
$ar_icons = scandir($icon_path.'/preset');

//2013/03/27 ���������� �����ݾ� ���� ����(���ǹ̻��)
if( $data['dc_type'] == 'settle_amt') $data['dc_type'] = 'goods';

//2011/09/06 ����������, �߰�������, �����ۺ� ���� ���� kmn
if( $data['dc_type'] ) $checked['dc_type'][$data['dc_type']] = "checked='checked'";
else $checked['dc_type']['goods'] = "checked='checked'";

$dc_std_amt[$data['dc_type']] = $data['dc_std_amt'];
$dc[$data['dc_type']] = $data['dc'];

if( $data['add_emoney_type'] ) $checked['add_emoney_type'][$data['add_emoney_type']] = "checked='checked'";
else $checked['add_emoney_type']['settle_amt'] = "checked='checked'";

$add_emoney_std_amt[$data['add_emoney_type']] = $data['add_emoney_std_amt'];
$add_emoney[$data['add_emoney_type']] = $data['add_emoney'];

$checked['free_deliveryfee_type'][$data['free_deliveryfee']] = "checked='checked'";
$free_deliveryfee_std_amt[$data['free_deliveryfee']] = $data['free_deliveryfee_std_amt'];

foreach($ar_icons as $k => $v) if ($v == '.' || $v == '..') unset($ar_icons[$k]);
?>
<script>
	function exec_add()
	{
		var ret;
		var str = new Array();
		var obj = document.forms[0]['cate[]'];
		for (i=0;i<obj.length;i++){
			if (obj[i].value){
				str[str.length] = obj[i][obj[i].selectedIndex].text;
				ret = obj[i].value;
			}
		}
		if (!ret){
			alert('ī�װ��� �������ּ���');
			return;
		}
		var obj = document.getElementById('objCategory');
		oTr = obj.insertRow();
		oTd = oTr.insertCell();
		oTd.id = "currPosition";
		oTd.innerHTML = str.join(" > ");
		oTd = oTr.insertCell();
		oTd.innerHTML = "\<input type=text name=category[] value='" + ret + "' style='display:none'>";
		oTd = oTr.insertCell();
		oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../img/i_del.gif' align=absmiddle></a>";
	}

	function cate_del(el)
	{
		idx = el.rowIndex;
		var obj = document.getElementById('objCategory');
		obj.deleteRow(idx);
	}
	function fnSetMemberGroupRuleset(el) {

		var id = 'leveling_type_' + el.value;

		$$(el.tagName+'[name="'+el.name+'"]').each(function(el){

			$('leveling_type_' + el.value).setStyle({
				display : (id == 'leveling_type_' + el.value) ? 'block' : 'none'
			});

		});

		var o = document.getElementById('leveling_type_' + el.value);
		o.style.display = 'block';
	}

	function chkForm2(frm) {
		chkForm(frm);
/*
		if(frm.by_number_buy_limit.value) {
			if(!frm.by_number_buy_max.value || (frm.by_number_buy_limit.value > frm.by_number_buy_max.value)) {
				alert('���űݾ��� '+frm.by_number_buy_limit.value+' �� ���� ���� ���� �Ǿ�� �մϴ�.');
				frm.by_number_buy_max.focus();
				return false;
			}
		}

		if(frm.by_number_buy_limit.value) {
			if(!frm.by_number_buy_max.value || (frm.by_number_buy_limit.value > frm.by_number_buy_max.value)) {
				alert('���űݾ��� '+frm.by_number_buy_limit.value+' �� ���� ���� ���� �Ǿ�� �մϴ�.');
				frm.by_number_buy_max.focus();
				return false;
			}
		}

		if(!frm.by_number_buy_limit.value && frm.by_number_buy_max.value) {
			alert('���űݾ��� '+frm.by_number_buy_limit.value+' �� ���� ���� ���� �Ǿ�� �մϴ�.');
			frm.by_number_buy_max.focus();
			return false;
		}
*/
	}

	function check_dormantInfo(){
		var menu = document.getElementsByName("menu[]");
		var member, dormant;

		for(var i=0; i<menu.length; i++){
			if(menu[i].value == 'member'){
				member = menu[i];
			}
			else if(menu[i].value == 'dormant'){
				dormant = menu[i];
			}
			else {

			}
		}

		if(dormant.checked === true && member.checked === false){
			alert("ȸ�� ���� ��� ���� �ִ� �����ڸ� �޸� ȸ�� ���� ���� �ο��� �����մϴ�.");
			dormant.checked = false;
		}
		if(dormant.checked === true){
			if(!confirm("�޸� ȸ���� ������ ������������ ��� �� �������� ó������ �� �ֽ��ϴ�.\n���� ������ �����Ͻñ� �ٶ��ϴ�.")){
				dormant.checked = false;
			}
		}
		return;
	}
</script>
<style>
fieldset.group-icon-wrap {display:block;margin:10px 4px 5px 4px;width:100%;}
fieldset.group-icon-wrap legend {padding:0 5px 0 5px}
fieldset.group-icon-wrap ol {list-style:none;padding:0;margin:10px;}
fieldset.group-icon-wrap ol li {margin:2px;padding:5px;float:left;cursor:pointer;height:16px}
fieldset.group-icon-wrap ol li.on {background:#eeeeee;}


</style>

<div class="title title_top"><?=($_GET['adminAuth'])?"�����ڱ׷�".$title."<span>�����ڱ׷��� �����ϼ���</span>":"ȸ���׷�".$title."<span>ȸ���׷��� �����ϼ���</span>"?></div>

<form name="frmMemberGroup" method="post" action="indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this);" target="ifrmHidden">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>">
<input type="hidden" name="sno" value="<?=$_GET['sno']?>">
<input type="hidden" name="adminAuth" value="<?=$_GET['adminAuth']?>">


	<!--�׷�����-->
		<table cellpadding="0" cellspacing="0" border="0" bgcolor="ebebeb"><tr><td bgcolor="e8e8e8">
		<table cellpadding="2" cellspacing="1" border="0" bgcolor="e8e8e8" width="100%">
		<tr>
			<td bgcolor="f6f6f6" width="160" align="center">�׷��</td>
			<td bgcolor="ffffff" width=""><input type="text" name="grpnm" value="<?=$data['grpnm']?>" required class="line" onKeyUp="document.getElementById('grpnm_disp_type_text').innerText = this.value;"></td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" width="160" align="center">�׷� ǥ��</td>
			<td bgcolor="ffffff" width="" class="noline">
			<div>
			<label><input type="radio" name="grpnm_disp_type" value="text" <?=$data['grpnm_disp_type'] != 'icon' ? 'checked' : ''?>>�ؽ�Ʈ</label> : <span id="grpnm_disp_type_text"><?=$data['grpnm']?></span>
			</div>
			<div>
			<? $icon = $icon_path.'/'.$data['grpnm_icon']; ?>

			<label><input type="radio" name="grpnm_disp_type" value="icon" <?=$data['grpnm_disp_type'] == 'icon' ? 'checked' : ''?>>������</label> : <img src="<?=(is_file($icon) ? $icon : '../img/ico_noimg_16.gif')?>" id="grpnm_disp_type_icon"/>
			</div>

			<fieldset class="group-icon-wrap"><legend>�׷� ������ ���� (�⺻���� �����Ǵ� ������ �̹��� �Դϴ�.)</legend>
			<ol>
			<? foreach ($ar_icons as $icon) { ?>
			<li><img src="<?=$icon_path.'/preset/'.$icon?>"></li>
			<? }?>
			</ol>
			</fieldset>

			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" width="160" align="center">������ ���</td>
			<td bgcolor="ffffff" width="" class="noline">
			<input type="file" name="group_icon">
			<input type="hidden" name="group_icon_preset" value="">
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">�׷췹��</td>
			<td bgcolor="ffffff">
			<select name="level" <?if($memberCount>0){?>onchange="alert('�׷췹�� ����� �׷쿡 ���� ȸ������ �׷췹���� �Բ� ����˴ϴ�.')"<?}?>>
		<?
			for($i = $slevel; $i <= $elevel; $i++){
				if($except_level['lv'][$i] == $i && $data['level'] != $i){
		?>
			<optgroup label="<?=$i?> - [<?=$except_level['nm'][$i]?>]"></optgroup>
		<?
				}else{
					if($data['level'] == $i){
						$strLevelNm		= " - [".$except_level['nm'][$i]."]";
						$strSelected	= "selected";
					}else{
						$strLevelNm		= "";
						$strSelected	= "";
					}
		?>
			<option value="<?=$i?>" <?=$strSelected?>><?=$i?><?=$strLevelNm?></option>
		<?
				}
			}
			echo '</select>&nbsp;['.$data['grpnm'].']�� ���� ȸ�� �� : '.$memberCount;
		?>
		</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" width="160" align="center">���� ��ġ</td>
			<td bgcolor="ffffff" width="">
				<div id="leveling_type_score" style="display:<?=$member_grp_ruleset['apprSystem'] == 'point' ? 'block' : 'none' ?>;">
					���� ���� <INPUT class="rline" value="<?=$data['by_score_limit']?>" size="4" type="text" name="by_score_limit" > �� �̻� ~ <INPUT class="rline" value="<?=$data['by_score_max']?>" size="4" type="text" name="by_score_max" > �� �̸�
				</div>

				<div id="leveling_type_number" style="display:<?=$member_grp_ruleset['apprSystem'] != 'point' ? 'block' : 'none' ?>;">

				<table cellpadding="0" cellspacing="0" style="width:510px; border:solid 1px #dddddd;">
				<col class=cellC style="width:70px;" ><col class=cellL style="width:220px;"><col class=cellR style="width:220px;">
				<tr style="solid 1px #dddddd;">
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">&nbsp;</td>
					<td style="color:#333333; background:#f6f6f6; font-weight:bold; text-align:center; border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">�� ��ü</td>
					<td style="color:#333333; background:#f6f6f6; font-weight:bold; text-align:center; border-bottom:solid 1px #dddddd;">����ϼ� �߰�����</td>
				</tr>
				<tr>
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">���űݾ�</td>
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['by_number_buy_limit']?>" size="4" type="text" name="by_number_buy_limit" > �� �̻� ~ <INPUT class="rline" value="<?=$data['by_number_buy_max']?>" size="4" type="text" name="by_number_buy_max" > �� �̸�
					</td>
					<td style="text-align:left; border-bottom:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['mobile_by_number_buy_limit']?>" size="4" type="text" name="mobile_by_number_buy_limit" > �� �̻� ~ <INPUT class="rline" value="<?=$data['mobile_by_number_buy_max']?>" size="4" type="text" name="mobile_by_number_buy_max" > �� �̸�
					</td>
				</tr>
				<tr>
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">����Ƚ��</td>
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['by_number_order_require']?>" size="4" type="text" name="by_number_order_require" > ȸ �̻�
					</td>
					<td style="text-align:left; border-bottom:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['mobile_by_number_order_require']?>" size="4" type="text" name="mobile_by_number_order_require" > ȸ �̻�
					</td>
				</tr>
				<tr>
					<td style="border-right:solid 1px #dddddd;">�����ı�</td>
					<td style="border-right:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['by_number_review_require']?>" size="4" type="text" name="by_number_review_require" > ȸ �̻�
					</td>
					<td style="text-align:left;">
						<INPUT class="rline" value="<?=$data['mobile_by_number_review_require']?>" size="4" type="text" name="mobile_by_number_review_require" > ȸ �̻�
					</td>
				</tr>
				</table>
				<div style="height:20px; line-height:20px; color:blue; font-weight:bold;">�� ���űݾ׼����� "[ ]�̻�" �ݾװ� "[ ]�̸�" �ݾ��� ��� �Է��ؾ� �մϴ�.</div>

				</div>

			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">����������</td>
			<td bgcolor="ffffff">
			<label><input type="radio" name="dc_type" value="N" style="border:0" <?=$checked['dc_type']['N']?> />���� ������ �������� ����</label><br/>

			<label><input type="radio" name="dc_type" value="goods" style="border:0" <?=$checked['dc_type']['goods']?> />�Ǹűݾ� ����</label><br/>
			<div style="padding:3px 0px 0px 10px;"><input type="text" name="dc_std_amt_goods" class="rline" value="<?= $dc_std_amt['goods']?>" size="7"/> �� �̻� ���Ž� ��ǰ �Ǹűݾ��� <input type="text" name="dc_goods" class="rline" value="<?= $dc['goods']?>" style="width:35px;" /> % ���� ������ �����մϴ�.</div>
			<div style="padding:3px 0px 0px 10px;"><font class="extext">��ۺ� ���ܵ� ��ǰ �Ǹűݾ��� �������� ���������� �־����ϴ�<br/>���űݾװ� ������� �������� �����ÿ��� �ݾ� �Է¶��� ����� �Ǵ� "0"���� �����ϼ���</font></div>
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">���ο���ī�װ�</td>
			<td bgcolor="ffffff">
			<script src="../../lib/js/categoryBox.js"></script>
			<div style="padding-top:3px"></div>
			<div style=padding-left:8><font class=small1 color=FF0066><img src="../img/icon_list.gif" align="absmiddle">ī�װ� ���� (ī�װ����� �� ������ ������ưŬ��)</font></div>
			<div style=padding-left:8><script>new categoryBox('cate[]',4,'','');</script>
			<a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
			<div class="box" style="padding:10 0 0 10">
			<table  cellpadding=8 cellspacing=0 id=objCategory bgcolor=f3f3f3 border=0 bordercolor=#cccccc style="border-collapse:collapse">
			<?
			if ($r_category){ foreach ($r_category as $k=>$v){ ?>
			<tr>
				<td id=currPosition><?=strip_tags(currPosition($k))?></td>
				<td><input type=text name=category[] value="<?=$k?>" style="display:none">
				<input type=hidden name=sort[] value="<?=-$v?>" class="sortBox right" maxlength=10 <?=$hidden[sort]?>></td>
				<td><a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a>
				</td>
			</tr>
			<? }} ?>
			</table>
			</div>
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center" height="50">���ο��ܻ�ǰ</td>
			<td bgcolor="ffffff">
			<div id=divRefer style="position:relative;z-index:99">
				<div style="padding:5px 0px 0px 3px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_refer[]', 'referX');" align="absmiddle" /> <font class="extext">������: ��ǰ���� �� �ݵ�� �ϴ� Ȯ�ι�ư�� �����ž� ���� ������ �˴ϴ�.</font></div>
				<div id="referX" style="padding-top:3px;">
					<?php foreach($arr_excep as $k => $v){ ?>
						<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
						<input type=hidden name="e_refer[]" value="<?php echo $v['goodsno']; ?>" />
					<?php } ?>
				</div>
			</div>
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">�߰�������</td>
			<td bgcolor="ffffff">
			<label><input type="radio" name="add_emoney_type" value="N" style="border:0" <?=$checked['add_emoney_type']['N']?> />�߰������� ������ �������� ����</label><br/>

			<label><input type="radio" name="add_emoney_type" value="goods" style="border:0" <?=$checked['add_emoney_type']['goods']?> />�Ǹűݾ� ����</label><br/>
			<div style="padding:3px 0px 0px 10px;"><input type="text" name="add_emoney_std_amt_goods" class="rline" value="<?= $add_emoney_std_amt['goods']?>" size="7"/> �� �̻� ���Ž� ��ǰ �Ǹűݾ��� <input type="text" name="add_emoney_goods" class="rline" value="<?= $add_emoney['goods']?>" style="width:35px;" /> % �߰����� ������ �����մϴ�.</div>
			<div style="padding:3px 0px 0px 10px;"><font class="extext">��ۺ� ���ܵ� ��ǰ �Ǹűݾ��� �������� �߰����� ������ �־����ϴ�<br/>���űݾװ� ������� ������ ���� �����ÿ��� �ݾ� �Է¶��� ����� �Ǵ� "0"���� �����ϼ���</font></div>

			<label><input type="radio" name="add_emoney_type" value="settle_amt" style="border:0" <?=$checked['add_emoney_type']['settle_amt']?> />�����ݾ� ����</label><br/>
			<div style="padding:3px 0px 0px 10px;"><input type="text" name="add_emoney_std_amt_settle_amt" class="rline" value="<?= $add_emoney_std_amt['settle_amt']?>" size="7"/> �� �̻� ������ �� �����ݾ��� <input type="text" name="add_emoney_settle_amt" class="rline" value="<?= $add_emoney['settle_amt']?>" style="width:35px;" /> % �߰����� ������ �����մϴ�.</div>
			<div style="padding:3px 0px 0px 10px;"><font class="extext">��ۺ�� �������� ���� ���Ե� �� �����ݾ��� �������� �߰����� ������ �־����ϴ�<br/>�����ݾװ� ������� ������ ���� �����ÿ��� �ݾ� �Է¶��� ����� �Ǵ� "0"���� �����ϼ���</font></div>
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">�����ۺ�</td>
			<td bgcolor="ffffff">
			<label><input type="radio" name="free_deliveryfee_type" value="N" style="border:0" <?= $checked['free_deliveryfee_type']['N']?>>���� ��ۺ� ������ �������� ����</label><br />
			<label><input type="radio" name="free_deliveryfee_type" value="goods" style="border:0" <?= $checked['free_deliveryfee_type']['goods']?>>�Ǹűݾ� ���� <input type="text" name="free_deliveryfee_std_amt_goods" class="rline" size="7" value="<?= $free_deliveryfee_std_amt['goods']?>">�� �̻� ��ǰ���Ž� ��ۺ� ����</label><br/>
			<label><input type="radio" name="free_deliveryfee_type" value="settle_amt" style="border:0" <?= $checked['free_deliveryfee_type']['settle_amt']?>>�����ݾ� ���� <input type="text" name="free_deliveryfee_std_amt_settle_amt" class="rline" size="7" value="<?= $free_deliveryfee_std_amt['settle_amt']?>">�� �̻� ��ǰ���Ž� ��ۺ� ����</label><br />
			<label><input type="radio" name="free_deliveryfee_type" value="Y" style="border:0" <?= $checked['free_deliveryfee_type']['Y']?>>���Ǿ��� ����ǰ �ֹ��� ��ۺ� ����</label>
			</td>
		</tr>
		<?
		if($_GET['adminAuth']){
			@include "../../conf/groupAuth.php";
			$arr = array('basic','design','goods','order','member','dormant','board','event','marketing','log','blog','todayshop','mobileShop','selly','shople','etc','hiebay');
			foreach($arr as $v)if(($rAuth[$data['level']] && in_array($v,$rAuth[$data['level']])) || $data['level'] == 100 )$checked['menu'][$v] = "checked";
		?>
		<tr><td bgcolor="f6f6f6" width="160" align="center">���� ���Ѽ���</td>
		<td bgcolor="ffffff">
			<div style="padding-left:15;padding-top:5px"><input type="checkbox" name="menu[]" value="basic" class="null" disabled <?=$checked['menu']['basic']?> />���θ��⺻���� <font class="extext" color="ea0095">(��ü�����ڿ��Ը� ������ �ο��˴ϴ�)</font></div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="design" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['design']?> />�����ΰ���</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="goods" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['goods']?> />��ǰ����</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="order" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['order']?> />�ֹ�����</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="member" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['member']?> onclick="javascript:check_dormantInfo();" />ȸ������</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="dormant" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['dormant']?> onclick="javascript:check_dormantInfo();" />�޸� ȸ������</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="board" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['board']?> />�Խ��ǰ���</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="event" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['event']?> />���θ��</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="marketing" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['marketing']?> />�����ð���</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="log" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['log']?> />������</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="blog" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['blog']?> />��αװ���</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="todayshop" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['todayshop']?> />�����̼� ����</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="mobileShop" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['mobileShop']?> />����ϼ�����</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="selly" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['selly']?> />����</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="shople" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['shople']?> />����</div>
			<!--<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="hiebay" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['hiebay']?> />����!ebay</div>-->
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="etc" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['etc']?> />�����</div>
		</td>
		</tr>
		<tr><td bgcolor="f6f6f6" width="160" align="center">��� ���Ѽ���</td>
		<td bgcolor="ffffff">
			<? if($data['level'] == 100 || $rAuthStatistics[$data['level']] == 'y') { $checked['statistics']['y'] = 'checked';}?>
			<div style="padding-left:15;padding-top:5px"><input type="checkbox" name="statistics" value="y" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['statistics']['y']?> />�����ڸ��� ��� ���̺� ���� </div>
		</td>
		</tr>
		<?}?>
		</table>
		</td></tr></table>
	<!--//�׷�����-->

<div style="margin-bottom:10px;padding-top:10px;" class=noline align="center">
<input type="image" src="../img/btn_confirm_s.gif">
</form>

<script>
linecss(document.form);

$$('fieldset.group-icon-wrap ol li').each(function(el){
	el
	.observe("mouseover", function(event) {
		el.addClassName('on');
	})
	.observe("mouseout", function(event) {
		el.removeClassName('on');
	})
	.observe("click", function(event) {
		var img = el.firstDescendant().readAttribute('src');
		$('grpnm_disp_type_icon').writeAttribute('src', img );
		document.frmMemberGroup.group_icon_preset.value = img;
	});

});
</script>