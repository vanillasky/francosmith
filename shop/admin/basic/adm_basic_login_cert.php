<?

$location = '�⺻���� > �����ں��� ����';
include '../_header.php';
@include '../../conf/config.admin_login_cert.php';

### �׷�� ��������
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

$res = $db->query("select aoc.aoc_sno, aoc.aoc_mobile, mb.m_id, mb.name, mb.level, mb.dormant_regDate from gd_admin_otp_contact as aoc left join ".GD_MEMBER." as mb on aoc.aoc_m_no = mb.m_no order by aoc_regdt asc");
?>
<script>
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function del(fm)
{
	if (!isChked(document.getElementsByName('chk[]'))) return;
	if (!confirm('������ ���� �Ͻðڽ��ϱ�?')) return;
	fm.target = "_self";
	fm.mode.value = "delContact";
	fm.action = "indb.login_cert.php";
	fm.submit();
}
</script>

<div class="title title_top">�޴��� ���� ����<span>������ ������ �α��� �� ��ϵ� �޴������� ���� �� ������ �����մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=40')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<!-- �ȳ� : Start -->
<div style="border:solid 4px #dce1e1; border-collapse:collapse; margin-bottom:20px; color:#666666; padding:10px 0 10px 10px;">
	<div class="g9" style="color:#0074BA"><b>* �޴��� ���� �����̶�?</b></div>
	<div style="padding-top:7px;">���θ� ������ ������ �α��� �� �Ʒ� <b>'���� �޴��� ����'</b> �� ����� ���� �޴��� ��ȣ�� ������ȣ�� ���� ���� �� ��ȣ ������ ���Ͽ� �α��� �� �� �ֵ��� ���ִ� ������ ���� ��ȭ ����Դϴ�.</div>
</div>
<!-- �ȳ� : End -->

<!-- �޴��� ���� ���� : Start -->
<form method="post" action="indb.login_cert.php">
<table class="tb">
<input type="hidden" name="mode" value="setAdminLoginCert">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>��� ����</td>
	<td class="noline">
	<input type="radio" name="use" value="Y" <?if($admLoginCertCfg['use'] == 'Y')echo"checked";?>> �����
	<input type="radio" name="use" value="N" <?if($admLoginCertCfg['use'] != 'Y')echo"checked";?>> ������
	</td>
</tr>
</table>
<div class="button" style="margin:10px;" align="center"><input type="image" src="../img/btn_save.gif"></div>
</form>
<!-- �޴��� ���� ���� : End -->

<!-- ��� : Start -->
<div class="pdv10" align="right" style="padding:0 5px 5px 0"><a href="javascript:popupLayer('adm_popup_login_cert_regit.php',500,450)"><img src="../img/i_add.gif" border=0></a></div>
<form name="fmList" method="post">
<input type="hidden" name="mode" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="6"></td></tr>
<tr class="rndbg">
	<th>����</th>
	<th>��ȣ</th>
	<th>���� �޴�����ȣ</th>
	<th>��Ī������ID</th>
	<th>�̸�</th>
	<th>�׷�</th>
</tr>
<tr><td class="rnd" colspan="6"></td></tr>
<col width="30" align="center">
<col width="30" align="center">
<?
while ($data=$db->fetch($res)){
	if($data['dormant_regDate'] != '0000-00-00 00:00:00'){
		$data['m_id'] = '(�޸�ȸ��)';
	}
?>
<tr height="40" align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['aoc_sno']?>" onclick="iciSelect(this);"></td>
	<td width="50" class="ver8"><?=++$idx?></td>
	<td><?=$data['aoc_mobile']?></td>
	<td><?=$data['m_id']?></td>
	<td><?=$data['name']?></td>
	<td><?=$r_grp[$data['level']]?></td>
</tr>
<tr><td colspan="6" class="rndline"></td></tr>
<? } ?>
</table>

<div style="height:35px; padding:5px 0 0 13px"><a href="javascript:del(document.fmList);"><img src="../img/btn_select_delete.gif" border="0" /></a></div>
</form>
<!-- ��� : End -->

<!-- �ܿ� SMS ����Ʈ : Start -->
<div style="padding-top:20px"></div>

<div style="border:solid 4px #dce1e1; border-collapse:collapse; color:#666666; padding:10px 0 10px 10px;">
	<table width="100%">
	<tr>
		<td>
		<? $sms = Core::loader('Sms');?>
		�ܿ� SMS ����Ʈ : <span style="font-weight:bold;color:#627DCE;"><?=number_format($sms->smsPt)?></span> ��
		</td>
		<td>
		<div style="padding-top:7px; color:#666666" class="g9">SMS ����Ʈ�� ���� ��� ������ȣ SMS�� �߼۵��� �����Ƿ� �޴��� ���� ����� �۵����� �ʽ��ϴ�.</div>
		<div style="padding-top:5px; color:#666666" class="g9">SMS ����Ʈ ���� �� �̿��Ͻñ� �ٶ��ϴ�.</div>
		</td>
		<td>
		<a href="../member/sms.pay.php"><img src="../img/btn_point_pay.gif" /></a>
		</td>
	</tr>
	</table>
</div>
<!-- �ܿ� SMS ����Ʈ : End -->

<!-- �ñ��� �ذ� : Start -->
<div style="padding-top:30px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ �޴��� ���� ����� ����ϱ� ���ؼ��� SMS����Ʈ�� �ʿ��ϸ�, ������ȣ ��û �� 1����Ʈ�� �����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ī������ID : ����� �޴��� ��ȣ�� ��Ī�� ������ID�� ǥ���ϸ�, �޴��� ��� �� ������ID ��Ī�� �ʼ��Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ID : �����ڱ��ѱ׷����� ��ϵ� ID�� ������ID�� ����� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<!-- �ñ��� �ذ� : End -->

<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>