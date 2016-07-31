<?php
$location = "���� > ���� �����Ǹ� ��û";
include "../_header.php";
require_once ('./_inc/config.inc.php');

// ���� �Ǹ�����
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg['shople'];

$origin_area = $origin_value = array();

if ($shopleCfg['origin_select'] == 1) {

	if ($shopleCfg['origin_type']) {
		$rs = $db->query("SELECT DISTINCT `area` as `name`, `area` as `value` FROM ".GD_SHOPLE_ORIGIN_CODE." WHERE `country` = '".($shopleCfg['origin_type'] == '01' ? '����' : '�ؿ�')."'");

		while ($row = $db->fetch($rs,1)) {
			$origin_area[] = $row;
		}
	}

	if ($shopleCfg['origin_value']) {

		$query = "
			SELECT
					`area`, `name`, `value`
			FROM ".GD_SHOPLE_ORIGIN_CODE."
			WHERE
					`area` = (
								SELECT `area` FROM ".GD_SHOPLE_ORIGIN_CODE."
								WHERE `value` = '".$shopleCfg['origin_value']."'
							)

		";
		$rs = $db->query($query);

		while ($row = $db->fetch($rs,1)) {
			$origin_value[] = $row;
		}

		$shopleCfg['origin_area'] = $origin_value[0]['area'];
	}

}
?>

<form method="post" action="./indb.config.php" target="ifrmHidden" id="frmConfig" name="frmConfig">

	<div class="title title_top">���� �Ǹ����� ���<span>���� ���񽺸� �̿��ϱ� ���� �⺻ �Ǹ������� ����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>������</td>
		<td class="noline">

			<div style="float:left;">
				<? foreach($_spt_ar_origin_type as $val => $label) { ?>
				<label><input type="radio" name="origin_type" id="origin_type" value="<?=$val?>" <?=($shopleCfg['origin_type'] == $val) ? 'checked' : '' ?> onclick="nsShople.origin.change(this.value);"><?=$label?></label>
				<? } ?>
			</div>

			<div style="float:left;margin-left:10px;display:<?=($shopleCfg['origin_type'] != '03') ? 'none' : 'block'?>;" id="origin_input_wrap"><input type="text" name="origin_name" value="<?=$shopleCfg['origin_name']?>" class="line" style="border:1px solid #CCCCCC !important;width:140px" maxlength="32"></div>

			<div style="clear:both;margin-top:5px;display:<?=($shopleCfg['origin_type'] != '03') ? 'block' : 'none'?>;" id="origin_select_wrap">
				<span>������ ���� </span> <input type="checkbox" name="origin_select" id="origin_select" value="1" onclick="nsShople.origin.toggle();" <?=$shopleCfg['origin_select'] ? 'checked' : '' ?>>

				<select style="width: 87px" name="origin_area" id="origin_area" class=select>
				<option value="">�����ϼ���</option>
				<? foreach ($origin_area as $k => $v) { ?>
				<option value="<?=$v['value']?>" <?=($v['value'] == $shopleCfg['origin_area']) ? 'selected' : ''?>><?=$v['name']?></option>
				<? } ?>
				</select>

				<select style="width: 87px" name="origin_value" id="origin_value" class=select>
				<option value="">�����ϼ���</option>
				<? foreach ($origin_value as $k => $v) { ?>
				<option value="<?=$v['value']?>" <?=($v['value'] == $shopleCfg['origin_value']) ? 'selected' : ''?>><?=$v['name']?></option>
				<? } ?>
				</select>
			</div>

		</td>

	</tr>
	<tr>
		<td>����� �ּ�</td>
		<td>
			<input type="text" name="address_out" value="<?=$shopleCfg['address_out']?>" class="line" style="width:300px" maxlength="32">
		</td>
	</tr>
	<tr>
		<td>��ǰ/��ȯ�� �ּ�</td>
		<td>
			<input type="text" name="address_in" value="<?=$shopleCfg['address_in']?>" class="line" style="width:300px" maxlength="32">
		</td>
	</tr>
	<tr>
		<td>A/S �ȳ�</td>
		<td>
			<textarea name="as_info" class="line" style="width:300px;height:80px;"><?=$shopleCfg['as_info']?></textarea>
		</td>
	</tr>
	<tr>
		<td>��ǰ/��ȯ �ȳ�</td>
		<td>
			<textarea name="rtnexch_info" class="line" style="width:300px;height:80px;"><?=$shopleCfg['rtnexch_info']?></textarea>
		</td>
	</tr>
	<tr>
		<td>��ǰ/��ȯ ��ۺ�</td>
		<td>
			��ǰ��ۺ� : <input type="text" name="return_dlv_price" value="<?=$shopleCfg['return_dlv_price']?>" class="line" style="width:50px" maxlength="5">��
			/
			��ȯ��ۺ� (�պ�) : <input type="text" name="exchange_dlv_price" value="<?=$shopleCfg['exchange_dlv_price']?>" class="line" style="width:50px" maxlength="5">��

		</td>
	</tr>
	<tr>
		<td>��۾�ü</td>
		<td>
			<select name="dlv_company">
			<option value="">����</option>
			<? foreach ($_spt_ar_dlv_company as $k => $v) { ?>
			<option value="<?=$k?>" <?=($shopleCfg['dlv_company'] == $k ? 'selected' : '')?>><?=$v?></option>
			<? } ?>
			</select>

		</td>
	</tr>
	</table>

	<div style="position:relative;">
		<div class=button >
		<input type=image src="../img/btn_save.gif">
		</div>
	</div>

</form>






<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11������ �����ϱ� ���� ���� �߰� ������ ����ؾ� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����� �ּҸ� �������� ��ǰ�� ���� ��۵˴ϴ�. ��Ȯ�� ����� ������ �Է����ּ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ/��ȯ�� �ּ�, A/S�ȳ�, ��ǰ/��ȯ�ȳ� ���� ���� 11���� ��ǰ�������� ����Ǿ� ���鿡�� ���޵Ǵ� �����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ȯ�ϰ� �Է��ؾ� �մϴ�.</td></tr>
</td></tr>
</table>
</div>

<script type="text/javascript" src="./_inc/common.js"></script>
<script type="text/javascript">
function _fnInit() {
	nsShople.origin.init();}

Event.observe(document, 'dom:loaded', _fnInit, false);
</script>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
