<?
include "../_header.popup.php";
include "../../lib/page.class.php";

if (is_file("../../conf/config.stocked_noti.php")) include "../../conf/config.stocked_noti.php";
else {
	// �⺻ ���� ��
	$stocked_noti_cfg = array(
		'msg' => '[{shopName}]
{name}��
{goodsnm}�� ���԰� �Ǿ����ϴ�.
',
		'short_name' => false
		);
}

// ���� �ޱ�
$goodsno = isset($_GET['goodsno']) ? $_GET['goodsno'] : '';
$optno = isset($_GET['optno']) ? $_GET['optno'] : '';
$opt1 = isset($_GET['opt1']) ? $_GET['opt1'] : '';
$opt2 = isset($_GET['opt2']) ? $_GET['opt2'] : '';
$selOption = isset($_GET['selOption']) ? $_GET['selOption'] : 'all';
$selected['selOption'][$selOption] = "selected";
$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : '10';


if (!$goodsno) {
	?>
		<script>
		alert('������ �߸��Ǿ����ϴ�.');
		parent.closeLayer();
		</script>
	<?
	exit;
}

$sms = Core::loader('sms');
$formatter = Core::loader('stringFormatter');

// ���� �����
	$db_table = "
	".GD_GOODS." AS G
	INNER JOIN ".GD_GOODS_STOCKED_NOTI." AS NT
	ON G.goodsno = NT.goodsno
	INNER JOIN ".GD_MEMBER." AS MB
	ON NT.m_id = MB.m_id
	";

// ������
	$where[] = "G.goodsno = '$goodsno'";
	$where[] = "NT.opt1 = '$opt1'";
	$where[] = "NT.opt2 = '$opt2'";
	if($selOption == "sended"){
		$where[] = "NT.sended = 1";
	}else if($selOption == "notsended"){
		$where[] = "NT.sended = 0";
	}

// ����
	$orderby = "NT.regdt ASC";

// �׷�
	$groupby = "";

// ��ü ��ǰ�� (ǰ���Ǹ�)
	list ($total) = $db->fetch("SELECT count(*) from ".$db_table." WHERE ".implode("AND ", $where)." ".$groupby );

// ���ڵ� ��������
	$pg = new Page($_GET[page],$page_num);
	$pg->field = "G.goodsno,G.goodsnm,NT.sno,G.img_s, NT.regdt,NT.phone,NT.name, NT.sended, MB.m_id AS mb_id, MB.name AS mb_name, MB.dormant_regDate AS dormant_regDate";
	$pg->setQuery($db_table,$where,$orderby,$groupby);
	$pg->exec();
	$res = $db->query($pg->query);

// �߼�, �̹߼� �Ǽ�
	$tmp = $db->_select("SELECT sended, COUNT(sended) AS cnt FROM ".GD_GOODS_STOCKED_NOTI." WHERE goodsno = '$goodsno' AND opt1='$opt1' AND opt2='$opt2' GROUP BY sended");
	$overview = array(0 => 0, 1 => 0);
	foreach ($tmp as $v) {
		$overview[ $v['sended'] ] = $v['cnt'];
	}



$spChr = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��');

?>
<script type="text/javascript">
SMS = {
	insSpchr: function(str) {
		var obj = document.getElementById("stockedSMS");
		if (!obj) return;
		obj.value = obj.value + str.replace(/\s/g, "");
		SMS.chkLength();
	},
	chkLength: function() {
		var obj = document.getElementById('stockedSMS');
		var obj2 = document.getElementById('stockedSMSLen');
		var str = obj.value;
		obj2.value = chkByte(str);
		if (chkByte(str)>90) {
			obj2.style.color = "#FF0000";
	//		SMS.chkLength(obj);
		}
		else {
			obj2.style.color = "";
		}
	},
	chkForm: function(fobj) {
		if (!fobj.smsMsg.value) {
			alert("�޼����� �Է��ϼ���.");
			fobj.smsMsg.focus();
			return false;
		}
		if (!fobj.smsCallback.value) {
			alert("�޼����� �Է��ϼ���.");
			fobj.smsCallback.focus();
			return false;
		}
	}
}

