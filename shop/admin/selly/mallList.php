<?
/*********************************************************
* ���ϸ�     :  mallList.php
* ���α׷��� :  ���� ����Ʈ
* �ۼ���     :  ����
* ������     :  2012.05.08
**********************************************************/
/*********************************************************
* ������     :  
* ��������   :  
**********************************************************/
$location = "���� > ���ϰ���";
include "../_header.php";
include "../../lib/sAPI.class.php";

list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

if(!$cust_seq || !$cust_seq) {
	msg("������ ��û�ϰ� ���� ���� ��� �Ŀ� ��밡���� �����Դϴ�.");
	go("./setting.php");
}

$sAPI = new sAPI();

$grp_cd = Array('grp_cd'=>'MALL_CD');
$ret_code = $sAPI->getCode($grp_cd, '');
$return_data = $ret_code['data'][0]['child']['return'][0]['child']['item'];

foreach($return_data as $data) {
	$tmp_data = $data['child'];
	$arr_mall_cd[$tmp_data['com_cd'][0]['data']]['mall_nm'] = $tmp_data['com_nm'][0]['data'];
	$arr_mall_cd[$tmp_data['com_cd'][0]['data']]['temp'] = $tmp_data['temp'][0]['data'];
}

$pagenum = $_GET['page'];

if(!$perpage) $perpage = '10';
if(!$pagenum) $pagenum = '1';

$mall_list_data['perpage'] = $perpage;
$mall_list_data['pagenum'] = $pagenum;
$arr_mall_list = $sAPI->getMallList($mall_list_data, 'hash');

if(!$totalcount) $totalcount = $arr_mall_list[0]['totalcount'];
if(!$nowpage) $nowpage = $arr_mall_list[0]['nowpage'];

$page_navi = $sAPI->exec_page($totalcount, $nowpage, '');

$use_yn = Array(
	'Y' => '���',
	'N' => '�̻��'
);

$domain_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'domain');
$res_domain = $db->_select($domain_query);
$domain = $res_domain[0]['value'];

$cust_seq_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_seq');
$cust_seq_res = $db->_select($cust_seq_query);
$cust_seq = $cust_seq_res[0]['value'];

$cust_cd_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');
$cust_cd_seq = $db->_select($cust_cd_query);
$cust_cd = $cust_cd_seq[0]['value'];

$seq = base64_encode($sAPI->xcryptare($cust_seq, $cust_cd, true));

?>

<script src="js/selly.js"></script>

<script>

function mall_modify(minfo_idx) {//���� �����˾�
	popup('mallInfo.php?minfo_idx=' + minfo_idx, 600, 300);
}

function mall_del(minfo_idx) {
	alert('��ϵ� ������ �����Ͻø� ��ũ�۾��� �Ͻ� �� �����ϴ�.');
	if (!confirm("�ش� ���������� �����Ͻðڽ��ϱ�?")) return;
	sellyLink.insMall('', '', '', '', '', 'delete', minfo_idx);
}

function successAjax(data) {
	var json_data = eval( '(' + data + ')' );

	if(json_data['code'] == '000') {//���� ��������
		alert(json_data['msg']);
		location.reload();
		return;
	}
	else {
		alert(json_data['msg']);
		return;
	}
}

function mall_register() {//���� ��������� �̵�
	location.replace("mallInfo.php");
}

function scm_login(minfo_idx) {
	if(minfo_idx == 'none') {
		alert('SCM�α��� ����� �������� �ʴ� ���� �Դϴ�.');
		return;
	}

	document.getElementsByName('minfo_idx')[0].value = minfo_idx;

	var fm = document.mallInfo;
	fm.target = "_blank";
	fm.method = "POST";
	fm.action = "http://<?=$domain?>/basic/STMallLoginTestShop.gm";
	fm.submit();
}

</script>

