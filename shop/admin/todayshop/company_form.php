<?
//$hiddenLeft = 1;
$location = "�����̼� > ���޾�ü���";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}


// ���� �ޱ� �� �⺻�� ����
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$sno = isset($_GET['sno']) ? $_GET['sno'] : '';


// ��ü���� ��������
$query = "SELECT * FROM ".GD_TODAYSHOP_COMPANY." WHERE cp_sno=".$sno;

// �����Ͱ� �ִ�.
if ( $sno != '' && ($data = $db->fetch($query)) !== NULL) {	// ������ ������ ���� sno �� ������ ���� üũ�ϰ� ��.
	$mode = 'modify';
}
else {
	$mode = 'register';
}

?>

<!-- -->
<form name="frmCompany" method="post" action="./indb.company.php" target="_self" onSubmit="return chkForm(this);">
<input type="hidden" name="returnUrl" value="<?=$_SERVER['REQUEST_URI']?>">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="sno" value="<?=($mode == 'modify') ? $sno : ''?>">

<!-- �⺻���� -->
	<div class=title style="margin-top:0px">�⺻����<span>*�� �ʼ� �Է� �����Դϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=12')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<table class=tb>
	<colgroup><col class=cellC width="120"><col class=cellL width="50%"><col class=cellC width="120"><col class=cellL width="50%"></colgroup>
	<tr>
		<td nowrap>��ü��*</td>
		<td <?=($mode != 'modify') ? 'colspan="3"' : '' ?>><input type="text" name="cp_name" style="width:200px" value="<?=$data['cp_name']?>" required label="��ü��" class="line"></td>
		<? if ($mode == 'modify') { ?>
		<td nowrap>�����</td>
		<td><?=$data['regdt']?></td>
		<? }?>
	</tr>
	<tr>
		<td nowrap>��ǥ�ڸ�</td>
		<td colspan=3><input type="text" name="cp_ceo" style="width:200px" value="<?=$data['cp_ceo']?>" label="��ǥ�ڸ�" class="line"></td>
	</tr>
	<tr>
		<td nowrap>��ü����</td>
		<td><input type="text" name="cp_type" style="width:200px" value="<?=$data['cp_type']?>" label="��ü����" class="line"></td>
		<td nowrap>����ڹ�ȣ</td>
		<td><input type="text" name="cp_bizno" style="width:150px" value="<?=$data['cp_bizno']?>" class="line"></td>
	</tr>
	<tr>
		<? $_arPhone_prefix = array('02','051','053','032','062','042','052','031','033','043','041','063','061','054','055','064','070','080'); ?>
		<td nowrap>��ȭ��ȣ*</td>
		<td>
			<input type="text" name="cp_phone" style="width:150px" value="<?=$data['cp_phone']?>" required label="��ȭ��ȣ" class="line">
		</td>

		<td nowrap>�ѽ���ȣ</td>
		<td><input type="text" name="cp_fax" style="width:150px" value="<?=$data['cp_fax']?>" label="�ѽ���ȣ" class="line"></td>
	</tr>
	<tr>
		<td nowrap>�ּ�</td>
		<td colspan="3">
		<? $_post = explode("-",$data['cp_address_post']) ?>
		<input type="text" name="zipcode[]" style="width:35px" value="<?=array_shift($_post)?>" class="line" label="�����ȣ">
		-
		<input type="text" name="zipcode[]" style="width:35px" value="<?=array_shift($_post)?>" class="line" label="�����ȣ">

		<a href="javascript:popup('../proc/popup_zipcode.php?form=opener.document.frmCompany',400,500)"><img src="../img/btn_zipcode.gif" align=absmiddle></a>

		<input type="text" name="address" style="width:100%" value="<?=$data['cp_address']?>" label="�ּ�" class="line">
		</td>
	</tr>
	<tr>
		<td nowrap>Ȩ������</td>
		<td colspan="3"><input type="text" name="cp_www" style="width:100%" value="<?=$data['cp_www']?>" label="Ȩ������" class="line"></td>
	</tr>
	</table>

<!-- ����� ���� -->
	<div class=title style="margin-top:0px">����� ����</div>

	<table class=tb>
	<colgroup><col class=cellC width="120"><col class=cellL width="50%"><col class=cellC width="120"><col class=cellL width="50%"></colgroup>
	<tr>
		<td nowrap>����ڸ�*</td>
		<td colspan="3"><input type="text" name="cp_man" style="width:200px" value="<?=$data['cp_man']?>" required label="����ڸ�" class="line"></td>
	</tr>
	<tr>
		<td nowrap>��ȭ��ȣ*</td>
		<td><input type="text" name="cp_man_phone" style="width:200px" value="<?=$data['cp_man_phone']?>" required label="��ȭ��ȣ" class="line"></td>
		<td nowrap>�޴���*</td>
		<td><input type="text" name="cp_man_mobile" style="width:200px" value="<?=$data['cp_man_mobile']?>" required class="line"></td>
	</tr>
	<tr>
		<td nowrap>�̸���*</td>
		<td colspan="3"><input type="text" name="cp_man_email" style="width:380px" value="<?=$data['cp_man_email']?>" required label="�̸���" class="line"></td>
	</tr>
	</table>

<!-- �������� -->
	<div class=title style="margin-top:0px">��������</div>
	<table class=tb>
	<colgroup><col class=cellC width="120"><col class=cellL width=""></colgroup>
	<tr>
		<td nowrap>������</td>
		<td><input type="text" name="cp_calc_rate" style="width:30px" value="<?=$data['cp_calc_rate']?>" label="������" class="line"> %</td>
	</tr>
	<tr>
		<td nowrap>������</td>
		<td><input type="text" name="cp_calc_day" style="width:30px" value="<?=$data['cp_calc_day']?>" label="������" class="line"></td>
	</tr>
	<tr>
		<td nowrap>�����*</td>
		<td><input type="text" name="cp_calc_account_bank" style="width:200px" value="<?=$data['cp_calc_account_bank']?>" required label="�����" class="line"></td>
	</tr>
	<tr>
		<td nowrap>���¹�ȣ*</td>
		<td><input type="text" name="cp_calc_account_no" style="width:200px" value="<?=$data['cp_calc_account_no']?>" required label="���¹�ȣ" class="line"></td>
	</tr>
	<tr>
		<td nowrap>������*</td>
		<td><input type="text" name="cp_calc_account_owner" style="width:200px" value="<?=$data['cp_calc_account_owner']?>" required label="������" class="line"></td>
	</tr>
	</table>

	<div style="border-bottom:3px #efefef solid;padding-top:30px"></div>

	<div class=button>
		<input type=image src="../img/btn_<?=$mode?>.gif">
		<?=$btn_list?>
		<?if($_GET['tgsno']){?>&nbsp;<a href="../../todayshop/today_goods.php?tgsno=<?=$_GET['tgsno']?>" target="_blank"><img src="../img/btn_goods_view.gif"></a><?}?>
	</div>
</form>
<!-- -->

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>
	<div>��ǰ�� �����ϴ� ��ü�� ���� ������ ����ϰ� �����մϴ�.</div>
	<div>��ü�� �⺻������ ��ǰ �ֹ��������� ������ ����� ����� ������ �Է��մϴ�.</div>
	<div>���������� ����� �ʿ��� ������ Ȯ�� �� �� �ֵ��� ����ϴ� �����̸�, ��ü�� �������� �������� �ʽ��ϴ�.</div>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>