function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function fnStockedNotiSend(m) {
<?
	if($overview[0] == 0){
		?>
		alert("�߼��� ������ �����ϴ�");
		return false;
		<?
	}
?>
	var force = false;
	var smsPt = <?=trim($sms->smsPt)?>;
	var totRS = <?=mysql_num_rows($res)?>;
	var sndCT = 0;

	if (m== 'all') {
		sndCT = totRS;
	}
	else if (m == 'selected') {

		var chks = document.getElementsByName('chk[]');
		for (var i=0;i<chks.length ;i++) {
			sndCT += (chks[i].disabled == false && chks[i].checked == true) ? 1 : 0;
		}

		if (sndCT == 0) {
			alert('��û ����Ʈ�� ������ �ּ���.');
			return false;
		}
	}

	if (smsPt < 1) {
		alert('SMS �ܿ� ����Ʈ�� �����մϴ�.');
		return false;
	}
	else if (sndCT > smsPt) {
		if (confirm('SMS �ܿ� ����Ʈ�� �����մϴ�.\n\n(Ȯ���� �����ø� �ܿ� SMS ����Ʈ ��ŭ ���۵˴ϴ�.)')) {
			force = true;
		}
		else {
			return false;
		}
	}

	var f = document.frmStocked;
	f.method.value = m;
	f.force.value = (force) ? '1' : '0';

	f.submit();
}

function viewChange(f){
	var url="<?=$_SERVER['PHP_SELF']?>";
	<?
		$queryString = "";
		foreach($_GET as $k=>$v){
			if($k != "selOption"){
				$queryString[] = $k."=".$v;
			}
		}
		$queryString = implode("&",$queryString);
	?>
	url += "?"+"<?=$queryString?>";
	url += "&selOption="+f.value;
	location.href=url;
}
function viewChangeItems(f){
	var url="<?=$_SERVER['PHP_SELF']?>";
	<?
		$queryString = "";
		foreach($_GET as $k=>$v){
			if($k != "page_num"){
				$queryString[] = $k."=".$v;
			}
		}
		$queryString = implode("&",$queryString);
	?>
	url += "?"+"<?=$queryString?>";
	url += "&page_num="+f.value;
	location.href=url;
}
function showHideOption(){
	var f = document.getElementById("moreOption");
	var f2 = document.getElementById("moreOptionDirection");
	if(f.style.display == "none"){
		f.style.display = "block";
		f2.src= "../img/stocked_btn_close.gif";
	}else{
		f.style.display = "none";
		f2.src= "../img/stocked_btn_open.gif";
	}
}
</script>
<div id="smsSendPage">
<div class="title title_top">��ǰ ���԰� �˸� ��û�� ����Ʈ</div>

<?
	$query = "SELECT img_s, goodsnm, opt1, opt2 FROM ".GD_GOODS." g, ".GD_GOODS_OPTION." go WHERE g.goodsno='".$goodsno."' AND go.optno='".$optno."' AND go.goodsno = g.goodsno and go_is_deleted <> '1'";
	list($img_s, $goodsnm, $opt1DB, $opt2DB) = $db->fetch($query);
	if(!empty($opt2))		$opt = $opt1." / ".$opt2;
	else if(!empty($opt1))	$opt = $opt1;
	else					$opt = "";
?>
<table class="tb">
	<tr>
		<td width="90" align="center"><a href="../../goods/goods_view.php?goodsno=<?=$goodsno?>" target=_blank><?=goodsimg($img_s,80,'',1)?></a></td>
		<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$goodsno?>',825,600)"><font color=0074BA><?=$goodsnm?> &nbsp; &nbsp; &nbsp;<?=$opt?></font></a></td>
	</tr>
</table>

<form name="frmStocked" method="post" action="./indb.stocked_noti.php" target="ifrmHidden">
<input type="hidden" name="goodsno" value="<?=$goodsno?>">
<input type="hidden" name="optno" value="<?=$optno?>">
<input type="hidden" name="opt1" value="<?=$opt1?>">
<input type="hidden" name="opt2" value="<?=$opt2?>">
<input type="hidden" name="method" value="">
<input type="hidden" name="force" value="0">

<table width=100% cellpadding=5 cellspacing=0>
<tr>
	<td width="150">
	�߼� <?=number_format($overview[1])?>�� / �̹߼� <?=number_format($overview[0])?>��
	</td>
	<td>
		<select name="selOption" onchange="viewChange(this)">
			<option value="all" <?=$selected['selOption']['all']?>>��ü</option>
			<option value="sended" <?=$selected['selOption']['sended']?>>�߼�</option>
			<option value="notsended" <?=$selected['selOption']['notsended']?>>�̹߼�</option>
		</select>
	</td>
	<td style="text-align: right">
		<img src="../img/sname_output.gif" align="absmiddle">
		<select name=page_num onchange="viewChangeItems(this)">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=($v == $_GET['page_num']) ? 'selected' : ''?>><?=$v?>�� ���
		<? } ?>
		</select>
	</td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<col width="60">