<div class="title title_top">���ϸ���Ʈ<span>SELLY�� ��ϵ� ������ �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<div style="padding-top:15px"></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<col style="width:5%"><col style="width:12%"><col style="width:16%"><col style="width:9%"><col style="width:19%"><col style="width:19%"><col style="width:10%"><col style="width:10%">
	<tr><td class="rnd" colspan="10"></td></tr>
	<tr class="rndbg">
		<th>No.</th>
		<th>����</th>
		<th>���� �α���ID</th>
		<th>��뿩��</th>
		<th>�����ֹ�������</th>
		<th>������ǰ��ũ��</th>
		<th>����/����</th>
		<th>SCM�α���</th>
	</tr>
	<tr><td class="rnd" colspan="10"></td></tr>
	<?
	if($arr_mall_list['code'] != '990' && is_array($arr_mall_list)) {
		$minus = 0;
		foreach($arr_mall_list as $key => $data) {
			if($data['mall_cd'] == 'mall0005') {
				$minus++;
				continue;
			}
		?>
		<tr><td height="4" colspan="10"></td></tr>
		<tr>
			<td align="center" class="noline"><!--No.-->
				<?=(($data['nowpage']-1)*10) + ($key+1-$minus)?>
			</td>
			<td align="center" class="noline"><!--����-->
				<?=$arr_mall_cd[$data['mall_cd']]['mall_nm']?>
			</td>
			<td align="center" class="noline"><!--���� �α���ID-->
				<?=$data['mall_login_id']?>
			</td>
			<td align="center" class="noline"><!--��뿩��-->
				<?=$use_yn[$data['status']]?>
			</td>
			<td align="center"><!--�����ֹ�������-->
				<?=$data['last_order_date']?>
			</td>
			<td align="center"><!--������ǰ��ũ��-->
				<?=$data['last_link_date']?>
			</td>
			<td align="center"><!--����/����-->
				<input type="image" src="../img/i_edit.gif" alt="����" onclick="mall_modify('<?=$data['minfo_idx']?>');">
				<input type="image" src="../img/i_del.gif" alt="����" onclick="mall_del('<?=$data['minfo_idx']?>');">
			</td>
			<td align="center"><!--SCM�α���-->
				<? if($arr_mall_cd[$data['mall_cd']]['temp'] == 'Y') { ?>
				<input type="image" src="../img/btn_scmlogin.gif" align="absbottom" alt="SCM�α���" onclick="scm_login('<?=$data['minfo_idx']?>');">
				<? } else { ?>
				<input type="image" src="../img/btn_no_addmall.gif" align="absbottom" alt="�����Ұ�" onclick="scm_login('none');">
				<? } ?>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=10 class=rndline></td></tr>
		<? } ?>
	<? } ?>
</table>
<div align="center" class="pageNavi"><font class="ver8"><?=$page_navi?></font></div>

<div align="right" class="pageNavi"><input type="image" src="../img/btn_addmarket.gif" align="absbottom" alt="���� ���" onclick="mall_register();"></div>

<form name="mallInfo">
	<input type="hidden" name="seq" value="<?=$seq?>">
	<input type="hidden" name="minfo_idx" value="">
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
����Ͻ� ���� ������ Ȯ�� �Ͻ� �� �ֽ��ϴ�.<br/>
���������� ���������� �۾��� �ð��� ǥ�õ˴ϴ�.<br/>
SCM�α��� ���� ���� ���ð� ����Ʈ ���� <img src="../img/btn_scmlogin.gif" align="absbottom"> ��ư�� �̿��Ͽ� one click ���� �̿��ϼ���~! <br/><br/><br/>

��ϵ� ���������� �����Ͻ� ��� ������ ���Ͽ� �ֹ������� ��ǰ��ũ/������ũ�� ����Ͻ� �� �����ϴ�.<br/>
������ ������ ����� ��� ���ϸ���Ʈ���� ����� ������ �������ּž� �մϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>