<col width="50">
<col width="130">
<col width="100">
<col width="50">
<col width="150">
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>��ü����</a></th>
	<th><font class=small1><b>��ȣ</th>
	<th><font class=small1><b>��û�Ͻ�</th>
	<th><font class=small1><b>ȸ��</th>
	<th><font class=small1><b>�߼ۿ���</th>
	<th><font class=small1><b>�޴���</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<?
while ($data=$db->fetch($res,1)) {

	$phone = $formatter->get($data['phone'],'dial','-');
	if($data['mb_id'] && $data['dormant_regDate'] != '0000-00-00 00:00:00'){
		$data['mb_id'] = '�޸�ȸ��';
	}
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[sno]?>" onclick="iciSelect(this)" <?=($phone === false || $data['sended'] == 1) ? 'disabled' : ''?>></td>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><?=$data['regdt']?></td>
	<td align=center><font class=small color=555555><?=$data[name]?><br>(<?=$data[mb_id]?>)</td>
	<td align=center><?=$data['sended'] == 1 ? '�߼�' : '�̹߼�'?></td>
	<td align=center><?=$phone ? $phone : $data['phone'].'<br>(���Ŀ���)'?></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table><br />
<a href="javascript:chkBox(document.getElementsByName('chk[]'),true)"><img src="../img/btn_allselect_s.gif" border="0"></a>
<a href="javascript:chkBox(document.getElementsByName('chk[]'),false)"><img src="../img/btn_alldeselect_s.gif" border="0"></a>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td width=6% style="padding-left:12"></td>
<td width=88% align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
<td width=6%></td>
</tr></table>
<div style="background-image: url(../img/stocked_bg_01.gif); height: 35px; width:100%">
<table width="100%" height="35">
	<tr>
		<td align="center" ><p style="font-weight: bold"><span style="padding-top:10px"><a href="javascript:void(showHideOption())"><img src="../img/stocked_sms.gif"> SMS �߼۳��� �����ϱ�</span> <img src="../img/stocked_btn_open.gif" id="moreOptionDirection" align="absmiddle" /></a></p></td>
	</tr>
</table>
</div>
<div id="moreOption" style="display:none" >
	<table class="tb" borderColor="#e6e6e6" style="width: 100%; border-collapse: collapse;" border="1" cellPadding="5">
		<tr>
			<td>
<!-- SMS �޽��� �Է� �� �߰� -->
<?
$popup = true;
include "_form.stocked_noti_config.php";
?>
			</td>
		</tr>
	</table>
</div>
<!-- SMS �޽��� �Է� �� �߰� -->
<div align="center" style="margin-top: 10px; height:38px; background-color:#F7FBFE; border-top:solid 1px #E0E0E0; border-bottom:solid 1px #E0E0E0;">
	<table width="100%" height="35">
		<tr>
			<td align="center"><strong>�ܿ� SMS����Ʈ: <span style="color:#0070C0"><?=number_format($sms->smsPt)?>��</span></strong>&nbsp;&nbsp;&nbsp;SMS����Ʈ�� ������ ��� �߼۵��� �ʽ��ϴ� <a href="javascript:parent.location.href='../member/sms.pay.php';"><img src="../img/btn_smspoint.gif" align="absmiiddle"></a></td>
		</tr>
	</table>
</div>
<div class=button>
<a href="javascript:void(fnStockedNotiSend('all'));" ><img src="../img/btn_member_sms_01.gif"></a>
<a href="javascript:void(fnStockedNotiSend('selected'));" ><img src="../img/btn_member_sms_02.gif"></a>
</div>

</form>
</div>
<script>table_design_load();</script>
<div id="smsSendingPage" style="display: none">
	<div id="msgInfo" style="width:100%; color:red; padding-top:100px; font-size:20px; text-align: center; font-weight: bold">[�� ����] �޽��� �߼� ���� �� �������� �̵��ϰų�, ���� ������~!!</div>
	<div id="msgTotal"></div>
	<div id="msgIng" style="width:100%;font-size:13px; text-align: center;"></div>
</div>


